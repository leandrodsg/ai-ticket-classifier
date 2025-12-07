@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <span class="inline-flex items-center text-sm font-medium text-blue-600">
                    <svg class="w-3 h-3 mr-2.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2A1 1 0 0 0 1 10h2v8a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-4a1 1 0 0 0 1-1h2a1 1 0 0 0 1 1v4a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-8h2a1 1 0 0 0 .707-1.707Z"/>
                    </svg>
                    Dashboard
                </span>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Tickets Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md hover:shadow-lg border border-gray-200/50 p-6 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-600">Total Tickets</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalTickets }}</p>
                    </div>
                </div>
            </div>

            <!-- Critical Priority Tickets Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md hover:shadow-lg border border-gray-200/50 p-6 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-600">Critical Priority</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $criticalTickets ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- SLA Compliance Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md hover:shadow-lg border border-gray-200/50 p-6 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-600">SLA Compliance</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $slaCompliancePercentage ?? 0 }}%</p>
                        <p class="text-xs text-gray-500">{{ $slaOnTimeTickets ?? 0 }}/{{ $totalTicketsWithSLA ?? 0 }} on time</p>
                    </div>
                </div>
            </div>

            <!-- SLA Breached Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md hover:shadow-lg border border-gray-200/50 p-6 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-600">SLA Breached</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $slaBreachedTickets ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Require attention</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Details Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Categories Donut Chart -->
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/50 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Tickets by Category</h3>
                <div class="relative">
                    <canvas id="categoriesChart" width="300" height="300"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($ticketsByCategory as $index => $item)
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'][$index % 5] }}"></div>
                                <span class="text-sm text-gray-600 capitalize">{{ $item->category }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $item->count }}</span>
                        </div>
                    @endforeach
                    @if($ticketsByCategory->isEmpty())
                        <p class="text-sm text-gray-500">No categorized tickets yet</p>
                    @endif
                </div>
            </div>

            <!-- Priority Distribution -->
            <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/50 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Priority Distribution</h3>
                <div class="space-y-4">
                    @foreach($ticketsByPriority ?? [] as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                @if($item->priority === 'critical')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        🚨 Critical
                                    </span>
                                @elseif($item->priority === 'high')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        ⚠️ High
                                    </span>
                                @elseif($item->priority === 'medium')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        📋 Medium
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✅ Low
                                    </span>
                                @endif
                                <span class="text-sm font-medium text-gray-900">{{ $item->count }}</span>
                            </div>
                            <div class="flex-1 ml-4">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @if($item->priority === 'critical')
                                        <div class="bg-red-500 h-2 rounded-full transition-all duration-300" style="width: {{ $totalTickets > 0 ? ($item->count / $totalTickets) * 100 : 0 }}%"></div>
                                    @elseif($item->priority === 'high')
                                        <div class="bg-orange-500 h-2 rounded-full transition-all duration-300" style="width: {{ $totalTickets > 0 ? ($item->count / $totalTickets) * 100 : 0 }}%"></div>
                                    @elseif($item->priority === 'medium')
                                        <div class="bg-yellow-500 h-2 rounded-full transition-all duration-300" style="width: {{ $totalTickets > 0 ? ($item->count / $totalTickets) * 100 : 0 }}%"></div>
                                    @else
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $totalTickets > 0 ? ($item->count / $totalTickets) * 100 : 0 }}%"></div>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $totalTickets > 0 ? round(($item->count / $totalTickets) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if(empty($ticketsByPriority))
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500">No priority data yet</p>
                            <p class="text-xs text-gray-400 mt-1">Priorities will be calculated automatically</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Priority Alerts Section -->
        <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/50 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Priority Alerts</h3>
            <div class="space-y-4">
                @forelse($recentAlerts ?? [] as $alert)
                    <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center space-x-3">
                            @if($alert->priority === 'critical')
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('tickets.show', $alert) }}" class="hover:text-blue-600">
                                        Ticket #{{ $alert->id }}: {{ Str::limit($alert->title, 50) }}
                                    </a>
                                </h4>
                                <div class="flex items-center space-x-2 mt-1">
                                    @include('components.priority-badge', ['priority' => $alert->priority])
                                    @include('components.sla-indicator', ['ticket' => $alert])
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $alert->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">No priority alerts</p>
                        <p class="text-xs text-gray-400 mt-1">All tickets are within acceptable parameters</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize Chart.js donut chart for categories
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('categoriesChart');
            if (ctx) {
                const categoriesData = @json($ticketsByCategory);
                const labels = categoriesData.map(item => item.category.charAt(0).toUpperCase() + item.category.slice(1));
                const data = categoriesData.map(item => item.count);
                const colors = ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'];

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors.slice(0, data.length),
                            borderWidth: 0,
                            hoverBorderWidth: 2,
                            hoverBorderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.parsed + ' tickets';
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
@endsection
