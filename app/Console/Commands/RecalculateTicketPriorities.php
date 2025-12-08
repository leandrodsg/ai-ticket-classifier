<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Services\PriorityCalculatorService;
use App\Services\SlaCalculatorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RecalculateTicketPriorities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:recalculate-priorities {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate priorities for tickets that have category and sentiment but no priority';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        // Find tickets that have category and sentiment but no priority
        $ticketsToProcess = Ticket::whereNotNull('category')
            ->whereNotNull('sentiment')
            ->whereNull('priority')
            ->get();

        if ($ticketsToProcess->isEmpty()) {
            $this->info('✅ No tickets found that need priority recalculation');
            return;
        }

        $this->info("📊 Found {$ticketsToProcess->count()} tickets to process");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($ticketsToProcess->count());
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($ticketsToProcess as $ticket) {
            try {
                // Calculate priority using the priority service
                $priorityService = app(PriorityCalculatorService::class);
                $priorityData = $priorityService->calculateFromCategoryAndSentiment(
                    $ticket->category,
                    $ticket->sentiment
                );

                // Calculate SLA due date
                $slaCalculator = app(SlaCalculatorService::class);
                $slaDueAt = $slaCalculator->calculateDueDate(
                    $priorityData['priority'],
                    $ticket->created_at
                );

                if ($dryRun) {
                    $this->line("🔍 Would update Ticket #{$ticket->id}: {$ticket->category}/{$ticket->sentiment} → {$priorityData['priority']}");
                } else {
                    // Update the ticket
                    $ticket->update([
                        'priority' => $priorityData['priority'],
                        'impact_level' => $priorityData['impact_level'],
                        'urgency_level' => $priorityData['urgency_level'],
                        'sla_due_at' => $slaDueAt,
                    ]);

                    Log::info("Priority recalculated for ticket {$ticket->id}", [
                        'old_category' => $ticket->category,
                        'old_sentiment' => $ticket->sentiment,
                        'new_priority' => $priorityData['priority'],
                        'new_impact' => $priorityData['impact_level'],
                        'new_urgency' => $priorityData['urgency_level'],
                        'sla_due_at' => $slaDueAt,
                    ]);
                }

                $successCount++;

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("❌ Failed to process ticket #{$ticket->id}: {$e->getMessage()}");

                Log::error("Failed to recalculate priority for ticket {$ticket->id}", [
                    'error' => $e->getMessage(),
                    'category' => $ticket->category,
                    'sentiment' => $ticket->sentiment,
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info("🔍 DRY RUN COMPLETE");
            $this->info("📊 Would process: {$ticketsToProcess->count()} tickets");
            $this->info("✅ Would succeed: {$successCount} tickets");
            if ($errorCount > 0) {
                $this->warn("❌ Would fail: {$errorCount} tickets");
            }
        } else {
            $this->info("✅ PRIORITY RECALCULATION COMPLETE");
            $this->info("📊 Processed: {$ticketsToProcess->count()} tickets");
            $this->info("✅ Successful: {$successCount} tickets");
            if ($errorCount > 0) {
                $this->warn("❌ Failed: {$errorCount} tickets");
            }

            // Clear dashboard cache to refresh stats
            \Illuminate\Support\Facades\Cache::forget('dashboard.stats');
            $this->info("🗑️  Dashboard cache cleared");
        }

        $this->newLine();
        $this->info("🎯 Next steps:");
        $this->info("   1. Check the dashboard to see updated priority statistics");
        $this->info("   2. Verify SLA calculations are working correctly");
        $this->info("   3. Monitor for any remaining issues");
    }
}
