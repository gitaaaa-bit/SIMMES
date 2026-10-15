<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Models\Concerns\HasSimmesRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasSimmesRole;

    protected $fillable = [
        'name',
        'nik',
        'email',
        'password',
        'role',
        'status',
        'department',
        'bagian',
        'whatsapp',
        'approved_by',
        'approved_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => Role::class,
            'status' => AccountStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Generate NIK otomatis sesuai peran (misal: TEK001, MGR001).
     */
    public static function nextNik(string|Role $role): string
    {
        $roleValue = $role instanceof Role ? $role->value : $role;
        $prefix = in_array(strtolower((string) $roleValue), ['manajemen', 'manager']) ? 'MGR' : 'TEK';

        $latest = static::where('nik', 'LIKE', "{$prefix}%")
            ->pluck('nik')
            ->map(function ($val) use ($prefix) {
                return (int) substr($val, strlen($prefix));
            })
            ->max();

        $nextNumber = ($latest && $latest >= 1) ? $latest + 1 : 1;
        $nik = sprintf('%s%03d', $prefix, $nextNumber);
        while (static::where('nik', $nik)->exists()) {
            $nextNumber++;
            $nik = sprintf('%s%03d', $prefix, $nextNumber);
        }

        return $nik;
    }
}
