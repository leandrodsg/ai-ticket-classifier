<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Heroicons CDN -->
    <script src="https://unpkg.com/heroicons@2.1.1/24/outline/index.js" type="module"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-gradient-to-br from-gray-50 via-blue-50 to-gray-100 text-gray-900">
    <!-- Topbar -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="hidden sm:block">
                                <h1 class="text-lg font-semibold text-gray-900">Smart Support</h1>
                                <p class="text-xs text-gray-500">AI Classifier</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}"
                       class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('tickets.index') }}"
                       class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ request()->routeIs('tickets.*') ? 'text-blue-600 bg-blue-50' : '' }}">
                        Tickets
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button"
                            class="text-gray-700 hover:text-blue-600 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-expanded="false">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div class="md:hidden hidden bg-white border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('dashboard') }}"
                   class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:text-blue-600' }}">
                    Dashboard
                </a>
                <a href="{{ route('tickets.index') }}"
                   class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tickets.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:text-blue-600' }}">
                    Tickets
                </a>
            </div>
        </div>
    </nav>

    <!-- Breadcrumbs -->
    @hasSection('breadcrumbs')
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                @yield('breadcrumbs')
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-500 mb-4 md:mb-0">
                    © {{ date('Y') }} Smart Support Classifier. Built with Laravel & Tailwind CSS.
                </div>
                <div class="flex space-x-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-blue-600 transition-colors duration-200">Privacy</a>
                    <a href="#" class="hover:text-blue-600 transition-colors duration-200">Terms</a>
                    <a href="#" class="hover:text-blue-600 transition-colors duration-200">Support</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Flash Messages -->
    @if (session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 max-w-sm z-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 max-w-sm z-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
