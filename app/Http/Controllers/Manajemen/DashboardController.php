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
            'menunggu'   => Ticket::whereIn('status', ['dilaporkan', 'ditugaskan'])->count(),
            'terlambat'  => (clone $open)->whereNotNull('due_at')->where('due_at', '<', now())->count(),
        ];

        $tickets = Ticket::with(['machine', 'technician', 'media', 'logs'])->latest()->take(6)->get();

        // data kecil untuk grafik batang 6 bulan terakhir
        $trend = collect(range(5, 0))->map(function ($i) {
            $m = now()->subMonths($i);
            return [
                'label' => $m->translatedFormat('M'),
                'count' => Ticket::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count(),
            ];
        });

        // Antrian jadwal maintenance aktif (belum selesai), yang baru dimasukkan berada di paling atas
        $schedules = MaintenanceSchedule::with(['machine.tickets.media', 'machine.tickets.logs', 'technician', 'ticket.media', 'ticket.logs'])
            ->where('status', '!=', 'selesai')
            ->latest()
            ->take(6)
            ->get();

        $upcoming = $schedules;

        return view('manajemen.dashboard', compact('stats', 'tickets', 'trend', 'upcoming', 'schedules'));
    }
}
