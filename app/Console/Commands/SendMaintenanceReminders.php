<?php

namespace App\Console\Commands;

use App\Models\MaintenanceSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Aturan notifikasi (Figma): H-7 & H-1 teknisi, hari-H teknisi, terlambat teknisi + manajemen.
 * Jadwalkan harian: $schedule->command('simmes:reminders')->dailyAt('07:00');
 * Ganti Log::info dengan Mail / gateway WhatsApp pilihanmu.
 */
class SendMaintenanceReminders extends Command
{
    protected $signature = 'simmes:reminders';
    protected $description = 'Kirim pengingat jadwal maintenance (H-7, H-1, hari-H, terlambat)';

    public function handle(): int
    {
        $today = now()->startOfDay();
        MaintenanceSchedule::with(['machine', 'technician'])->where('status', '!=', 'selesai')->get()
            ->each(function ($s) use ($today) {
                $d = (int) $today->diffInDays($s->due_date, false);
                $kind = match (true) { $d === 7 => 'H-7', $d === 1 => 'H-1', $d === 0 => 'Hari-H', $d < 0 => 'Terlambat', default => null };
                if (!$kind) return;
                Log::info("[REMINDER {$kind}] {$s->machine->code} {$s->task} -> ".($s->technician?->whatsapp ?? 'belum ditugaskan'));
                $this->line("{$kind}: {$s->machine->code} {$s->task}");
            });
        return self::SUCCESS;
    }
}
