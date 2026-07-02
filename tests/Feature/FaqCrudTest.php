<?php

use App\Models\Faq;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected from faq admin page', function () {
    $this->get(route('faqs.index'))->assertRedirect(route('login'));
});

test('authenticated users can visit the faq admin page', function () {
    $this->actingAs(User::factory()->create());
    $this->get(route('faqs.index'))->assertOk();
});

test('faq list is displayed', function () {
    $this->actingAs(User::factory()->create());
    $faq = Faq::factory()->create(['question' => 'Bagaimana cara daftar?']);

    $this->get(route('faqs.index'))->assertSee('Bagaimana cara daftar?');
});

test('can create a new faq', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(App\Livewire\Actions\Faq::class)
        ->call('openCreate')
        ->set('question', 'Siapa bisa daftar?')
        ->set('answer', 'Semua pegawai RS Adam Malik.')
        ->call('save');

    expect(Faq::where('question', 'Siapa bisa daftar?')->exists())->toBeTrue();
});

test('can edit an existing faq', function () {
    $this->actingAs(User::factory()->create());
    $faq = Faq::factory()->create(['question' => 'Pertanyaan lama']);

    Livewire::test(App\Livewire\Actions\Faq::class)
        ->call('edit', $faq->id)
        ->set('question', 'Pertanyaan baru')
        ->call('save');

    expect($faq->fresh()->question)->toBe('Pertanyaan baru');
});

test('can toggle faq active status', function () {
    $this->actingAs(User::factory()->create());
    $faq = Faq::factory()->create(['is_active' => true]);

    Livewire::test(App\Livewire\Actions\Faq::class)->call('toggleActive', $faq->id);

    expect($faq->fresh()->is_active)->toBeFalse();
});

test('can copy a faq as an inactive draft', function () {
    $this->actingAs(User::factory()->create());
    $faq = Faq::factory()->create(['question' => 'Bagaimana cara daftar?', 'is_active' => true]);

    Livewire::test(App\Livewire\Actions\Faq::class)->call('copy', $faq->id);

    $copy = Faq::where('question', 'Bagaimana cara daftar? (Salinan)')->first();
    expect($copy)->not->toBeNull()
        ->and($copy->answer)->toBe($faq->answer)
        ->and($copy->is_active)->toBeFalse();
});

test('can delete a faq', function () {
    $this->actingAs(User::factory()->create());
    $faq = Faq::factory()->create();

    Livewire::test(App\Livewire\Actions\Faq::class)
        ->call('confirmDelete', $faq->id)
        ->call('delete');

    expect(Faq::find($faq->id))->toBeNull();
});

test('can reorder faqs with move up', function () {
    $this->actingAs(User::factory()->create());
    $first = Faq::factory()->create(['order' => 1]);
    $second = Faq::factory()->create(['order' => 2]);

    Livewire::test(App\Livewire\Actions\Faq::class)->call('moveUp', $second->id);

    expect($second->fresh()->order)->toBe(1)
        ->and($first->fresh()->order)->toBe(2);
});

test('landing page shows active faqs from database', function () {
    Faq::factory()->create(['question' => 'FAQ Aktif', 'is_active' => true, 'order' => 1]);
    Faq::factory()->inactive()->create(['question' => 'FAQ Nonaktif', 'order' => 2]);

    $this->get(route('home'))
        ->assertSee('FAQ Aktif')
        ->assertDontSee('FAQ Nonaktif');
});
