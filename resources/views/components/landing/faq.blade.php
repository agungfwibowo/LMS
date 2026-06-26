@props(['id', 'question'])

<x-landing.faq-item :id="$id" :question="$question">
    {{ $slot }}
</x-landing.faq-item>
