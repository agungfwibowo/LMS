<?php

use App\Enums\PelatihanStatus;
use App\Livewire\Actions\Pelatihan as PelatihanComponent;
use App\Livewire\Actions\PelatihanForm;
use App\Models\Pelatihan;
use App\Models\PelatihanCategory;
use App\Models\PelatihanModule;
use App\Models\PelatihanModuleVideo;
use App\Models\PelatihanQuestion;
use App\Models\PelatihanQuestionOption;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('guests are redirected from pelatihan admin page', function () {
    $this->get(route('pelatihan.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit the pelatihan admin page', function () {
    $this->actingAs(User::factory()->create());
    $this->get(route('pelatihan.index'))->assertOk();
});

test('pelatihan list is displayed', function () {
    $this->actingAs(User::factory()->create());
    Pelatihan::factory()->create(['title' => 'Pelatihan BHD']);

    $this->get(route('pelatihan.index'))->assertSee('Pelatihan BHD');
});

test('pelatihan list shows module count and names in tooltip', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    PelatihanModule::factory()->create(['pelatihan_id' => $pelatihan->id, 'title' => 'Modul Dasar', 'urutan' => 0]);
    PelatihanModule::factory()->create(['pelatihan_id' => $pelatihan->id, 'title' => 'Modul Lanjutan', 'urutan' => 1]);

    $this->get(route('pelatihan.index'))
        ->assertSee('Modul Dasar, Modul Lanjutan', escape: false)
        ->assertSeeText('2');
});

test('pelatihan without modules shows a zero count', function () {
    $this->actingAs(User::factory()->create());
    Pelatihan::factory()->create();

    $this->get(route('pelatihan.index'))->assertSeeText('0');
});

test('can toggle pelatihan active status', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create(['is_active' => true]);

    Livewire::test(PelatihanComponent::class)->call('toggleActive', $pelatihan->id);

    expect($pelatihan->fresh()->is_active)->toBeFalse();
});

test('deleting a pelatihan with thumbnail removes the file', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $path = 'uploads/pelatihan/poster.jpg';
    Storage::disk('public')->put($path, 'fake-content');
    $pelatihan = Pelatihan::factory()->create(['thumbnail' => $path]);

    Livewire::test(PelatihanComponent::class)
        ->call('confirmDelete', $pelatihan->id)
        ->call('delete');

    expect(Pelatihan::find($pelatihan->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('guests are redirected from the pelatihan create page', function () {
    $this->get(route('pelatihan.create'))->assertRedirect(route('login'));
});

test('authenticated users can visit the pelatihan create page', function () {
    $this->actingAs(User::factory()->create());
    $this->get(route('pelatihan.create'))->assertOk();
});

test('authenticated users can visit the pelatihan edit page', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();

    $this->get(route('pelatihan.edit', $pelatihan))->assertOk();
});

test('slug is auto generated from title on create', function () {
    $this->actingAs(User::factory()->create());

    $slug = Livewire::test(PelatihanForm::class)
        ->set('title', 'Pelatihan Dasar Kegawatan')
        ->get('slug');

    expect($slug)->toBe('pelatihan-dasar-kegawatan');
});

test('can create a pelatihan and redirects to its edit page', function () {
    $this->actingAs(User::factory()->create());
    $category = PelatihanCategory::factory()->create();

    Livewire::test(PelatihanForm::class)
        ->set('title', 'Pelatihan BHD')
        ->set('slug', 'pelatihan-bhd')
        ->set('pelatihanCategoryId', $category->id)
        ->set('description', 'Materi bantuan hidup dasar')
        ->set('status', 'published')
        ->set('mode', 'offline')
        ->set('location', 'Aula RS')
        ->set('instructor', 'dr. Andi')
        ->set('quota', 40)
        ->set('price', 0)
        ->call('save')
        ->assertHasNoErrors();

    $pelatihan = Pelatihan::where('slug', 'pelatihan-bhd')->first();
    expect($pelatihan)->not->toBeNull()
        ->and($pelatihan->pelatihan_category_id)->toBe($category->id)
        ->and($pelatihan->status)->toBe(PelatihanStatus::Published)
        ->and($pelatihan->quota)->toBe(40);
});

test('slug must be unique', function () {
    $this->actingAs(User::factory()->create());
    Pelatihan::factory()->create(['slug' => 'pelatihan-bhd']);

    Livewire::test(PelatihanForm::class)
        ->set('title', 'Pelatihan BHD Lanjut')
        ->set('slug', 'pelatihan-bhd')
        ->call('save')
        ->assertHasErrors(['slug' => 'unique']);
});

test('end date must be after or equal to start date', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(PelatihanForm::class)
        ->set('title', 'Pelatihan Test')
        ->set('slug', 'pelatihan-test')
        ->set('startDate', '2026-08-10T09:00')
        ->set('endDate', '2026-08-09T09:00')
        ->call('save')
        ->assertHasErrors(['endDate']);
});

test('can create a pelatihan with uploaded thumbnail', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $file = UploadedFile::fake()->image('poster.jpg', 400, 300);

    Livewire::test(PelatihanForm::class)
        ->set('title', 'Pelatihan Thumbnail')
        ->set('slug', 'pelatihan-thumbnail')
        ->set('uploadedThumbnail', $file)
        ->call('save')
        ->assertHasNoErrors();

    $pelatihan = Pelatihan::where('slug', 'pelatihan-thumbnail')->first();
    expect($pelatihan->thumbnail)->toStartWith('uploads/pelatihan/');
    Storage::disk('public')->assertExists($pelatihan->thumbnail);
});

test('can edit an existing pelatihan', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create(['title' => 'Judul Lama']);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->set('title', 'Judul Baru')
        ->call('save')
        ->assertHasNoErrors();

    expect($pelatihan->fresh()->title)->toBe('Judul Baru');
});

