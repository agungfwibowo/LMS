<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    public function saveAppearance(string $appearance): void
    {
        auth()->user()->update(['appearance' => $appearance]);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Appearance settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <flux:radio.group x-data="{
                current: localStorage.getItem('flux.appearance') || 'system',
                apply(value) {
                    if (value === 'system') {
                        localStorage.removeItem('flux.appearance');
                        document.documentElement.classList.toggle('dark', window.matchMedia('(prefers-color-scheme: dark)').matches);
                    } else {
                        localStorage.setItem('flux.appearance', value);
                        document.documentElement.classList.toggle('dark', value === 'dark');
                    }
                }
            }" variant="segmented" x-model="current"
            x-on:change="apply(current); $wire.saveAppearance(current)">
            <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
        </flux:radio.group>
    </x-pages::settings.layout>
</section>
