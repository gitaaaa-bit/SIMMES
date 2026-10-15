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

    /**
     * Generate kode mesin otomatis unik (misal: MTR-001, MSN-002).
     */
    public static function generateCode(string $name): string
    {
        $clean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $prefix = substr($clean, 0, 3);
        if (strlen($prefix) < 2) {
            $prefix = 'MSN';
        }

        $count = static::where('code', 'LIKE', "{$prefix}-%")->count() + 1;
        $code = sprintf('%s-%03d', $prefix, $count);
        while (static::where('code', $code)->exists()) {
            $count++;
            $code = sprintf('%s-%03d', $prefix, $count);
        }
        return $code;
    }

    /**
     * Temukan atau buat mesin baru berdasarkan nama.
     */
    public static function findOrCreateByName(string $name, ?string $code = null, ?string $location = null): self
    {
        $name = trim($name);
        $machine = static::where('name', $name)->first();
        if ($machine) {
            if ($code && empty($machine->code)) $machine->update(['code' => $code]);
            if ($location && empty($machine->location)) $machine->update(['location' => $location]);
            return $machine;
        }

        if (empty($code)) {
            $code = static::generateCode($name);
        }

        return static::create([
            'name' => $name,
            'code' => $code,
            'location' => $location ?: 'Workshop',
            'status' => 'aktif',
            'maintenance_interval_months' => 3,
        ]);
    }
}
