<?php

use App\Enums\PelatihanStatus;
use App\Livewire\Actions\Pelatihan as PelatihanComponent;
use App\Models\Pelatihan;
use App\Models\PelatihanCategory;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('starting quick edit loads the pelatihan fields', function () {
    $category = PelatihanCategory::factory()->create();
    $pelatihan = Pelatihan::factory()->create([
        'title' => 'Judul Lama',
        'slug' => 'judul-lama',
        'pelatihan_category_id' => $category->id,
        'status' => PelatihanStatus::Draft,
        'is_active' => false,
    ]);

    Livewire::test(PelatihanComponent::class)
        ->call('startQuickEdit', $pelatihan->id)
        ->assertSet('quickEditId', $pelatihan->id)
        ->assertSet('qeTitle', 'Judul Lama')
        ->assertSet('qeSlug', 'judul-lama')
        ->assertSet('qeCategoryId', $category->id)
        ->assertSet('qeStatus', PelatihanStatus::Draft->value)
        ->assertSet('qeIsActive', false);
});

test('quick edit updates title, slug, category, status, and active', function () {
    $category = PelatihanCategory::factory()->create();
    $pelatihan = Pelatihan::factory()->create([
        'title' => 'Judul Lama',
        'slug' => 'judul-lama',
        'pelatihan_category_id' => null,
        'status' => PelatihanStatus::Draft,
        'is_active' => false,
    ]);

    Livewire::test(PelatihanComponent::class)
        ->call('startQuickEdit', $pelatihan->id)
        ->set('qeTitle', 'Judul Baru')
        ->set('qeSlug', 'judul-baru')
        ->set('qeCategoryId', $category->id)
        ->set('qeStatus', PelatihanStatus::Published->value)
        ->set('qeIsActive', true)
        ->call('saveQuickEdit')
        ->assertSet('quickEditId', null);

    $pelatihan->refresh();
    expect($pelatihan->title)->toBe('Judul Baru')
        ->and($pelatihan->slug)->toBe('judul-baru')
        ->and($pelatihan->pelatihan_category_id)->toBe($category->id)
        ->and($pelatihan->status)->toBe(PelatihanStatus::Published)
        ->and($pelatihan->is_active)->toBeTrue();
});

test('quick edit validates required title and unique slug', function () {
    Pelatihan::factory()->create(['slug' => 'sudah-dipakai']);
    $pelatihan = Pelatihan::factory()->create(['slug' => 'pelatihan-ini']);

    Livewire::test(PelatihanComponent::class)
        ->call('startQuickEdit', $pelatihan->id)
        ->set('qeTitle', '')
        ->set('qeSlug', 'sudah-dipakai')
        ->call('saveQuickEdit')
        ->assertHasErrors(['qeTitle' => 'required', 'qeSlug' => 'unique']);

    expect($pelatihan->fresh()->slug)->toBe('pelatihan-ini');
});

test('cancel quick edit clears state', function () {
    $pelatihan = Pelatihan::factory()->create();

    Livewire::test(PelatihanComponent::class)
        ->call('startQuickEdit', $pelatihan->id)
        ->set('qeTitle', 'Berubah')
        ->call('cancelQuickEdit')
        ->assertSet('quickEditId', null)
        ->assertSet('qeTitle', '');
});
