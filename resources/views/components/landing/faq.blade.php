@props(['id', 'question'])

<x-landing.faq-item :id="$id" :question="$question" {{ $attributes }}>
    {{ $slot }}
</x-landing.faq-item>
