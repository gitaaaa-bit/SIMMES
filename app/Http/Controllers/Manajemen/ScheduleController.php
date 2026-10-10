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
        $schedules = MaintenanceSchedule::with(['machine', 'technician'])->orderBy('due_date')->get();

        // kartu ringkas ala Figma: Lewat jadwal / Minggu ini / Terjadwal
        $cards = [
            'lewat'    => $schedules->filter(fn ($s) => $s->display_status === 'terlambat')->first(),
            'minggu'   => $schedules->filter(fn ($s) => $s->status !== 'selesai' && $s->due_date->between(now()->startOfDay(), now()->addDays(7)))->first(),
            'terjadwal'=> $schedules->filter(fn ($s) => $s->status === 'terjadwal' && $s->due_date->gt(now()->addDays(7)))->first(),
        ];
        $history = MaintenanceSchedule::with(['machine', 'technician'])->where('status', 'selesai')->latest('completed_at')->take(5)->get();

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
            'machine_id' => 'required|exists:machines,id',
            'task' => 'required|max:120',
            'type' => 'required|in:rutin,insiden',
            'interval_months' => 'nullable|integer|min:1|max:60',
            'due_date' => 'required|date',
            'technician_id' => 'nullable|exists:users,id',
        ]);
        $d['interval_label'] = $d['interval_months'] ? $d['interval_months'].' bulan' : null;
        MaintenanceSchedule::create($d);
        return redirect()->route('manajemen.jadwal.index')->with('ok', 'Jadwal ditambahkan.');
    }

    public function assign(Request $r, MaintenanceSchedule $jadwal)
    {
        $d = $r->validate(['technician_id' => 'required|exists:users,id']);
        $jadwal->update($d);
        return back()->with('ok', 'Teknisi ditugaskan.');
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
        return redirect()->route('manajemen.perbaikan.show', $t)->with('ok', "Tiket {$t->code} dibuat.");
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
            ]);
        }
        return back()->with('ok', 'Jadwal ditandai selesai, siklus berikutnya dibuat.');
    }
}
