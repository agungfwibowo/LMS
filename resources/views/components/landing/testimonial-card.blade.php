@props(['t'])

<x-ui.card {{ $attributes->class('flex flex-col') }}>
    @if ($t->rating > 0)
        <x-star-rating :rating="$t->rating" size="md" :show-value="true" class="mb-3" />
    @else
        <div class="mb-2 font-heading text-[30px] font-extrabold leading-none text-lime">&ldquo;</div>
    @endif

    <p class="mb-6 flex-1 text-[15.5px] leading-[1.65] text-zinc-700 dark:text-zinc-300">{{ $t->quote }}</p>

    <div class="flex items-center gap-3">
        @if ($t->photo_url)
            <img src="{{ $t->photo_url }}" class="size-11 rounded-full object-cover" alt="{{ $t->name }}">
        @else
            <div class="{{ $t->avatar_bg_class }} flex size-11 items-center justify-center rounded-full font-heading text-sm font-bold text-brand-900 dark:text-teal-300">
                {{ $t->initials }}
            </div>
        @endif

        <div>
            <div class="text-[14.5px] font-bold text-brand-950 dark:text-white">{{ $t->name }}</div>
            <div class="text-[13px] text-zinc-500 dark:text-zinc-400">{{ $t->role }}</div>
        </div>
    </div>
</x-ui.card>
