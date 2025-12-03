<?php

namespace App\Http\Controllers;

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
        // Implementation will be added in next commits
        return view('dashboard.index');
    }
}
