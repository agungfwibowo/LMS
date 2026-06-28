{{-- Logo SIPAHAM — dipakai di sidebar & header admin --}}
<a {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-sm">
        <img src="{{ asset('logo.png') }}" alt="SIPAHAM" class="bg-white p-0.5 rounded-sm">
    </span>
    <span class="flex flex-col leading-none">
        <span class="text-base font-bold tracking-tight text-zinc-900 dark:text-white">SIPAHAM</span>
        <span class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400">RS Adam Malik</span>
    </span>
</a>
