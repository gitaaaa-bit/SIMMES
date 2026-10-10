<?php

namespace App\Enums;

enum AccountStatus: string
{
    case Pending = 'pending';
    case Aktif = 'aktif';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu persetujuan',
            self::Aktif => 'Aktif',
            self::Ditolak => 'Ditolak',
        };
    }
}
