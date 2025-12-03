@php
    $category = $category ?? '';
    $colors = [
        'technical' => 'bg-blue-100 text-blue-800',
        'commercial' => 'bg-green-100 text-green-800',
        'billing' => 'bg-purple-100 text-purple-800',
        'general' => 'bg-gray-100 text-gray-800',
    ];
    $colorClass = $colors[$category] ?? 'bg-gray-100 text-gray-800';
    $displayText = ucfirst($category ?: 'Unknown');
@endphp

@if($category)
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
        {{ $displayText }}
    </span>
@else
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
        No Category
    </span>
@endif
