<?php

namespace App\Actions\Pelatihan;

use App\Enums\PelatihanVideoSourceType;
use App\Models\PelatihanModule;
use App\Models\PelatihanModuleVideo;
use Illuminate\Support\Facades\Storage;

class SavePelatihanModuleVideo
{
    public function handle(?PelatihanModuleVideo $video, PelatihanModule $module, array $input): PelatihanModuleVideo
    {
        $isUpload = $input['source_type'] === PelatihanVideoSourceType::Upload->value;

        $filePath = $input['existing_file_path'];
        $url = $input['url'];

        if ($isUpload) {
            $url = null;

            if ($input['uploaded_file']) {
                if ($filePath) {
                    Storage::disk('public')->delete($filePath);
                }
                $filePath = $input['uploaded_file']->store('uploads/pelatihan-videos', 'public');
            }
        } else {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = null;
        }

        $data = [
            'title' => $input['title'],
            'source_type' => $input['source_type'],
            'url' => $url,
            'file_path' => $filePath,
            'duration_seconds' => $input['duration_seconds'],
        ];

        if ($video) {
            $video->update($data);
        } else {
            $data['pelatihan_module_id'] = $module->id;
            $data['urutan'] = $module->videos()->count();
            $video = PelatihanModuleVideo::create($data);
        }

        return $video;
    }
}
