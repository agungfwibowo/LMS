<?php

use App\Livewire\Actions\PelatihanForm;
use App\Models\Pelatihan;
use App\Models\PelatihanModule;
use App\Models\PelatihanModuleVideo;
use App\Models\PelatihanQuestion;
use App\Models\User;
use Livewire\Livewire;

test('can create a module', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openModuleCreate')
        ->set('moduleTitle', 'Modul 1: Pengenalan BHD')
        ->set('moduleDescription', 'Materi dasar bantuan hidup dasar.')
        ->call('saveModule')
        ->assertHasNoErrors();

    $module = PelatihanModule::where('pelatihan_id', $pelatihan->id)->first();
    expect($module)->not->toBeNull()
        ->and($module->title)->toBe('Modul 1: Pengenalan BHD')
        ->and($module->urutan)->toBe(0);
});

test('module title is required', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openModuleCreate')
        ->set('moduleTitle', '')
        ->call('saveModule')
        ->assertHasErrors(['moduleTitle' => 'required']);
});

test('can edit an existing module', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create(['title' => 'Judul Lama']);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('editModule', $module->id)
        ->assertSet('moduleTitle', 'Judul Lama')
        ->set('moduleTitle', 'Judul Baru')
        ->call('saveModule')
        ->assertHasNoErrors();

    expect($module->fresh()->title)->toBe('Judul Baru');
});

test('can delete a module', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('confirmDeleteModule', $module->id)
        ->call('deleteModule');

    expect(PelatihanModule::find($module->id))->toBeNull();
});

test('visiting the pelatihan edit page with form=module-create opens the module create modal', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();

    Livewire::withQueryParams(['form' => 'module-create'])
        ->test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->assertSet('showModuleForm', true)
        ->assertSet('editingModuleId', null);
});

test('visiting the pelatihan edit page with form=module-{id} opens the module edit modal', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create(['title' => 'Modul Lama']);

    Livewire::withQueryParams(['form' => 'module-'.$module->id])
        ->test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->assertSet('showModuleForm', true)
        ->assertSet('editingModuleId', $module->id)
        ->assertSet('moduleTitle', 'Modul Lama');
});

test('saving a module resets the form url parameter', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openModuleCreate')
        ->assertSet('form', 'module-create')
        ->set('moduleTitle', 'Modul Baru')
        ->call('saveModule')
        ->assertSet('form', '');
});

test('can copy a module with its videos and questions', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create(['title' => 'Modul 1', 'urutan' => 0]);
    PelatihanModuleVideo::factory()->for($module, 'module')->create();
    $question = PelatihanQuestion::factory()->for($module, 'module')->create();
    $question->options()->create(['teks_pilihan' => 'A', 'is_correct' => true, 'urutan' => 0]);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('copyModule', $module->id);

    $copy = PelatihanModule::where('title', 'Modul 1 (Salinan)')->first();
    expect($copy)->not->toBeNull()
        ->and($copy->urutan)->toBe(1)
        ->and($copy->videos)->toHaveCount(1)
        ->and($copy->questions)->toHaveCount(1)
        ->and($copy->questions->first()->options)->toHaveCount(1);
});

test('can reorder modules with move up and down', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $first = PelatihanModule::factory()->for($pelatihan)->create(['urutan' => 0]);
    $second = PelatihanModule::factory()->for($pelatihan)->create(['urutan' => 1]);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('moveModuleUp', $second->id);

    expect($second->fresh()->urutan)->toBe(0)
        ->and($first->fresh()->urutan)->toBe(1);
});

test('modules panel is empty until the pelatihan is created', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(PelatihanForm::class)->assertSet('pelatihan', null);
});
