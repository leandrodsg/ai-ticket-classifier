@php
    $status = $status ?? '';
    $configs = [
        'open' => [
            'color' => 'bg-yellow-100 text-yellow-800',
            'icon' => '🟡',
            'text' => 'Open'
        ],
        'pending' => [
            'color' => 'bg-blue-100 text-blue-800',
            'icon' => '⏳',
            'text' => 'Pending'
        ],
        'closed' => [
            'color' => 'bg-gray-100 text-gray-800',
            'icon' => '✅',
            'text' => 'Closed'
        ],
    ];
    $config = $configs[$status] ?? [
        'color' => 'bg-gray-100 text-gray-500',
        'icon' => '❓',
        'text' => 'Unknown'
    ];
@endphp

@if($status)
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['color'] }}">
        <span class="mr-1">{{ $config['icon'] }}</span>
        {{ $config['text'] }}
    </span>
@else
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
        <span class="mr-1">❓</span>
        No Status
    </span>
@endif
