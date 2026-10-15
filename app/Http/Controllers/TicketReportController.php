<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Ticket;
use App\Models\TicketMedia;
use Illuminate\Http\Request;

/** Form "Laporan Perbaikan" — dipakai teknisi & manajemen. */
class TicketReportController extends Controller
{
    public function create()
    {
        return view('tiket.lapor', ['machines' => Machine::orderBy('name')->get(['code', 'name', 'location'])]);
    }

    protected function resolveMachine(string $name, ?string $code = null, ?string $location = null): Machine
    {
        $name = trim($name);
        $location = $location ? trim($location) : 'Area Operasional';

        if (!empty($code)) {
            $code = strtoupper(trim($code));
            $machine = Machine::where('code', $code)->first();
            if ($machine) {
                $machine->update([
                    'name'     => $name ?: $machine->name,
                    'location' => $location ?: $machine->location,
                ]);
                return $machine;
            }
        }

        $machine = Machine::where('name', $name)->first();
        if ($machine) {
            if (!empty($code) && $machine->code !== $code) {
                $machine->update(['code' => $code]);
            }
            if (!empty($location)) {
                $machine->update(['location' => $location]);
            }
            return $machine;
        }

        if (empty($code)) {
            $letters = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $name));
            $prefix = 'MC-' . (substr($letters, 0, 3) ?: 'MSN');
            $count = Machine::where('code', 'like', "{$prefix}%")->count() + 1;
            $code = "{$prefix}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
            while (Machine::where('code', $code)->exists()) {
                $count++;
                $code = "{$prefix}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        }

        return Machine::create([
            'code'     => $code,
            'name'     => $name,
            'location' => $location,
            'status'   => 'dilaporkan',
        ]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'machine_name' => 'required|string|max:120',
            'machine_code' => 'nullable|string|max:50',
            'location'     => 'nullable|string|max:120',
            'complaint'    => 'required|max:1000',
            'urgency'      => 'required|in:rendah,sedang,tinggi',
            'files.*'      => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:30720',
        ], [
            'machine_name.required' => 'Nama mesin wajib diisi.',
            'complaint.required'    => 'Deskripsi keluhan wajib diisi.',
        ]);

        $machine = $this->resolveMachine(
            $d['machine_name'],
            $d['machine_code'] ?? null,
            $d['location'] ?? null
        );
        $user = $r->user();

        $ticket = Ticket::create([
            'code' => Ticket::nextCode(), 'machine_id' => $machine->id, 'reporter_id' => $user->id,
            // teknisi yang melapor langsung menjadi penanggung jawab
            'technician_id' => $user->isTeknisi() ? $user->id : null,
            'complaint' => $d['complaint'], 'urgency' => $d['urgency'],
            'status' => $user->isTeknisi() ? 'ditugaskan' : 'dilaporkan',
            'due_at' => now()->addDays(['tinggi' => 1, 'sedang' => 3, 'rendah' => 7][$d['urgency']]),
        ]);
        foreach ($r->file('files', []) as $f) {
            TicketMedia::create([
                'ticket_id' => $ticket->id, 'phase' => 'report',
                'kind' => str_starts_with($f->getMimeType(), 'video') ? 'video' : 'photo',
                'path' => $f->store("tickets/{$ticket->code}", 'public'),
            ]);
        }
        $ticket->log('Dilaporkan', $user->name);
        $machine->update(['status' => 'dilaporkan']);

        // Siarkan notifikasi laporan baru ke semua teknisi (siapa cepat dia dapat)
        if ($ticket->technician_id === null) {
            \App\Services\WhatsAppService::broadcastToAllTechnicians($ticket);
        }

        $routePrefix = $user->isTeknisi() ? 'teknisi' : 'manajemen';
        return redirect()->route($routePrefix.'.lapor.sukses', $ticket);
    }

    public function success(Ticket $ticket)
    {
        $broadcastLink = \App\Services\WhatsAppService::makeBroadcastShareLink($ticket);
        return view('tiket.sukses', [
            'ticket'        => $ticket->load('machine'),
            'broadcastLink' => $broadcastLink,
        ]);
    }

    /** Autocomplete "Ditemukan: Mesin Press 2, Produksi A" */
    public function lookup(string $code)
    {
        $m = Machine::where('code', strtoupper(trim($code)))->first();
        return response()->json($m ? ['found' => true, 'name' => $m->name, 'location' => $m->location] : ['found' => false]);
    }
}
