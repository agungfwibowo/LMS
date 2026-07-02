<?php

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('guests are redirected from testimonial admin page', function () {
    $this->get(route('testimonials.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit the testimonial admin page', function () {
    $this->actingAs(User::factory()->create());
    $this->get(route('testimonials.index'))->assertOk();
});

test('testimonial list is displayed', function () {
    $this->actingAs(User::factory()->create());
    Testimonial::factory()->create(['name' => 'Budi Santoso']);

    $this->get(route('testimonials.index'))->assertSee('Budi Santoso');
});

test('can create a testimonial with initials', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(App\Livewire\Actions\Testimonial::class)
        ->call('openCreate')
        ->set('name', 'Rina Dewi')
        ->set('role', 'Perawat ICU')
        ->set('quote', 'Pelatihannya sangat membantu.')
        ->set('avatarColor', 'lime')
        ->set('photoSource', 'initials')
        ->call('save');

    $testimonial = Testimonial::where('name', 'Rina Dewi')->first();
    expect($testimonial)->not->toBeNull()
        ->and($testimonial->photo)->toBeNull()
        ->and($testimonial->rating)->toBe(5.0);
});

test('can save testimonial with custom rating', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(App\Livewire\Actions\Testimonial::class)
        ->call('openCreate')
        ->set('name', 'Budi Santoso')
        ->set('role', 'Dokter')
        ->set('quote', 'Bagus sekali.')
        ->set('rating', 4.5)
        ->call('save');

    expect(Testimonial::where('name', 'Budi Santoso')->first()->rating)->toBe(4.5);
});

test('rating is cast to float', function () {
    $testimonial = Testimonial::factory()->create(['rating' => 3.5]);

    expect($testimonial->rating)->toBeFloat()->toBe(3.5);
});

test('can create a testimonial with external photo url', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(App\Livewire\Actions\Testimonial::class)
        ->call('openCreate')
        ->set('name', 'Andi Saputra')
        ->set('role', 'Dokter Spesialis')
        ->set('quote', 'Platform yang sangat informatif.')
        ->set('photoSource', 'external')
        ->set('photoUrl', 'https://example.com/photo.jpg')
        ->call('save');

    $testimonial = Testimonial::where('name', 'Andi Saputra')->first();
    expect($testimonial->photo)->toBe('https://example.com/photo.jpg');
});

test('can create a testimonial with uploaded photo', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $file = UploadedFile::fake()->image('foto.jpg', 200, 200);

    Livewire::test(App\Livewire\Actions\Testimonial::class)
        ->call('openCreate')
        ->set('name', 'Dewi Lestari')
        ->set('role', 'Bidan')
        ->set('quote', 'Sangat bermanfaat.')
        ->set('photoSource', 'upload')
        ->set('uploadedPhoto', $file)
        ->call('save');

    $testimonial = Testimonial::where('name', 'Dewi Lestari')->first();
    expect($testimonial->photo)->not->toBeNull()
        ->and($testimonial->photo)->toStartWith('uploads/testimonials/');
    Storage::disk('public')->assertExists($testimonial->photo);
});

test('deleting a testimonial with uploaded photo removes the file', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $path = 'uploads/testimonials/foto.jpg';
    Storage::disk('public')->put($path, 'fake-content');
    $testimonial = Testimonial::factory()->create(['photo' => $path]);

    Livewire::test(App\Livewire\Actions\Testimonial::class)
        ->call('confirmDelete', $testimonial->id)
        ->call('delete');

    expect(Testimonial::find($testimonial->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('can copy a testimonial with its uploaded photo', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $path = 'uploads/testimonials/foto.jpg';
    Storage::disk('public')->put($path, 'fake-content');
    $testimonial = Testimonial::factory()->create([
        'name' => 'Budi Santoso',
        'photo' => $path,
        'is_active' => true,
    ]);

    Livewire::test(App\Livewire\Actions\Testimonial::class)->call('copy', $testimonial->id);

    $copy = Testimonial::where('name', 'Budi Santoso (Salinan)')->first();
    expect($copy)->not->toBeNull()
        ->and($copy->is_active)->toBeFalse()
        ->and($copy->photo)->not->toBe($path);
    Storage::disk('public')->assertExists($copy->photo);
});

test('can copy a testimonial with an external photo url', function () {
    $this->actingAs(User::factory()->create());
    $testimonial = Testimonial::factory()->create([
        'name' => 'Andi Saputra',
        'photo' => 'https://example.com/photo.jpg',
    ]);

    Livewire::test(App\Livewire\Actions\Testimonial::class)->call('copy', $testimonial->id);

    $copy = Testimonial::where('name', 'Andi Saputra (Salinan)')->first();
    expect($copy->photo)->toBe('https://example.com/photo.jpg');
});

test('can edit an existing testimonial', function () {
    $this->actingAs(User::factory()->create());
    $testimonial = Testimonial::factory()->create(['name' => 'Nama Lama']);

    Livewire::test(App\Livewire\Actions\Testimonial::class)
        ->call('edit', $testimonial->id)
        ->set('name', 'Nama Baru')
        ->call('save');

    expect($testimonial->fresh()->name)->toBe('Nama Baru');
});

test('can toggle testimonial active status', function () {
    $this->actingAs(User::factory()->create());
    $testimonial = Testimonial::factory()->create(['is_active' => true]);

    Livewire::test(App\Livewire\Actions\Testimonial::class)->call('toggleActive', $testimonial->id);

    expect($testimonial->fresh()->is_active)->toBeFalse();
});

test('initials are computed from name', function () {
    $testimonial = Testimonial::factory()->create(['name' => 'Siti Rahmawati']);

    expect($testimonial->initials)->toBe('SR');
});

test('avatar bg class maps correctly', function () {
    $brand = Testimonial::factory()->create(['avatar_color' => 'brand']);
    $lime = Testimonial::factory()->create(['avatar_color' => 'lime']);

    expect($brand->avatar_bg_class)->toBe('bg-brand-50')
        ->and($lime->avatar_bg_class)->toBe('bg-lime-50');
});

test('photo_url returns null when no photo', function () {
    $testimonial = Testimonial::factory()->create(['photo' => null]);

    expect($testimonial->photo_url)->toBeNull();
});

test('photo_url returns external url directly', function () {
    $testimonial = Testimonial::factory()->create(['photo' => 'https://example.com/foto.jpg']);

    expect($testimonial->photo_url)->toBe('https://example.com/foto.jpg');
});

test('photo_url returns storage url for uploaded photo', function () {
    Storage::fake('public');
    $path = 'uploads/testimonials/foto.jpg';
    Storage::disk('public')->put($path, 'fake');
    $testimonial = Testimonial::factory()->create(['photo' => $path]);

    expect($testimonial->photo_url)->toBe(Storage::disk('public')->url($path));
});

test('landing page shows only active testimonials', function () {
    Testimonial::factory()->create(['name' => 'Aktif User', 'is_active' => true]);
    Testimonial::factory()->inactive()->create(['name' => 'Nonaktif User']);

    $this->get(route('home'))
        ->assertSee('Aktif User')
        ->assertDontSee('Nonaktif User');
});
