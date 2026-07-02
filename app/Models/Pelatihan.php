<?php

namespace App\Models;

use App\Enums\PelatihanStatus;
use Database\Factories\PelatihanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Pelatihan extends Model
{
    /** @use HasFactory<PelatihanFactory> */
    use HasFactory;

    protected $fillable = [
        'pelatihan_category_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'status',
        'is_active',
        'start_date',
        'end_date',
        'location',
        'mode',
        'instructor',
        'quota',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'status' => PelatihanStatus::class,
            'is_active' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'quota' => 'integer',
            'price' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PelatihanCategory::class, 'pelatihan_category_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(PelatihanModule::class)->orderBy('urutan');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->thumbnail) {
            return null;
        }

        if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
            return $this->thumbnail;
        }

        return Storage::disk('public')->url($this->thumbnail);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', PelatihanStatus::Published);
    }
}
