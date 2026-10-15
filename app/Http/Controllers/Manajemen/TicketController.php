<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $r)
    {
        $q = Ticket::with(['machine', 'technician'])->latest();

        if ($s = $r->q) {
            $q->where(fn ($w) => $w->where('code', 'like', "%$s%")
                ->orWhereHas('machine', fn ($m) => $m->where('code', 'like', "%$s%")->orWhere('name', 'like', "%$s%")));
        }
        if ($r->status === 'terlambat') {
            $q->where('status', '!=', 'selesai')->where('due_at', '<', now());
        } elseif ($r->status) {
            $q->where('status', $r->status);
        }
        if ($r->urgency) $q->where('urgency', $r->urgency);
        if ($r->location) $q->whereHas('machine', fn ($m) => $m->where('location', $r->location));

        return view('manajemen.perbaikan.index', [
            'tickets'   => $q->paginate(12)->withQueryString(),
            'locations' => Machine::distinct()->orderBy('location')->pluck('location'),
        ]);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['machine', 'technician', 'reporter', 'media', 'logs.user']);
        $technicians = User::where('role', 'teknisi')->where('status', 'aktif')->orderBy('name')->get();
        return view('manajemen.perbaikan.show', compact('ticket', 'technicians'));
    }

    public function assign(Request $r, Ticket $ticket)
    {
        $data = $r->validate([
            'technician_id' => 'required|exists:users,id',
            'due_at'        => 'nullable|date',
        ]);
        $tech = User::findOrFail($data['technician_id']);

        $ticket->update([
            'technician_id' => $tech->id,
            'due_at'        => $data['due_at'] ?? now()->addDays($ticket->urgency === 'tinggi' ? 1 : 3),
            'status'        => $ticket->status === 'dilaporkan' ? 'ditugaskan' : $ticket->status,
        ]);
        $ticket->log('Ditugaskan', $tech->name);

        $waLink = null;
        if (!empty($tech->whatsapp)) {
            $msg = \App\Services\WhatsAppService::buildTicketMessage($ticket, $tech);
            $waLink = \App\Services\WhatsAppService::makeDirectLink($tech->whatsapp, $msg);
            \App\Services\WhatsAppService::sendTicketAssignment($ticket, $tech);
        }

        return back()
            ->with('ok', "Tiket {$ticket->code} berhasil ditugaskan ke {$tech->name}.")
            ->with('wa_link', $waLink)
            ->with('tech_name', $tech->name)
            ->with('tech_phone', $tech->whatsapp);
    }

    public function notifyWa(Ticket $ticket)
    {
        $ticket->load(['machine', 'technician']);
        if (!$ticket->technician || empty($ticket->technician->whatsapp)) {
            return back()->with('err', 'Teknisi belum ditugaskan atau belum memiliki nomor WhatsApp di profilnya.');
        }

        $msg = \App\Services\WhatsAppService::buildTicketMessage($ticket, $ticket->technician);
        $waLink = \App\Services\WhatsAppService::makeDirectLink($ticket->technician->whatsapp, $msg);

        // Coba kirim via gateway di background jika ada token
        \App\Services\WhatsAppService::sendTicketAssignment($ticket, $ticket->technician);

        // Buka langsung ke WhatsApp
        return redirect()->away($waLink);
    }
}
