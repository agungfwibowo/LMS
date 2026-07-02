<?php

namespace App\Actions\Concerns;

use Illuminate\Support\Facades\Storage;

trait CopiesStorageFile
{
    private function copyStorageFile(?string $path): ?string
    {
        if (! $path || str_starts_with($path, 'http')) {
            return $path;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return null;
        }

        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $newPath = $directory.'/'.uniqid('copy_').($extension ? '.'.$extension : '');

        $disk->copy($path, $newPath);

        return $newPath;
    }
}
