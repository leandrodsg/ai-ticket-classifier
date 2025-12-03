<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DebugDashboardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:debug {--performance : Show performance metrics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug dashboard queries and performance metrics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Dashboard Debug Information');
        $this->line('================================');

        // Basic stats
        $totalTickets = Ticket::count();
        $this->info("📊 Total Tickets: {$totalTickets}");

        // Cache information
        $cacheKey = 'dashboard.stats';
        $cacheExists = Cache::has($cacheKey);
        $this->info("💾 Cache Status: " . ($cacheExists ? '✅ Active' : '❌ Empty'));

        if ($cacheExists) {
            $cacheData = Cache::get($cacheKey);
            $this->info("📈 Cached Categories: " . count($cacheData['ticketsByCategory'] ?? []));
            $this->info("😊 Cached Sentiments: " . count($cacheData['ticketsBySentiment'] ?? []));
            $this->info("📋 Cached Status: " . count($cacheData['ticketsByStatus'] ?? []));
        }

        // Performance metrics
        if ($this->option('performance')) {
            $this->line('');
            $this->info('⚡ Performance Metrics');
            $this->line('----------------------');

            // Query performance
            $startTime = microtime(true);
            $categories = Ticket::selectRaw('category, COUNT(*) as count')
                ->whereNotNull('category')
                ->groupBy('category')
                ->get();
            $queryTime = (microtime(true) - $startTime) * 1000;

            $this->info("🔍 Categories Query: {$queryTime}ms");

            $startTime = microtime(true);
            $sentiments = Ticket::selectRaw('sentiment, COUNT(*) as count')
                ->whereNotNull('sentiment')
                ->groupBy('sentiment')
                ->get();
            $queryTime = (microtime(true) - $startTime) * 1000;

            $this->info("😊 Sentiments Query: {$queryTime}ms");

            $startTime = microtime(true);
            $statuses = Ticket::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();
            $queryTime = (microtime(true) - $startTime) * 1000;

            $this->info("📋 Status Query: {$queryTime}ms");

            // Memory usage
            $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;
            $this->info("🧠 Peak Memory: " . round($memoryUsage, 2) . " MB");
        }

        // Recommendations
        $this->line('');
        $this->info('💡 Recommendations');
        $this->line('-----------------');

        if (!$cacheExists) {
            $this->warn('⚠️  Cache is empty - first request will be slow');
        }

        if ($totalTickets === 0) {
            $this->warn('⚠️  No tickets found - dashboard will show empty state');
        }

        $this->info('✅ Dashboard debug complete');
    }
}
