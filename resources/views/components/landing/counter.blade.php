@props([
    'class',
    'value',
    'animate' => true,
    'duration' => 1200,
])

<div
    @if($animate)
        x-data="counter({{ $value }}, {{ $duration }})"
        x-intersect.once="start()"
    @endif
    {{ $attributes->class(['inline']) }}
    >
    @if($animate)
        <span x-text="current"></span>
    @else
        {{ $value }}
    @endif
</div>