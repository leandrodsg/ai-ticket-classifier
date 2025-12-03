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
    public function index(Request $request): View
    {
        try {
            // Redis cache for 5 minutes to improve performance
            $cacheKey = 'dashboard.stats';

            $stats = Cache::remember($cacheKey, 300, function () {
                Log::info('Dashboard cache miss - fetching from database');

                return [
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
            });

            Log::info('Dashboard cache hit', ['cache_key' => $cacheKey]);

            return view('dashboard.index', $stats);

        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback with basic data if cache/database fails
            return view('dashboard.index', [
                'totalTickets' => 0,
                'ticketsByCategory' => collect(),
                'ticketsBySentiment' => collect(),
                'ticketsByStatus' => collect(),
            ]);
        }
    }
}
