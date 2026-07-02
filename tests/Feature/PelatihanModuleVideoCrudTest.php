<?php

use App\Enums\PelatihanVideoSourceType;
use App\Livewire\Actions\PelatihanForm;
use App\Models\Pelatihan;
use App\Models\PelatihanModule;
use App\Models\PelatihanModuleVideo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('can create an embed video', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openVideoCreate', $module->id)
        ->set('videoTitle', 'Pengenalan BHD')
        ->set('videoUrl', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
        ->call('saveVideo')
        ->assertHasNoErrors();

    $video = PelatihanModuleVideo::where('pelatihan_module_id', $module->id)->first();
    expect($video)->not->toBeNull()
        ->and($video->source_type)->toBe(PelatihanVideoSourceType::Embed)
        ->and($video->url)->toBe('https://www.youtube.com/watch?v=dQw4w9WgXcQ')
        ->and($video->playable_url)->toBe('https://www.youtube.com/embed/dQw4w9WgXcQ');
});

test('embed video requires a url', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openVideoCreate', $module->id)
        ->set('videoTitle', 'Video tanpa url')
        ->set('videoUrl', '')
        ->call('saveVideo')
        ->assertHasErrors(['videoUrl' => 'required']);
});

test('can create an uploaded video', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    $file = UploadedFile::fake()->create('materi.mp4', 1024, 'video/mp4');

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openVideoCreate', $module->id)
        ->set('videoTitle', 'Video Upload')
        ->set('videoSourceType', 'upload')
        ->set('uploadedVideo', $file)
        ->call('saveVideo')
        ->assertHasNoErrors();

    $video = PelatihanModuleVideo::where('pelatihan_module_id', $module->id)->first();
    expect($video->source_type)->toBe(PelatihanVideoSourceType::Upload)
        ->and($video->file_path)->toStartWith('uploads/pelatihan-videos/');
    Storage::disk('public')->assertExists($video->file_path);
});

test('uploaded video requires a file on create', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openVideoCreate', $module->id)
        ->set('videoTitle', 'Video tanpa berkas')
        ->set('videoSourceType', 'upload')
        ->call('saveVideo')
        ->assertHasErrors(['uploadedVideo' => 'required']);
});

test('switching an uploaded video to embed removes its old file', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    $path = 'uploads/pelatihan-videos/lama.mp4';
    Storage::disk('public')->put($path, 'fake-content');
    $video = PelatihanModuleVideo::factory()->for($module, 'module')->uploaded()->create(['file_path' => $path]);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('editVideo', $video->id)
        ->set('videoSourceType', 'embed')
        ->set('videoUrl', 'https://vimeo.com/123456789')
        ->call('saveVideo')
        ->assertHasNoErrors();

    Storage::disk('public')->assertMissing($path);
    expect($video->fresh()->file_path)->toBeNull()
        ->and($video->fresh()->url)->toBe('https://vimeo.com/123456789');
});

test('can edit an existing video', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $video = PelatihanModuleVideo::factory()->for($module, 'module')->create(['title' => 'Judul Lama']);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('editVideo', $video->id)
        ->assertSet('videoTitle', 'Judul Lama')
        ->set('videoTitle', 'Judul Baru')
        ->call('saveVideo')
        ->assertHasNoErrors();

    expect($video->fresh()->title)->toBe('Judul Baru');
});

test('deleting an uploaded video removes its file', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    $path = 'uploads/pelatihan-videos/hapus.mp4';
    Storage::disk('public')->put($path, 'fake-content');
    $video = PelatihanModuleVideo::factory()->for($module, 'module')->uploaded()->create(['file_path' => $path]);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('confirmDeleteVideo', $video->id)
        ->call('deleteVideo');

    expect(PelatihanModuleVideo::find($video->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('visiting the pelatihan edit page with form=video-create-{moduleId} opens the video create modal', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::withQueryParams(['form' => 'video-create-'.$module->id])
        ->test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->assertSet('showVideoForm', true)
        ->assertSet('currentModuleId', $module->id);
});

test('visiting the pelatihan edit page with form=video-{id} opens the video edit modal', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $video = PelatihanModuleVideo::factory()->for($module, 'module')->create(['title' => 'Video Lama']);

    Livewire::withQueryParams(['form' => 'video-'.$video->id])
        ->test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->assertSet('showVideoForm', true)
        ->assertSet('editingVideoId', $video->id)
        ->assertSet('videoTitle', 'Video Lama');
});

test('can copy an uploaded video within the same module', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    $path = 'uploads/pelatihan-videos/asli.mp4';
    Storage::disk('public')->put($path, 'fake-content');
    $video = PelatihanModuleVideo::factory()->for($module, 'module')->uploaded()->create(['title' => 'Video Asli', 'file_path' => $path]);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('copyVideo', $video->id);

    $copy = PelatihanModuleVideo::where('title', 'Video Asli (Salinan)')->first();
    expect($copy)->not->toBeNull()
        ->and($copy->pelatihan_module_id)->toBe($module->id)
        ->and($copy->file_path)->not->toBe($path);
    Storage::disk('public')->assertExists($copy->file_path);
});

test('can copy an embed video', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $video = PelatihanModuleVideo::factory()->for($module, 'module')->create(['title' => 'Video Embed', 'url' => 'https://vimeo.com/123456789']);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('copyVideo', $video->id);

    $copy = PelatihanModuleVideo::where('title', 'Video Embed (Salinan)')->first();
    expect($copy->url)->toBe('https://vimeo.com/123456789');
});

test('can reorder videos with move up and down', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $first = PelatihanModuleVideo::factory()->for($module, 'module')->create(['urutan' => 0]);
    $second = PelatihanModuleVideo::factory()->for($module, 'module')->create(['urutan' => 1]);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('moveVideoUp', $second->id);

    expect($second->fresh()->urutan)->toBe(0)
        ->and($first->fresh()->urutan)->toBe(1);
});
