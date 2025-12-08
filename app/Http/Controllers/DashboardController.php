<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with ticket statistics and analytics.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        try {
            // Redis cache for 5 minutes to improve performance
            $cacheKey = 'dashboard.stats';

            $stats = Cache::remember($cacheKey, 300, function () {
                Log::info('Dashboard cache miss - fetching from database');

                $basicStats = [
                    'totalTickets' => Ticket::count(),
                    'ticketsByCategory' => Ticket::selectRaw('category, COUNT(*) as count')
                        ->whereNotNull('category')
                        ->groupBy('category')
                        ->get(),
                    'ticketsBySentiment' => Ticket::selectRaw('sentiment, COUNT(*) as count')
                        ->whereNotNull('sentiment')
                        ->groupBy('sentiment')
                        ->get(),
                    'ticketsByStatus' => Ticket::selectRaw('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->get(),
                ];

                $priorityData = Ticket::selectRaw('priority, COUNT(*) as count')
                    ->whereNotNull('priority')
                    ->groupBy('priority')
                    ->get()
                    ->keyBy('priority');

                $allPriorities = ['critical', 'high', 'medium', 'low'];
                $ticketsByPriority = collect();
                foreach ($allPriorities as $priority) {
                    $ticketsByPriority->push((object)[
                        'priority' => $priority,
                        'count' => $priorityData->has($priority) ? $priorityData->get($priority)->count : 0
                    ]);
                }

                $priorityStats = [
                    'ticketsByPriority' => $ticketsByPriority,
                    'criticalTickets' => Ticket::critical()->count(),
                    'slaOverdueTickets' => Ticket::slaOverdue()->count(),
                ];

                $slaStats = [
                    'totalTicketsWithSLA' => Ticket::whereNotNull('sla_due_at')->count(),
                    'slaBreachedTickets' => Ticket::whereNotNull('sla_due_at')
                        ->where('sla_due_at', '<', now())
                        ->where('status', '!=', 'closed')
                        ->count(),
                    'slaOnTimeTickets' => Ticket::whereNotNull('sla_due_at')
                        ->where(function ($query) {
                            $query->where('sla_due_at', '>=', now())
                                  ->orWhere('status', 'closed');
                        })
                        ->count(),
                ];

                $totalWithSLA = $slaStats['totalTicketsWithSLA'];
                $slaStats['slaCompliancePercentage'] = $totalWithSLA > 0
                    ? round((($totalWithSLA - $slaStats['slaBreachedTickets']) / $totalWithSLA) * 100, 1)
                    : 100;

                $recentAlerts = Ticket::with(['aiLogs' => function ($query) {
                    $query->latest()->limit(1);
                }])
                ->where(function ($query) {
                    $query->where('priority', 'critical')
                          ->orWhere('priority', 'high');
                })
                ->where('status', '!=', 'closed')
                ->latest()
                ->limit(10)
                ->get();

                return array_merge($basicStats, $priorityStats, $slaStats, [
                    'recentAlerts' => $recentAlerts,
                ]);
            });

            Log::info('Dashboard loaded successfully');

            // Return JSON for API requests, view for web requests
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json($stats);
            }

            return view('dashboard.index', $stats);

        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback with basic data if cache/database fails
            $fallbackData = [
                'totalTickets' => 0,
                'criticalTickets' => 0,
                'slaCompliancePercentage' => 100,
                'totalTicketsWithSLA' => 0,
                'slaOnTimeTickets' => 0,
                'slaBreachedTickets' => 0,
                'ticketsByCategory' => collect(),
                'ticketsBySentiment' => collect(),
                'ticketsByStatus' => collect(),
                'ticketsByPriority' => collect([
                    (object)['priority' => 'critical', 'count' => 0],
                    (object)['priority' => 'high', 'count' => 0],
                    (object)['priority' => 'medium', 'count' => 0],
                    (object)['priority' => 'low', 'count' => 0],
                ]),
                'recentAlerts' => collect(),
            ];

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json($fallbackData, 500);
            }

            return view('dashboard.index', $fallbackData);
        }
    }
}
