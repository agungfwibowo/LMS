<?php

use App\Enums\PelatihanCategoryIcon;
use App\Models\PelatihanCategory;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected from pelatihan category admin page', function () {
    $this->get(route('pelatihan-categories.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit the pelatihan category admin page', function () {
    $this->actingAs(User::factory()->create());
    $this->get(route('pelatihan-categories.index'))->assertOk();
});

test('pelatihan category list is displayed', function () {
    $this->actingAs(User::factory()->create());
    PelatihanCategory::factory()->create(['name' => 'Manajemen RS']);

    $this->get(route('pelatihan-categories.index'))->assertSee('Manajemen RS');
});

test('slug is auto generated from name', function () {
    $this->actingAs(User::factory()->create());

    $slug = Livewire::test(App\Livewire\Actions\PelatihanCategory::class)
        ->call('openCreate')
        ->set('name', 'Pelatihan Dasar')
        ->get('slug');

    expect($slug)->toBe('pelatihan-dasar');
});

test('can create a pelatihan category', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(App\Livewire\Actions\PelatihanCategory::class)
        ->call('openCreate')
        ->set('name', 'Keperawatan')
        ->set('slug', 'keperawatan')
        ->set('description', 'Kategori keperawatan')
        ->set('icon', 'heart')
        ->call('save');

    $category = PelatihanCategory::where('slug', 'keperawatan')->first();
    expect($category)->not->toBeNull()
        ->and($category->icon)->toBe(PelatihanCategoryIcon::Heart);
});

test('category defaults to academic cap icon when creating', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(App\Livewire\Actions\PelatihanCategory::class)
        ->call('openCreate')
        ->assertSet('icon', 'academic-cap');
});

test('category icon must be a valid option', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(App\Livewire\Actions\PelatihanCategory::class)
        ->call('openCreate')
        ->set('name', 'Kategori Aneh')
        ->set('slug', 'kategori-aneh')
        ->set('icon', 'not-a-real-icon')
        ->call('save')
        ->assertHasErrors(['icon']);
});

test('editing a category loads its saved icon', function () {
    $this->actingAs(User::factory()->create());
    $category = PelatihanCategory::factory()->create(['icon' => 'beaker']);

    Livewire::test(App\Livewire\Actions\PelatihanCategory::class)
        ->call('edit', $category->id)
        ->assertSet('icon', 'beaker');
});

test('slug must be unique', function () {
    $this->actingAs(User::factory()->create());
    PelatihanCategory::factory()->create(['slug' => 'keperawatan']);

    Livewire::test(App\Livewire\Actions\PelatihanCategory::class)
        ->call('openCreate')
        ->set('name', 'Keperawatan Lain')
        ->set('slug', 'keperawatan')
        ->call('save')
        ->assertHasErrors(['slug' => 'unique']);
});

test('can edit a pelatihan category', function () {
    $this->actingAs(User::factory()->create());
    $category = PelatihanCategory::factory()->create(['name' => 'Nama Lama']);

    Livewire::test(App\Livewire\Actions\PelatihanCategory::class)
        ->call('edit', $category->id)
        ->set('name', 'Nama Baru')
        ->call('save');

    expect($category->fresh()->name)->toBe('Nama Baru');
});

test('can copy a pelatihan category with a unique slug', function () {
    $this->actingAs(User::factory()->create());
    $category = PelatihanCategory::factory()->create([
        'name' => 'Keperawatan',
        'slug' => 'keperawatan',
        'icon' => 'heart',
    ]);

    Livewire::test(App\Livewire\Actions\PelatihanCategory::class)->call('copy', $category->id);

    $copy = PelatihanCategory::where('slug', 'keperawatan-salinan')->first();
    expect($copy)->not->toBeNull()
        ->and($copy->name)->toBe('Keperawatan (Salinan)')
        ->and($copy->icon)->toBe(PelatihanCategoryIcon::Heart);
});

test('copying a pelatihan category twice generates unique slugs', function () {
    $this->actingAs(User::factory()->create());
    $category = PelatihanCategory::factory()->create(['slug' => 'keperawatan']);

    Livewire::test(App\Livewire\Actions\PelatihanCategory::class)
        ->call('copy', $category->id)
        ->call('copy', $category->id);

    expect(PelatihanCategory::where('slug', 'keperawatan-salinan')->exists())->toBeTrue()
        ->and(PelatihanCategory::where('slug', 'keperawatan-salinan-2')->exists())->toBeTrue();
});

test('can delete a pelatihan category', function () {
    $this->actingAs(User::factory()->create());
    $category = PelatihanCategory::factory()->create();

    Livewire::test(App\Livewire\Actions\PelatihanCategory::class)
        ->call('confirmDelete', $category->id)
        ->call('delete');

    expect(PelatihanCategory::find($category->id))->toBeNull();
});
