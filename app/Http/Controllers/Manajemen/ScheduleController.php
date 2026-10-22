<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MaintenanceSchedule;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $r)
    {
        // Antrian jadwal maintenance aktif (belum selesai), terbaru dimasukkan berada di paling atas
        $schedules = MaintenanceSchedule::with(['machine.tickets.media', 'machine.tickets.logs', 'technician', 'ticket.media', 'ticket.logs'])
            ->where('status', '!=', 'selesai')
            ->latest()
            ->get();

        // kartu ringkas ala Figma: Lewat jadwal / Minggu ini / Terjadwal
        $cards = [
            'lewat'    => $schedules->filter(fn ($s) => $s->display_status === 'terlambat')->first(),
            'minggu'   => $schedules->filter(fn ($s) => $s->due_date->between(now()->startOfDay(), now()->addDays(7)))->first(),
            'terjadwal'=> $schedules->filter(fn ($s) => $s->status === 'terjadwal' && $s->due_date->gt(now()->addDays(7)))->first(),
        ];
        $history = MaintenanceSchedule::with(['machine.tickets.media', 'machine.tickets.logs', 'technician', 'ticket.media', 'ticket.logs'])->where('status', 'selesai')->latest('completed_at')->take(10)->get();

        return view('manajemen.jadwal.index', compact('schedules', 'cards', 'history'));
    }

    public function calendar(Request $r)
    {
        $month = Carbon::createFromFormat('Y-m', $r->get('bulan', now()->format('Y-m')))->startOfMonth();
        $items = MaintenanceSchedule::with('machine')
            ->whereBetween('due_date', [$month->copy(), $month->copy()->endOfMonth()])
            ->orderBy('due_date')->get();

        return view('manajemen.jadwal.kalender', [
            'month' => $month,
            'byDay' => $items->groupBy(fn ($s) => $s->due_date->day),
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('manajemen.jadwal.form', [
            'machines'    => Machine::orderBy('code')->get(),
            'technicians' => User::where('role', 'teknisi')->where('status', 'aktif')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'machine_id'           => 'nullable',
            'machine_source'       => 'nullable|in:database,manual',
            'new_machine_name'     => 'nullable|string|max:120',
            'new_machine_code'     => 'nullable|string|max:50',
            'new_machine_location' => 'nullable|string|max:120',
            'task'                 => 'required|max:120',
            'type'                 => 'required|in:rutin,insiden',
            'interval_months'      => 'nullable|integer|min:1|max:60',
            'due_date'             => 'required|date',
            'due_time'             => 'nullable|string|max:8',
            'technician_id'        => 'nullable|exists:users,id',
        ]);

        // Resolusi mesin: pilih dari database ATAU buat mesin baru dari input manual
        $machine = null;
        if ($r->input('machine_source') === 'manual' || !empty($d['new_machine_name']) || empty($d['machine_id'])) {
            if (empty($d['new_machine_name'])) {
                return back()->withInput()->with('error', 'Silakan isi Nama Mesin (input manual) atau pilih mesin dari database.');
            }
            $machine = Machine::findOrCreateByName(
                $d['new_machine_name'],
                $d['new_machine_code'] ?? null,
                $d['new_machine_location'] ?? 'Workshop'
            );
        } else {
            $machine = Machine::find($d['machine_id']);
            if (!$machine) {
                return back()->withInput()->with('error', 'Mesin yang dipilih tidak ditemukan di database.');
            }
        }

        $scheduleData = [
            'machine_id'      => $machine->id,
            'task'            => $d['task'],
            'type'            => $d['type'],
            'interval_months' => $d['interval_months'],
            'interval_label'  => $d['interval_months'] ? $d['interval_months'].' bulan' : null,
            'due_date'        => $d['due_date'],
            'due_time'        => $d['due_time'] ?: '09:00',
            'technician_id'   => $d['technician_id'],
        ];

        $schedule = MaintenanceSchedule::create($scheduleData);
        $schedule->load(['machine', 'technician']);

        $waNote = '';
        if ($r->boolean('send_wa', true) && $schedule->technician) {
            $wa = \App\Services\WhatsAppService::sendScheduleReminder($schedule, 'Jadwal Baru');
            if ($wa['success']) {
                $waNote = ($wa['simulated'] ?? false)
                    ? " & simulasi WA tercatat di log ({$schedule->technician->name})."
                    : " & notifikasi WA terkirim ke {$schedule->technician->name} ({$schedule->technician->whatsapp}).";
            } else {
                $waNote = " (WA: {$wa['message']}).";
            }
        } elseif ($schedule->technician_id && !$schedule->technician?->whatsapp) {
            $waNote = " (Teknisi belum mengisi nomor WhatsApp).";
        }

        return redirect()->route('manajemen.jadwal.index')
            ->with('ok', "Jadwal untuk mesin {$machine->label} berhasil ditambahkan di antrian teratas{$waNote}");
    }

    public function assign(Request $r, MaintenanceSchedule $jadwal)
    {
        $d = $r->validate(['technician_id' => 'required|exists:users,id']);
        $tech = User::findOrFail($d['technician_id']);
        $jadwal->update($d);
        $jadwal->load(['machine', 'technician']);

        $wa = \App\Services\WhatsAppService::sendScheduleReminder($jadwal, 'Penugasan Baru');
        $waNote = $wa['success']
            ? ' & notifikasi WA terkirim.'
            : ($tech->whatsapp ? ' (WA: ' . $wa['message'] . ')' : ' (Teknisi belum mengisi WA).');

        return back()->with('ok', "Teknisi {$tech->name} ditugaskan{$waNote}");
    }

    /** "Buat tiket" dari jadwal yang lewat. */
    public function makeTicket(MaintenanceSchedule $jadwal)
    {
        $t = Ticket::create([
            'code' => Ticket::nextCode(), 'machine_id' => $jadwal->machine_id, 'reporter_id' => auth()->id(),
            'technician_id' => $jadwal->technician_id, 'type' => 'rutin', 'urgency' => 'sedang',
            'complaint' => $jadwal->task.' (jadwal maintenance)', 'status' => $jadwal->technician_id ? 'ditugaskan' : 'dilaporkan',
            'due_at' => $jadwal->due_date->endOfDay(),
        ]);
        $t->log('Dilaporkan', 'Dibuat dari jadwal maintenance');
        $jadwal->update(['ticket_id' => $t->id]);

        if (empty($jadwal->technician_id)) {
            \App\Services\WhatsAppService::broadcastToAllTechnicians($t);
        }

        return redirect()->route('manajemen.perbaikan.show', $t)->with('ok', "Tiket {$t->code} dibuat & disiarkan ke teknisi.");
    }

    /** "Tandai selesai" + jadwalkan siklus berikutnya otomatis. */
    public function complete(MaintenanceSchedule $jadwal)
    {
        $jadwal->update(['status' => 'selesai', 'progress' => 100, 'completed_at' => now()]);
        $jadwal->machine->update(['last_service_at' => now()]);

        if ($next = $jadwal->nextDue()) {
            MaintenanceSchedule::create([
                'machine_id' => $jadwal->machine_id, 'technician_id' => $jadwal->technician_id, 'type' => 'rutin',
                'task' => $jadwal->task, 'interval_label' => $jadwal->interval_label,
                'interval_months' => $jadwal->interval_months, 'due_date' => $next,
                'due_time' => $jadwal->due_time ?: '09:00',
            ]);
        }
        return back()->with('ok', 'Jadwal ditandai selesai, siklus berikutnya dibuat.');
    }

    /** Uji coba kirim notifikasi WhatsApp untuk jadwal ini. */
    public function testNotif(MaintenanceSchedule $jadwal)
    {
        $jadwal->load(['machine', 'technician']);
        if (!$jadwal->technician) {
            return back()->with('error', 'Pilih atau tugaskan teknisi terlebih dahulu pada jadwal ini untuk menguji notifikasi.');
        }

        $res = \App\Services\WhatsAppService::sendScheduleReminder($jadwal, 'Uji Coba Pengingat');
        if ($res['success']) {
            $msg = ($res['simulated'] ?? false)
                ? "Simulasi kirim notifikasi WA dicatat di log sistem untuk {$jadwal->technician->name} ({$jadwal->technician->whatsapp})."
                : "Notifikasi WhatsApp berhasil dikirim ke {$jadwal->technician->name} ({$jadwal->technician->whatsapp}).";
            return back()->with('ok', $msg);
        }

        return back()->with('error', "Gagal mengirim notifikasi WhatsApp: {$res['message']}");
    }

    /** Jalankan pengecekan dan pengiriman pengingat harian secara instan. */
    public function runReminders()
    {
        \Illuminate\Support\Facades\Artisan::call('simmes:reminders');
        $out = trim(\Illuminate\Support\Facades\Artisan::output());
        return back()->with('ok', 'Pengecekan pengingat jadwal berhasil dijalankan' . ($out ? ": {$out}" : '.'));
    }
}
