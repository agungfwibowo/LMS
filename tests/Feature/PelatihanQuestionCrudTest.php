<?php

use App\Enums\PelatihanQuestionType;
use App\Livewire\Actions\PelatihanForm;
use App\Models\Pelatihan;
use App\Models\PelatihanModule;
use App\Models\PelatihanQuestion;
use App\Models\User;
use Livewire\Livewire;

test('question panel defaults to two empty options when opening create', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openQuestionCreate', $module->id)
        ->assertSet('questionType', 'pilihan_ganda')
        ->assertCount('options', 2);
});

test('can create a pilihan ganda question with one correct option', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openQuestionCreate', $module->id)
        ->set('questionText', 'Berapa rasio kompresi RJP dewasa?')
        ->set('bobot', 2)
        ->set('options', [
            ['text' => '15:2', 'is_correct' => false],
            ['text' => '30:2', 'is_correct' => true],
        ])
        ->call('saveQuestion')
        ->assertHasNoErrors();

    $question = PelatihanQuestion::where('pelatihan_module_id', $module->id)->first();
    expect($question)->not->toBeNull()
        ->and($question->tipe)->toBe(PelatihanQuestionType::PilihanGanda)
        ->and($question->bobot)->toBe(2)
        ->and($question->options)->toHaveCount(2)
        ->and($question->options->firstWhere('is_correct', true)->teks_pilihan)->toBe('30:2');
});

test('pilihan ganda requires exactly one correct option', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openQuestionCreate', $module->id)
        ->set('questionText', 'Soal tanpa jawaban benar')
        ->set('options', [
            ['text' => 'Opsi A', 'is_correct' => false],
            ['text' => 'Opsi B', 'is_correct' => false],
        ])
        ->call('saveQuestion')
        ->assertHasErrors(['options']);

    expect(PelatihanQuestion::count())->toBe(0);
});

test('can create a benar salah question', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openQuestionCreate', $module->id)
        ->set('questionType', 'benar_salah')
        ->set('questionText', 'Kompresi dada harus dihentikan saat memeriksa nadi.')
        ->set('correctAnswer', false)
        ->call('saveQuestion')
        ->assertHasNoErrors();

    $question = PelatihanQuestion::where('pelatihan_module_id', $module->id)->first();
    expect($question->tipe)->toBe(PelatihanQuestionType::BenarSalah)
        ->and($question->correct_answer)->toBeFalse()
        ->and($question->options)->toHaveCount(0);
});

test('can create an esai question with optional kunci jawaban', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openQuestionCreate', $module->id)
        ->set('questionType', 'esai')
        ->set('questionText', 'Jelaskan langkah-langkah BHD.')
        ->set('kunciJawaban', 'Cek respons, panggil bantuan, RJP, AED.')
        ->call('saveQuestion')
        ->assertHasNoErrors();

    $question = PelatihanQuestion::where('pelatihan_module_id', $module->id)->first();
    expect($question->tipe)->toBe(PelatihanQuestionType::Esai)
        ->and($question->kunci_jawaban)->toBe('Cek respons, panggil bantuan, RJP, AED.')
        ->and($question->options)->toHaveCount(0);
});

test('changing question type from pilihan ganda to esai removes its options', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $question = PelatihanQuestion::factory()->for($module, 'module')->create();
    $question->options()->create(['teks_pilihan' => 'A', 'is_correct' => true, 'urutan' => 0]);
    $question->options()->create(['teks_pilihan' => 'B', 'is_correct' => false, 'urutan' => 1]);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('editQuestion', $question->id)
        ->set('questionType', 'esai')
        ->set('questionText', 'Soal yang diubah jadi esai')
        ->call('saveQuestion')
        ->assertHasNoErrors();

    expect($question->fresh()->options)->toHaveCount(0);
});

test('can edit an existing question', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $question = PelatihanQuestion::factory()->for($module, 'module')->create(['pertanyaan' => 'Soal lama']);
    $question->options()->create(['teks_pilihan' => 'A', 'is_correct' => true, 'urutan' => 0]);
    $question->options()->create(['teks_pilihan' => 'B', 'is_correct' => false, 'urutan' => 1]);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('editQuestion', $question->id)
        ->assertSet('questionText', 'Soal lama')
        ->set('questionText', 'Soal baru')
        ->call('saveQuestion')
        ->assertHasNoErrors();

    expect($question->fresh()->pertanyaan)->toBe('Soal baru');
});

