<?php

namespace App\Models;

use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory;

    protected $fillable = ['name', 'role', 'quote', 'avatar_color', 'photo', 'rating', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'rating' => 'float',
        ];
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->take(2)
            ->map(fn (string $word): string => strtoupper($word[0]))
            ->implode('');
    }

    public function getAvatarBgClassAttribute(): string
    {
        return $this->avatar_color === 'lime' ? 'bg-lime-50' : 'bg-brand-50';
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        return Storage::disk('public')->url($this->photo);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
