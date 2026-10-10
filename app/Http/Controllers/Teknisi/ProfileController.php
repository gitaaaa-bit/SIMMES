<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $u = auth()->user();
        $total = Ticket::where('technician_id', $u->id)->where('status', 'selesai')->count();
        return view('teknisi.profil', compact('u', 'total'));
    }

    public function update(Request $r)
    {
        auth()->user()->update($r->validate(['whatsapp' => 'nullable|max:20', 'bagian' => 'nullable|max:80']));
        return back()->with('ok', 'Profil diperbarui.');
    }
}
