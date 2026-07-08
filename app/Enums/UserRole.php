<?php

namespace App\Enums;

enum UserRole: string
{
    case Peserta = 'peserta';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            UserRole::Peserta => 'Peserta',
            UserRole::Admin => 'Admin',
        };
    }

    public function color(): string
    {
        return match ($this) {
            UserRole::Peserta => 'zinc',
            UserRole::Admin => 'indigo',
        };
    }
}
