<x-sidebar.nav-group
    :heading="__('Pelatihan')"
    icon="academic-cap"
    :routes="['pelatihan.*', 'pelatihan-categories.*']"
    :items="[
        ['icon' => 'academic-cap',  'route' => 'pelatihan.index',            'current' => 'pelatihan.index',        'label' => 'Semua Pelatihan'],
        ['icon' => 'plus-circle',   'route' => 'pelatihan.create',           'current' => 'pelatihan.create',       'label' => 'Tambah Pelatihan'],
        ['icon' => 'tag',           'route' => 'pelatihan-categories.index', 'current' => 'pelatihan-categories.*', 'label' => 'Kategori'],
    ]"
/>
