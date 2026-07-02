<?php

use App\Livewire\Actions\Category;
use App\Models\Category as CategoryModel;
use App\Models\User;
use Livewire\Livewire;

test('can copy a category with a unique slug', function () {
    $this->actingAs(User::factory()->create());
    $category = CategoryModel::factory()->create([
        'name' => 'Politik',
        'slug' => 'politik',
    ]);

    Livewire::test(Category::class)->call('copy', $category->id);

    $copy = CategoryModel::where('slug', 'politik-salinan')->first();
    expect($copy)->not->toBeNull()
        ->and($copy->name)->toBe('Politik (Salinan)')
        ->and($copy->description)->toBe($category->description);
});

test('copying a category twice generates unique slugs', function () {
    $this->actingAs(User::factory()->create());
    $category = CategoryModel::factory()->create(['slug' => 'politik']);

    Livewire::test(Category::class)
        ->call('copy', $category->id)
        ->call('copy', $category->id);

    expect(CategoryModel::where('slug', 'politik-salinan')->exists())->toBeTrue()
        ->and(CategoryModel::where('slug', 'politik-salinan-2')->exists())->toBeTrue();
});
