<?php

namespace App\Enums;

enum PelatihanVideoSourceType: string
{
    case Embed = 'embed';
    case Upload = 'upload';

    public function label(): string
    {
        return match ($this) {
            PelatihanVideoSourceType::Embed => 'Link Embed (YouTube/Vimeo)',
            PelatihanVideoSourceType::Upload => 'Upload File',
        };
    }
}
