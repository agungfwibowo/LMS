<?php

namespace App\Actions\Posts;

use App\Actions\Concerns\CopiesStorageFile;
use App\Enums\PostStatus;
use App\Models\Post;

class CopyPost
{
    use CopiesStorageFile;

    public function handle(Post $post): Post
    {
        $post->loadMissing('categories', 'tags');

        $copy = Post::create([
            'author_id' => $post->author_id,
            'title' => $post->title.' (Salinan)',
            'slug' => $this->uniqueSlug($post->slug),
            'content' => $post->content,
            'excerpt' => $post->excerpt,
            'featured_image' => $this->copyStorageFile($post->featured_image),
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);

        $copy->categories()->sync($post->categories->pluck('id'));
        $copy->tags()->sync($post->tags->pluck('id'));

        return $copy;
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug.'-salinan';
        $counter = 2;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-salinan-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
