<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMedia;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $r)
    {
        $filter = $r->get('f', 'semua');
        $q = Ticket::with('machine')->where('technician_id', auth()->id())->latest();

        match ($filter) {
            'belum'   => $q->whereIn('status', ['ditugaskan', 'dilaporkan']),
            'proses'  => $q->where('status', 'diproses'),
            'selesai' => $q->where('status', 'selesai'),
            default   => null,
        };
        if ($s = $r->q) {
            $q->where(fn ($w) => $w->where('code', 'like', "%$s%")
                ->orWhereHas('machine', fn ($m) => $m->where('code', 'like', "%$s%")->orWhere('name', 'like', "%$s%")));
        }

        return view('teknisi.tugas', ['tickets' => $q->get(), 'filter' => $filter]);
    }

    public function show(Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        $ticket->load(['machine', 'media', 'logs.user', 'reporter']);
        return view('teknisi.detail', compact('ticket'));
    }

    /** "Mulai Proses" */
    public function start(Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        if ($ticket->status !== 'selesai') {
            $ticket->update(['status' => 'diproses', 'started_at' => $ticket->started_at ?? now()]);
            $ticket->machine->update(['status' => 'diproses']);
            $ticket->log('Diproses');
            $ticket->recalcProgress();
        }
        return redirect()->route('teknisi.tugas.show', $ticket);
    }

    /** Upload foto/video sebelum atau sesudah. */
    public function upload(Request $r, Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        $r->validate([
            'phase' => 'required|in:before,after',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:30720',
        ]);
        foreach ($r->file('files', []) as $f) {
            TicketMedia::create([
                'ticket_id' => $ticket->id, 'phase' => $r->phase,
                'kind' => str_starts_with($f->getMimeType(), 'video') ? 'video' : 'photo',
                'path' => $f->store("tickets/{$ticket->code}", 'public'),
            ]);
        }
        $ticket->log($r->phase === 'before' ? 'Foto sebelum diunggah' : 'Foto sesudah diunggah');
        $ticket->load('media')->recalcProgress();
        return back()->with('ok', 'Bukti berhasil diunggah.');
    }

    /** Tambahkan progress (catatan, tindakan, sparepart). */
    public function progress(Request $r, Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        $d = $r->validate(['note' => 'required|max:500', 'action' => 'nullable|max:120', 'spareparts' => 'nullable|max:120']);
        $ticket->update(array_filter(['action' => $d['action'] ?? null, 'spareparts' => $d['spareparts'] ?? null]));
        $ticket->log('Progress', $d['note']);
        return back()->with('ok', 'Progress dicatat.');
    }

    /** "Tandai Selesai" — wajib ada foto sesudah. */
    public function finish(Ticket $ticket)
    {
        $this->authorizeTicket($ticket);
        if (!$ticket->media()->where('phase', 'after')->exists()) {
            return back()->withErrors(['finish' => 'Foto/video sesudah wajib diisi sebelum menandai selesai.']);
        }
        $ticket->update(['status' => 'selesai', 'finished_at' => now()]);
        $ticket->machine->update(['status' => 'aktif', 'last_service_at' => now()]);
        $ticket->log('Selesai');
        $ticket->recalcProgress();
        return redirect()->route('teknisi.tugas.index', ['f' => 'selesai'])->with('ok', "{$ticket->code} selesai. Kerja bagus!");
    }

    private function authorizeTicket(Ticket $t): void
    {
        abort_unless($t->technician_id === auth()->id(), 403);
    }
}
