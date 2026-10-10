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
}
