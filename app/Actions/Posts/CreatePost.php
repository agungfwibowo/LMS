<?php

namespace App\Actions\Posts;

use App\Concerns\SanitizesHtml;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Str;

class CreatePost
{
    use SanitizesHtml;

    public function handle(array $input, int $authorId): Post
    {
        $imagePath = $input['featured_image']?->store('posts', 'public');

        $post = Post::create([
            'author_id' => $authorId,
            'title' => $input['title'],
            'slug' => $input['slug'],
            'content' => $this->sanitizeHtml($input['content']),
            'excerpt' => $input['excerpt'],
            'featured_image' => $imagePath,
            'status' => $input['status'],
            'published_at' => $input['status'] === 'published'
                ? ($input['published_at'] ?: now())
                : ($input['published_at'] ?: null),
        ]);

        $post->categories()->sync($input['selected_categories']);
        $post->tags()->sync($this->resolveTagIds($input['tag_input']));

        return $post;
    }

    private function resolveTagIds(string $tagInput): array
    {
        return collect(explode(',', $tagInput))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->map(fn ($name) => Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            )->id)
            ->toArray();
    }
}
