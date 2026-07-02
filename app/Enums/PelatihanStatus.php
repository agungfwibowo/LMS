<?php

namespace App\Enums;

enum PelatihanStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            PelatihanStatus::Draft => 'Draft',
            PelatihanStatus::Published => 'Dipublikasi',
            PelatihanStatus::Archived => 'Diarsipkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            PelatihanStatus::Draft => 'yellow',
            PelatihanStatus::Published => 'green',
            PelatihanStatus::Archived => 'zinc',
        };
    }
}