test('the question panel is empty until the pelatihan is created', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(PelatihanForm::class)
        ->assertSet('pelatihan', null);
});

test('deleting a category nullifies its pelatihan reference', function () {
    $category = PelatihanCategory::factory()->create();
    $pelatihan = Pelatihan::factory()->create(['pelatihan_category_id' => $category->id]);

    $category->delete();

    expect($pelatihan->fresh()->pelatihan_category_id)->toBeNull();
});

test('thumbnail_url returns null when no thumbnail', function () {
    $pelatihan = Pelatihan::factory()->create(['thumbnail' => null]);

    expect($pelatihan->thumbnail_url)->toBeNull();
});

test('can copy a pelatihan with its modules, videos, and questions', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    Storage::disk('public')->put('uploads/pelatihan/poster.jpg', 'fake-poster');
    $pelatihan = Pelatihan::factory()->create([
        'title' => 'Pelatihan BHD',
        'slug' => 'pelatihan-bhd',
        'thumbnail' => 'uploads/pelatihan/poster.jpg',
        'status' => PelatihanStatus::Published,
        'is_active' => true,
    ]);

    $module = PelatihanModule::factory()->create(['pelatihan_id' => $pelatihan->id, 'title' => 'Modul 1']);
    PelatihanModuleVideo::factory()->create(['pelatihan_module_id' => $module->id, 'title' => 'Video 1']);
    $question = PelatihanQuestion::factory()->create(['pelatihan_module_id' => $module->id]);
    PelatihanQuestionOption::factory()->count(2)->create(['pelatihan_question_id' => $question->id]);

    Livewire::test(PelatihanComponent::class)->call('copy', $pelatihan->id);

    $copy = Pelatihan::where('slug', 'pelatihan-bhd-salinan')->first();
    expect($copy)->not->toBeNull()
        ->and($copy->title)->toBe('Pelatihan BHD (Salinan)')
        ->and($copy->status)->toBe(PelatihanStatus::Draft)
        ->and($copy->is_active)->toBeFalse()
        ->and($copy->modules)->toHaveCount(1)
        ->and($copy->modules->first()->videos)->toHaveCount(1)
        ->and($copy->modules->first()->questions)->toHaveCount(1)
        ->and($copy->modules->first()->questions->first()->options)->toHaveCount(2)
        ->and($copy->thumbnail)->not->toBe($pelatihan->thumbnail);

    Storage::disk('public')->assertExists($copy->thumbnail);
});

test('copying a pelatihan twice generates unique slugs', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create(['slug' => 'pelatihan-bhd']);

    Livewire::test(PelatihanComponent::class)
        ->call('copy', $pelatihan->id)
        ->call('copy', $pelatihan->id);

    expect(Pelatihan::where('slug', 'pelatihan-bhd-salinan')->exists())->toBeTrue()
        ->and(Pelatihan::where('slug', 'pelatihan-bhd-salinan-2')->exists())->toBeTrue();
});
