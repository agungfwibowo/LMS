<?php

namespace App\Enums;

enum PelatihanQuestionType: string
{
    case PilihanGanda = 'pilihan_ganda';
    case BenarSalah = 'benar_salah';
    case Esai = 'esai';

    public function label(): string
    {
        return match ($this) {
            PelatihanQuestionType::PilihanGanda => 'Pilihan Ganda',
            PelatihanQuestionType::BenarSalah => 'Benar / Salah',
            PelatihanQuestionType::Esai => 'Esai',
        };
    }
}
