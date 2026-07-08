<?php

use App\Actions\Posts\CreatePost;
use App\Models\Post;
use App\Models\User;

function createPostWithContent(string $content): Post
{
    return app(CreatePost::class)->handle([
        'title' => 'Judul',
        'slug' => fake()->unique()->slug(),
        'content' => $content,
        'excerpt' => '',
        'featured_image' => null,
        'status' => 'draft',
        'published_at' => null,
        'selected_categories' => [],
        'tag_input' => '',
    ], User::factory()->create()->id);
}

test('script tags are removed from post content', function () {
    $post = createPostWithContent('<p>Halo</p><script>alert(1)</script>');

    expect($post->content)->toBe('<p>Halo</p>');
});

test('event handler attributes are removed', function () {
    $post = createPostWithContent('<p onclick="alert(1)">Halo</p><img src="/storage/a.jpg" onerror="alert(1)">');

    expect($post->content)->not->toContain('onclick')
        ->not->toContain('onerror')
        ->toContain('<img src="/storage/a.jpg"');
});

test('javascript urls are removed from links', function () {
    $post = createPostWithContent('<a href="javascript:alert(1)">klik</a><a href="https://example.com">ok</a>');

    expect($post->content)->not->toContain('javascript:')
        ->toContain('href="https://example.com"');
});

test('disallowed tags are unwrapped but text is kept', function () {
    $post = createPostWithContent('<article><p>Isi <marquee>berjalan</marquee></p></article>');

    expect($post->content)->toBe('<p>Isi berjalan</p>');
});

test('quill formatting is preserved', function () {
    $html = '<h2>Judul</h2><p class="ql-align-center"><strong>Tebal</strong> <em>miring</em>'
        .' <span style="color: #ff0000">merah</span></p><ul><li>satu</li></ul><blockquote>kutip</blockquote>';

    $post = createPostWithContent($html);

    expect($post->content)->toContain('<h2>Judul</h2>')
        ->toContain('class="ql-align-center"')
        ->toContain('<strong>Tebal</strong>')
        ->toContain('style="color: #ff0000"')
        ->toContain('<li>satu</li>')
        ->toContain('<blockquote>kutip</blockquote>');
});

test('non quill classes and unsafe styles are stripped', function () {
    $post = createPostWithContent('<p class="hacky ql-align-right" style="position: fixed; color: red">x</p>');

    expect($post->content)->toContain('class="ql-align-right"')
        ->not->toContain('hacky')
        ->not->toContain('position')
        ->toContain('color: red');
});
