<?php

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('can copy a post with its categories, tags, and featured image', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $path = 'posts/gambar.jpg';
    Storage::disk('public')->put($path, 'fake-content');
    $post = Post::factory()->create([
        'title' => 'Berita Asli',
        'slug' => 'berita-asli',
        'status' => PostStatus::Published,
        'published_at' => now(),
        'featured_image' => $path,
    ]);
    $category = Category::factory()->create();
    $tag = Tag::factory()->create();
    $post->categories()->attach($category);
    $post->tags()->attach($tag);

    Livewire::test('pages::posts.index')->call('copy', $post->id);

    $copy = Post::where('slug', 'berita-asli-salinan')->first();
    expect($copy)->not->toBeNull()
        ->and($copy->title)->toBe('Berita Asli (Salinan)')
        ->and($copy->status)->toBe(PostStatus::Draft)
        ->and($copy->published_at)->toBeNull()
        ->and($copy->author_id)->toBe($post->author_id)
        ->and($copy->categories->pluck('id'))->toContain($category->id)
        ->and($copy->tags->pluck('id'))->toContain($tag->id)
        ->and($copy->featured_image)->not->toBe($path);
    Storage::disk('public')->assertExists($copy->featured_image);
});

test('copying a post twice generates unique slugs', function () {
    $this->actingAs(User::factory()->create());
    $post = Post::factory()->create(['slug' => 'berita-asli']);

    Livewire::test('pages::posts.index')
        ->call('copy', $post->id)
        ->call('copy', $post->id);

    expect(Post::where('slug', 'berita-asli-salinan')->exists())->toBeTrue()
        ->and(Post::where('slug', 'berita-asli-salinan-2')->exists())->toBeTrue();
});
