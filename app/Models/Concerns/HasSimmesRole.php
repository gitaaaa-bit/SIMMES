<?php

namespace App\Models\Concerns;

use App\Enums\Role;
use App\Models\Ticket;

trait HasSimmesRole
{
    public function isManajemen(): bool
    {
        return $this->role === Role::Manajemen || (is_string($this->role) && $this->role === 'manajemen');
    }

    public function isTeknisi(): bool
    {
        return $this->role === Role::Teknisi || (is_string($this->role) && $this->role === 'teknisi');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'technician_id');
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', trim($this->name ?? 'U')))->take(2)
            ->filter()
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'U';
    }
}
