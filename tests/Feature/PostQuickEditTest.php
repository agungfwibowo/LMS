<?php

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('starting quick edit loads the post fields', function () {
    $author = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->create([
        'title' => 'Judul Lama',
        'slug' => 'judul-lama',
        'status' => PostStatus::Draft,
        'author_id' => $author->id,
    ]);
    $post->categories()->attach($category);

    Livewire::test('pages::posts.index')
        ->call('startQuickEdit', $post->id)
        ->assertSet('quickEditId', $post->id)
        ->assertSet('qeTitle', 'Judul Lama')
        ->assertSet('qeSlug', 'judul-lama')
        ->assertSet('qeStatus', PostStatus::Draft->value)
        ->assertSet('qeAuthorId', $author->id)
        ->assertSet('qeCategories', [(string) $category->id]);
});

test('quick edit updates title, status, author, and categories', function () {
    $newAuthor = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->create([
        'title' => 'Judul Lama',
        'slug' => 'judul-lama',
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    Livewire::test('pages::posts.index')
        ->call('startQuickEdit', $post->id)
        ->set('qeTitle', 'Judul Baru')
        ->set('qeSlug', 'judul-baru')
        ->set('qeStatus', PostStatus::Published->value)
        ->set('qeAuthorId', $newAuthor->id)
        ->set('qeCategories', [(string) $category->id])
        ->call('saveQuickEdit')
        ->assertSet('quickEditId', null);

    $post->refresh();
    expect($post->title)->toBe('Judul Baru')
        ->and($post->slug)->toBe('judul-baru')
        ->and($post->status)->toBe(PostStatus::Published)
        ->and($post->author_id)->toBe($newAuthor->id)
        ->and($post->published_at)->not->toBeNull()
        ->and($post->categories->pluck('id'))->toContain($category->id);
});

test('quick edit validates required title and unique slug', function () {
    $other = Post::factory()->create(['slug' => 'sudah-dipakai']);
    $post = Post::factory()->create(['slug' => 'berita-ini']);

    Livewire::test('pages::posts.index')
        ->call('startQuickEdit', $post->id)
        ->set('qeTitle', '')
        ->set('qeSlug', 'sudah-dipakai')
        ->call('saveQuickEdit')
        ->assertHasErrors(['qeTitle' => 'required', 'qeSlug' => 'unique']);

    expect($post->fresh()->slug)->toBe('berita-ini');
});

test('cancel quick edit clears state', function () {
    $post = Post::factory()->create();

    Livewire::test('pages::posts.index')
        ->call('startQuickEdit', $post->id)
        ->set('qeTitle', 'Berubah')
        ->call('cancelQuickEdit')
        ->assertSet('quickEditId', null)
        ->assertSet('qeTitle', '');
});
