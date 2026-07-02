<?php

use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

test('new post form defaults author to the logged-in user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('pages::posts.form')
        ->assertSet('authorId', $user->id);
});

test('can create a post with a different author than the creator', function () {
    $creator = User::factory()->create();
    $author = User::factory()->create();
    $this->actingAs($creator);

    Livewire::test('pages::posts.form')
        ->set('authorId', $author->id)
        ->set('title', 'Berita Baru')
        ->set('slug', 'berita-baru')
        ->set('content', '<p>Isi</p>')
        ->set('status', 'draft')
        ->call('save');

    $post = Post::where('slug', 'berita-baru')->first();
    expect($post)->not->toBeNull()
        ->and($post->author_id)->toBe($author->id);
});

test('edit form loads the existing author and can change it', function () {
    $originalAuthor = User::factory()->create();
    $newAuthor = User::factory()->create();
    $this->actingAs(User::factory()->create());

    $post = Post::factory()->create(['author_id' => $originalAuthor->id]);

    Livewire::test('pages::posts.form', ['post' => $post])
        ->assertSet('authorId', $originalAuthor->id)
        ->set('authorId', $newAuthor->id)
        ->call('save');

    expect($post->fresh()->author_id)->toBe($newAuthor->id);
});

test('author is required and must exist', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::posts.form')
        ->set('authorId', 999999)
        ->set('title', 'Berita')
        ->set('slug', 'berita')
        ->set('status', 'draft')
        ->call('save')
        ->assertHasErrors(['authorId' => 'exists']);
});
