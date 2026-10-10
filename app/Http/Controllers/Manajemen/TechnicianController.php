<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;

class TechnicianController extends Controller
{
    public function index()
    {
        $technicians = User::where('role', 'teknisi')->where('status', 'aktif')
            ->withCount([
                'assignedTickets as active_count' => fn ($q) => $q->where('status', '!=', 'selesai'),
                'assignedTickets as done_count'   => fn ($q) => $q->where('status', 'selesai'),
            ])->orderBy('name')->get();

        return view('manajemen.teknisi.index', compact('technicians'));
    }

    public function show(User $teknisi)
    {
        abort_unless($teknisi->role === 'teknisi', 404);
        $tickets = Ticket::with('machine')->where('technician_id', $teknisi->id)->latest()->take(15)->get();
        return view('manajemen.teknisi.show', ['tech' => $teknisi, 'tickets' => $tickets]);
    }
}
