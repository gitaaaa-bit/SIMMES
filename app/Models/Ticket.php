<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $guarded = [];
    protected $casts = ['due_at' => 'datetime', 'started_at' => 'datetime', 'finished_at' => 'datetime'];

    public function machine()    { return $this->belongsTo(Machine::class); }
    public function reporter()   { return $this->belongsTo(User::class, 'reporter_id'); }
    public function technician() { return $this->belongsTo(User::class, 'technician_id'); }
    public function media()      { return $this->hasMany(TicketMedia::class); }
    public function logs()       { return $this->hasMany(TicketLog::class)->orderBy('created_at'); }

    public function mediaOf(string $phase) { return $this->media->where('phase', $phase); }

    /** Terlambat dihitung otomatis: lewat batas waktu & belum selesai. */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'selesai' && $this->due_at && $this->due_at->isPast();
    }

    /** Status yang ditampilkan di UI (terlambat menimpa status berjalan). */
    public function getDisplayStatusAttribute(): string
    {
        return $this->is_overdue ? 'terlambat' : $this->status;
    }

    public static function nextCode(): string
    {
        $n = (int) static::max('id') + 1;
        return 'TK-'.str_pad((string) (100 + $n), 4, '0', STR_PAD_LEFT);
    }

    /** Hitung progress 25/50/75/100 sesuai checklist di Figma. */
    public function recalcProgress(): void
    {
        $p = 0;
        if ($this->mediaOf('before')->count() || $this->media()->where('phase', 'before')->exists()) $p = 25;
        if ($this->status === 'diproses' && $p >= 25) $p = 50;
        if ($p >= 50 && $this->media()->where('phase', 'after')->exists()) $p = 75;
        if ($this->status === 'selesai') $p = 100;
        $this->progress = $p;
        $this->save();
    }

    public function log(string $label, ?string $note = null, ?int $userId = null): void
    {
        $this->logs()->create(['label' => $label, 'note' => $note, 'user_id' => $userId ?? auth()->id()]);
    }
}
