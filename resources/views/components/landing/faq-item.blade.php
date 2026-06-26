@props(['id', 'question'])

<div x-data="{ id: {{ $id }} }" class="border-b border-zinc-200 dark:border-zinc-800">
    <button type="button" @click="active = id"
            class="flex w-full items-center justify-between gap-4 py-5 text-left"
            :aria-expanded="active === id">
        <span class="text-base font-semibold text-zinc-900 dark:text-white">{{ $question }}</span>
        <flux:icon name="chevron-down" class="size-5 shrink-0 text-brand-600 transition-transform duration-200 dark:text-brand-400" ::class="active === id && 'rotate-180'" />
    </button>
    <div x-show="active === id" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0">
        <p class="pb-5 pr-8 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
            {{ $slot }}
        </p>
    </div>
</div>