test('can delete a question', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $question = PelatihanQuestion::factory()->for($module, 'module')->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('confirmDeleteQuestion', $question->id)
        ->call('deleteQuestion');

    expect(PelatihanQuestion::find($question->id))->toBeNull();
});

test('visiting the pelatihan edit page with form=question-create-{moduleId} opens the question create modal', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::withQueryParams(['form' => 'question-create-'.$module->id])
        ->test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->assertSet('showQuestionForm', true)
        ->assertSet('currentModuleIdForQuestion', $module->id);
});

test('visiting the pelatihan edit page with form=question-{id} opens the question edit modal', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $question = PelatihanQuestion::factory()->for($module, 'module')->create(['pertanyaan' => 'Soal lama']);

    Livewire::withQueryParams(['form' => 'question-'.$question->id])
        ->test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->assertSet('showQuestionForm', true)
        ->assertSet('editingQuestionId', $question->id)
        ->assertSet('questionText', 'Soal lama');
});

test('an unresolvable form url parameter is cleared instead of opening a modal', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();

    Livewire::withQueryParams(['form' => 'question-999999'])
        ->test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->assertSet('showQuestionForm', false)
        ->assertSet('form', '');
});

test('can reorder options within the question form with move up and down', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openQuestionCreate', $module->id)
        ->set('options', [
            ['text' => 'Opsi A', 'is_correct' => true],
            ['text' => 'Opsi B', 'is_correct' => false],
        ])
        ->call('moveOptionDown', 0)
        ->assertSet('options.0.text', 'Opsi B')
        ->assertSet('options.1.text', 'Opsi A')
        ->call('moveOptionUp', 1)
        ->assertSet('options.0.text', 'Opsi A')
        ->assertSet('options.1.text', 'Opsi B');
});

test('moving the first option up or the last option down does nothing', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('openQuestionCreate', $module->id)
        ->set('options', [
            ['text' => 'Opsi A', 'is_correct' => true],
            ['text' => 'Opsi B', 'is_correct' => false],
        ])
        ->call('moveOptionUp', 0)
        ->assertSet('options.0.text', 'Opsi A')
        ->call('moveOptionDown', 1)
        ->assertSet('options.1.text', 'Opsi B');
});

test('can copy a question with its options', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $question = PelatihanQuestion::factory()->for($module, 'module')->create(['pertanyaan' => 'Soal asli']);
    $question->options()->create(['teks_pilihan' => 'A', 'is_correct' => true, 'urutan' => 0]);
    $question->options()->create(['teks_pilihan' => 'B', 'is_correct' => false, 'urutan' => 1]);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('copyQuestion', $question->id);

    $copy = PelatihanQuestion::where('pertanyaan', 'Soal asli (Salinan)')->first();
    expect($copy)->not->toBeNull()
        ->and($copy->pelatihan_module_id)->toBe($module->id)
        ->and($copy->options)->toHaveCount(2);
});

test('can reorder questions with move up and down', function () {
    $this->actingAs(User::factory()->create());
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $first = PelatihanQuestion::factory()->for($module, 'module')->create(['urutan' => 0]);
    $second = PelatihanQuestion::factory()->for($module, 'module')->create(['urutan' => 1]);

    Livewire::test(PelatihanForm::class, ['pelatihan' => $pelatihan])
        ->call('moveQuestionUp', $second->id);

    expect($second->fresh()->urutan)->toBe(0)
        ->and($first->fresh()->urutan)->toBe(1);
});

test('deleting a module cascades to its questions and options', function () {
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $question = PelatihanQuestion::factory()->for($module, 'module')->create();
    $question->options()->create(['teks_pilihan' => 'A', 'is_correct' => true, 'urutan' => 0]);

    $module->delete();

    expect(PelatihanQuestion::find($question->id))->toBeNull();
});

test('deleting a pelatihan cascades to its modules and questions', function () {
    $pelatihan = Pelatihan::factory()->create();
    $module = PelatihanModule::factory()->for($pelatihan)->create();
    $question = PelatihanQuestion::factory()->for($module, 'module')->create();

    $pelatihan->delete();

    expect(PelatihanModule::find($module->id))->toBeNull()
        ->and(PelatihanQuestion::find($question->id))->toBeNull();
});
