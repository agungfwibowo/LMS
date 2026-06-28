@props([
    'heading',
    'icon' => 'bars-3',
    'routes' => [],
    'items' => [],
])

<flux:sidebar.group
    expandable
    :icon="$icon"
    :heading="$heading"
    :expanded="$routes && request()->routeIs(...$routes)"
    class="grid"
>
    @foreach ($items as $item)
        <flux:sidebar.item
            :icon="$item['icon'] ?? null"
            :href="route($item['route'])"
            :current="request()->routeIs($item['current'] ?? $item['route'])"
            wire:navigate
        >
            {{ __($item['label']) }}
        </flux:sidebar.item>
    @endforeach

    {{ $slot }}
</flux:sidebar.group>
