<?php

namespace App\Actions\Posts;

use App\Concerns\SanitizesHtml;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdatePost
{
    use SanitizesHtml;

    public function handle(Post $post, array $input): Post
    {
        $imagePath = $input['existing_image'];

        if ($input['featured_image']) {
            if ($input['existing_image']) {
                Storage::disk('public')->delete($input['existing_image']);
            }
            $imagePath = $input['featured_image']->store('posts', 'public');
        }

        $publishedAt = match (true) {
            (bool) $input['published_at'] => $input['published_at'],
            $input['status'] === 'published' && ! $post->published_at => now(),
            default => $post->published_at,
        };

        $post->update([
            'author_id' => $input['author_id'],
            'title' => $input['title'],
            'slug' => $input['slug'],
            'content' => $this->sanitizeHtml($input['content']),
            'excerpt' => $input['excerpt'],
            'featured_image' => $imagePath,
            'status' => $input['status'],
            'published_at' => $publishedAt,
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
