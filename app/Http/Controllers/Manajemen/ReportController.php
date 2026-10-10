<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $r)
    {
        $month = Carbon::createFromFormat('Y-m', $r->get('bulan', now()->format('Y-m')))->startOfMonth();

        $jobs = Ticket::with(['machine', 'media'])
            ->whereBetween('created_at', [$month->copy(), $month->copy()->endOfMonth()])
            ->orderBy('created_at')->get();

        $summary = [
            'total'     => $jobs->count(),
            'selesai'   => $jobs->where('status', 'selesai')->count(),
            'dikerjakan'=> $jobs->where('status', 'diproses')->count(),
            'terlambat' => $jobs->filter->is_overdue->count(),
        ];

        $upcoming = MaintenanceSchedule::with('machine')->where('status', '!=', 'selesai')->orderBy('due_date')->take(8)->get();

        return view('manajemen.laporan.index', compact('month', 'jobs', 'summary', 'upcoming'));
    }
}
