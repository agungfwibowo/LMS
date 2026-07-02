<?php

namespace App\Actions\Pelatihan;

use App\Models\Pelatihan;

class CreatePelatihan
{
    public function handle(array $input): Pelatihan
    {
        $thumbnail = $input['thumbnail']?->store('uploads/pelatihan', 'public');

        return Pelatihan::create([
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
    }
}
