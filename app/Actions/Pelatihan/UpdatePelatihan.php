<?php

namespace App\Actions\Pelatihan;

use App\Models\Pelatihan;
use Illuminate\Support\Facades\Storage;

class UpdatePelatihan
{
    public function handle(Pelatihan $pelatihan, array $input): Pelatihan
    {
        $thumbnail = $input['existing_thumbnail'];

        if ($input['thumbnail']) {
            if ($input['existing_thumbnail'] && ! str_starts_with($input['existing_thumbnail'], 'http')) {
                Storage::disk('public')->delete($input['existing_thumbnail']);
            }
            $thumbnail = $input['thumbnail']->store('uploads/pelatihan', 'public');
        }

        $pelatihan->update([
            'pelatihan_category_id' => $input['pelatihan_category_id'] ?: null,
            'title' => $input['title'],
            'slug' => $input['slug'],
            'description' => $input['description'] ?: null,
            'thumbnail' => $thumbnail,
            'status' => $input['status'],
            'is_active' => $input['is_active'],
            'start_date' => $input['start_date'] ?: null,
            'end_date' => $input['end_date'] ?: null,
            'location' => $input['location'] ?: null,
            'mode' => $input['mode'],
            'instructor' => $input['instructor'] ?: null,
            'quota' => $input['quota'],
            'price' => $input['price'],
        ]);

        return $pelatihan;
    }
}
