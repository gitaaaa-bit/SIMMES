<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MaintenanceSchedule;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $open = Ticket::where('status', '!=', 'selesai');

        $stats = [
            'aktif'      => Machine::where('status', 'aktif')->count(),
            'diperbaiki' => Ticket::where('status', 'diproses')->count(),
            'menunggu'   => Ticket::whereNull('technician_id')->where('status', '!=', 'selesai')->count(),
            'terlambat'  => (clone $open)->whereNotNull('due_at')->where('due_at', '<', now())->count(),
        ];

        $tickets = Ticket::with(['machine', 'technician', 'media', 'logs'])->latest()->take(6)->get();

        // Data distribusi tiket untuk grafik donut enam bulan terakhir.
        $trend = collect(range(5, 0))->map(function ($i) {
            $m = now()->subMonths($i);
            return [
                'label' => $m->translatedFormat('M'),
                'count' => Ticket::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count(),
            ];
        });

        // Pekerjaan yang masih perlu dilakukan; siklus baru otomatis masuk ke sini setelah periode sebelumnya selesai.
        $upcoming = MaintenanceSchedule::with(['machine.tickets.media', 'machine.tickets.logs', 'technician', 'ticket.media', 'ticket.logs'])
            ->where('status', '!=', 'selesai')
            ->orderBy('due_date')
            ->orderBy('due_time')
            ->take(6)
            ->get();

        // Riwayat pekerjaan yang sudah dituntaskan, dipisahkan dari antrian aktif.
        $completedSchedules = MaintenanceSchedule::with(['machine', 'technician'])
            ->where('status', 'selesai')
            ->latest('completed_at')
            ->take(6)
            ->get();

        return view('manajemen.dashboard', compact('stats', 'tickets', 'trend', 'upcoming', 'completedSchedules'));
    }
}
