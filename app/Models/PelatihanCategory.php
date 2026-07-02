<?php

namespace App\Models;

use App\Enums\PelatihanCategoryIcon;
use Database\Factories\PelatihanCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PelatihanCategory extends Model
{
    /** @use HasFactory<PelatihanCategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon'];

    protected function casts(): array
    {
        return [
            'icon' => PelatihanCategoryIcon::class,
        ];
    }

    public function pelatihans(): HasMany
    {
        return $this->hasMany(Pelatihan::class);
    }
}
