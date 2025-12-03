@php
    $sentiment = $sentiment ?? '';
    $configs = [
        'positive' => [
            'color' => 'bg-green-100 text-green-800',
            'icon' => '😊',
            'text' => 'Positive'
        ],
        'negative' => [
            'color' => 'bg-red-100 text-red-800',
            'icon' => '😞',
            'text' => 'Negative'
        ],
        'neutral' => [
            'color' => 'bg-gray-100 text-gray-800',
            'icon' => '😐',
            'text' => 'Neutral'
        ],
    ];
    $config = $configs[$sentiment] ?? [
        'color' => 'bg-gray-100 text-gray-500',
        'icon' => '❓',
        'text' => 'Unknown'
    ];
@endphp

@if($sentiment)
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['color'] }}">
        <span class="mr-1">{{ $config['icon'] }}</span>
        {{ $config['text'] }}
    </span>
@else
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
        <span class="mr-1">❓</span>
        No Sentiment
    </span>
@endif
