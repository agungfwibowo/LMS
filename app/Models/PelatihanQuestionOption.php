<?php

namespace App\Models;

use Database\Factories\PelatihanQuestionOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelatihanQuestionOption extends Model
{
    /** @use HasFactory<PelatihanQuestionOptionFactory> */
    use HasFactory;

    protected $fillable = [
        'pelatihan_question_id',
        'teks_pilihan',
        'is_correct',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(PelatihanQuestion::class, 'pelatihan_question_id');
    }
}
