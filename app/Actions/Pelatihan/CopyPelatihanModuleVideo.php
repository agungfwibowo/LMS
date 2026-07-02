<?php

namespace App\Actions\Pelatihan;

use App\Actions\Concerns\CopiesStorageFile;
use App\Enums\PelatihanVideoSourceType;
use App\Models\PelatihanModuleVideo;

class CopyPelatihanModuleVideo
{
    use CopiesStorageFile;

    public function handle(PelatihanModuleVideo $video): PelatihanModuleVideo
    {
        $module = $video->module;

        return $module->videos()->create([
            'title' => $video->title.' (Salinan)',
            'source_type' => $video->source_type,
            'url' => $video->url,
            'file_path' => $video->source_type === PelatihanVideoSourceType::Upload
                ? $this->copyStorageFile($video->file_path)
                : $video->file_path,
            'duration_seconds' => $video->duration_seconds,
            'urutan' => $module->videos()->count(),
        ]);
    }
}
