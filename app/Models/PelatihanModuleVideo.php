<?php

namespace App\Models;

use App\Enums\PelatihanVideoSourceType;
use Database\Factories\PelatihanModuleVideoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PelatihanModuleVideo extends Model
{
    /** @use HasFactory<PelatihanModuleVideoFactory> */
    use HasFactory;

    protected $fillable = [
        'pelatihan_module_id',
        'title',
        'source_type',
        'url',
        'file_path',
        'duration_seconds',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'source_type' => PelatihanVideoSourceType::class,
            'duration_seconds' => 'integer',
            'urutan' => 'integer',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(PelatihanModule::class, 'pelatihan_module_id');
    }

    public function getPlayableUrlAttribute(): ?string
    {
        if ($this->source_type === PelatihanVideoSourceType::Upload) {
            return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
        }

        return $this->embedUrl();
    }

    private function embedUrl(): ?string
    {
        if (! $this->url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/', $this->url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $this->url, $matches)) {
            return 'https://player.vimeo.com/video/'.$matches[1];
        }

        return $this->url;
    }
}
