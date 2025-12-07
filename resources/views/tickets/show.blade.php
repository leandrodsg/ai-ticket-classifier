@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@section('breadcrumbs')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-3 h-3 mr-2.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2A1 1 0 0 0 1 10h2v8a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-4a1 1 0 0 0 1-1h2a1 1 0 0 0 1 1v4a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-8h2a1 1 0 0 0 .707-1.707Z"/>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                    </svg>
                    <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Tickets</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-500">#{{ $ticket->id }}</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Ticket #{{ $ticket->id }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $ticket->title }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('tickets.edit', $ticket) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Ticket
                </a>
                <a href="{{ route('tickets.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Tickets
                </a>
            </div>
        </div>

        <!-- Ticket Details -->
        <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-md border border-gray-200/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Ticket Details</h2>
            </div>

            <div class="px-6 py-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <!-- Title -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket->title }}</dd>
                    </div>

                    <!-- Status -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @include('components.status-badge', ['status' => $ticket->status])
                        </dd>
                    </div>

                    <!-- Category -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1">
                            @include('components.category-badge', ['category' => $ticket->category])
                        </dd>
                    </div>

                    <!-- Sentiment -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sentiment</dt>
                        <dd class="mt-1">
                            @include('components.sentiment-badge', ['sentiment' => $ticket->sentiment])
                        </dd>
                    </div>

                    <!-- Priority -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Priority</dt>
                        <dd class="mt-1">
                            @include('components.priority-badge', ['priority' => $ticket->priority])
                        </dd>
                    </div>

                    <!-- SLA Status -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">SLA Status</dt>
                        <dd class="mt-1">
                            @include('components.sla-indicator', ['ticket' => $ticket])
                        </dd>
                    </div>

                    <!-- Created At -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket->created_at->format('M d, Y \a\t H:i') }}</dd>
                    </div>

                    <!-- Updated At -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $ticket->updated_at->format('M d, Y \a\t H:i') }}</dd>
                    </div>
                </dl>

                <!-- Description -->
                <div class="mt-8">
                    <dt class="text-sm font-medium text-gray-500 mb-2">Description</dt>
                    <dd class="text-sm text-gray-900 bg-gray-50 rounded-lg p-4 whitespace-pre-wrap">{{ $ticket->description }}</dd>
                </div>

                <!-- AI Classification Log -->
                @if($ticket->ai_classification_log)
                    <div class="mt-8">
                        <dt class="text-sm font-medium text-gray-500 mb-2">AI Classification Details</dt>
                        <dd class="text-xs text-gray-600 bg-blue-50 rounded-lg p-4 font-mono">
                            <pre>{{ json_encode($ticket->ai_classification_log, JSON_PRETTY_PRINT) }}</pre>
                        </dd>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('tickets.edit', $ticket) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Ticket
            </a>

            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this ticket? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Ticket
                </button>
            </form>
        </div>
    </div>
@endsection
