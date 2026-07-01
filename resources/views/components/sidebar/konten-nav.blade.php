<x-sidebar.nav-group
    :heading="__('Konten Landing')"
    icon="layout-grid"
    :routes="['faqs.*', 'testimonials.*']"
    :items="[
        ['icon' => 'chat-bubble-bottom-center-text', 'route' => 'faqs.index',         'current' => 'faqs.*',         'label' => 'FAQ'],
        ['icon' => 'star',                           'route' => 'testimonials.index',  'current' => 'testimonials.*', 'label' => 'Testimoni'],
    ]"
/>
