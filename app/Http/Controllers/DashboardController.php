<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
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
        // Optimized queries using selectRaw for better performance
        $totalTickets = Ticket::count();

        $ticketsByCategory = Ticket::selectRaw('category, COUNT(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->get();

        $ticketsBySentiment = Ticket::selectRaw('sentiment, COUNT(*) as count')
            ->whereNotNull('sentiment')
            ->groupBy('sentiment')
            ->get();

        $ticketsByStatus = Ticket::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('dashboard.index', compact(
            'totalTickets',
            'ticketsByCategory',
            'ticketsBySentiment',
            'ticketsByStatus'
        ));
    }
}
