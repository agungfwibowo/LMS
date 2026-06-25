@props([
    'value' => '',
    'label' => '',
    'icon' => null,
])

<div class="flex flex-col items-center text-center">
    @if ($icon)
        <span class="mb-3 flex size-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
            <flux:icon name="{{ $icon }}" class="size-6" />
        </span>
    @endif
    <dd class="text-3xl font-bold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">{{ $value }}</dd>
    <dt class="mt-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $label }}</dt>
</div>
