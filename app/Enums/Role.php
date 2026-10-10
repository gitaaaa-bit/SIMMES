<?php

namespace App\Enums;

enum Role: string
{
    case Teknisi = 'teknisi';
    case Manajemen = 'manajemen';

    public function label(): string
    {
        return match ($this) {
            self::Teknisi => 'Teknisi',
            self::Manajemen => 'Manager',
        };
    }
}
