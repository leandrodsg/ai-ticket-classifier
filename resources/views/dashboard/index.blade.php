<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart Support Classifier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Topbar -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <h1 class="text-2xl font-semibold text-gray-800">Smart Support Classifier</h1>
                </div>
                <div class="text-sm text-gray-600">
                    Dashboard
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Tickets Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Tickets</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalTickets }}</p>
                    </div>
                </div>
            </div>

            <!-- Open Tickets Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Open Tickets</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ $ticketsByStatus->where('status', 'open')->first()?->count ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pending Tickets Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Tickets</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ $ticketsByStatus->where('status', 'pending')->first()?->count ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Closed Tickets Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Closed Tickets</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ $ticketsByStatus->where('status', 'closed')->first()?->count ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Details Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Categories Donut Chart -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tickets by Category</h3>
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

            <!-- Sentiment Analysis Placeholder -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Sentiment Analysis</h3>
                <div class="space-y-3">
                    @foreach($ticketsBySentiment as $item)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm text-gray-600 capitalize">{{ $item->sentiment }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $item->count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $totalTickets > 0 ? ($item->count / $totalTickets) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($ticketsBySentiment->isEmpty())
                        <p class="text-sm text-gray-500">No sentiment data yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
</body>
</html>
