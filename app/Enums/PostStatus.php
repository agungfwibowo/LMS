<?php

namespace App\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            PostStatus::Draft => 'Draft',
            PostStatus::Published => 'Dipublikasi',
            PostStatus::Archived => 'Diarsipkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            PostStatus::Draft => 'yellow',
            PostStatus::Published => 'green',
            PostStatus::Archived => 'zinc',
        };
    }
}
