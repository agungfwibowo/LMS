<?php

namespace App\Models;

use Database\Factories\PelatihanModuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PelatihanModule extends Model
{
    /** @use HasFactory<PelatihanModuleFactory> */
    use HasFactory;

    protected $fillable = [
        'pelatihan_id',
        'title',
        'description',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(PelatihanModuleVideo::class)->orderBy('urutan');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(PelatihanQuestion::class)->orderBy('urutan');
    }
}
