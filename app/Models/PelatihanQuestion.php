<?php

namespace App\Models;

use App\Enums\PelatihanQuestionType;
use Database\Factories\PelatihanQuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PelatihanQuestion extends Model
{
    /** @use HasFactory<PelatihanQuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'pelatihan_module_id',
        'tipe',
        'pertanyaan',
        'correct_answer',
        'kunci_jawaban',
        'bobot',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'tipe' => PelatihanQuestionType::class,
            'correct_answer' => 'boolean',
            'bobot' => 'integer',
            'urutan' => 'integer',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(PelatihanModule::class, 'pelatihan_module_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(PelatihanQuestionOption::class)->orderBy('urutan');
    }
}
