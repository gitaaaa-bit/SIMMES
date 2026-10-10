<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $guarded = [];
    protected $casts = ['purchase_date' => 'date', 'last_service_at' => 'date', 'media' => 'array'];

    public function tickets()   { return $this->hasMany(Ticket::class); }
    public function schedules() { return $this->hasMany(MaintenanceSchedule::class); }

    public function nextSchedule()
    {
        return $this->hasOne(MaintenanceSchedule::class)->where('status', '!=', 'selesai')->orderBy('due_date');
    }

    public function getLabelAttribute(): string { return $this->code.' · '.$this->name; }

    public function getNeedsServiceAttribute(): bool
    {
        $n = $this->nextSchedule;
        return $n && $n->due_date->lte(now()->addDays(7));
    }
}
