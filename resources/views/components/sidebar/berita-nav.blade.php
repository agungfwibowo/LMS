<x-sidebar.nav-group
    :heading="__('Berita')"
    icon="newspaper"
    :routes="['posts.*', 'categories.*', 'tags.*']"
    :items="[
        ['icon' => 'newspaper', 'route' => 'posts.index',      'current' => 'posts.index',   'label' => 'Semua Berita'],
        ['icon' => 'plus-circle','route' => 'posts.create',    'current' => 'posts.create',  'label' => 'Tambah Berita'],
        ['icon' => 'tag',        'route' => 'categories.index','current' => 'categories.*',  'label' => 'Kategori'],
        ['icon' => 'hashtag',    'route' => 'tags.index',      'current' => 'tags.*',        'label' => 'Tags'],
    ]"
/>
