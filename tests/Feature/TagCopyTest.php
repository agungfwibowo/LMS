<?php

use App\Livewire\Actions\Tag;
use App\Models\Tag as TagModel;
use App\Models\User;
use Livewire\Livewire;

test('can copy a tag with a unique slug', function () {
    $this->actingAs(User::factory()->create());
    $tag = TagModel::factory()->create([
        'name' => 'Hukum',
        'slug' => 'hukum',
    ]);

    Livewire::test(Tag::class)->call('copy', $tag->id);

    $copy = TagModel::where('slug', 'hukum-salinan')->first();
    expect($copy)->not->toBeNull()
        ->and($copy->name)->toBe('Hukum (Salinan)');
});

test('copying a tag twice generates unique slugs', function () {
    $this->actingAs(User::factory()->create());
    $tag = TagModel::factory()->create(['slug' => 'hukum']);

    Livewire::test(Tag::class)
        ->call('copy', $tag->id)
        ->call('copy', $tag->id);

    expect(TagModel::where('slug', 'hukum-salinan')->exists())->toBeTrue()
        ->and(TagModel::where('slug', 'hukum-salinan-2')->exists())->toBeTrue();
});
