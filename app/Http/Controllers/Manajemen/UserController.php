<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('manajemen.pengguna.index', [
            'pending' => User::where('status', 'pending')->latest()->get(),
            'active'  => User::where('status', 'aktif')->orderBy('name')->get(),
        ]);
    }

    public function create() { return view('manajemen.pengguna.form'); }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|max:120', 'nik' => 'required|unique:users,nik|max:30',
            'department' => 'required|max:80', 'bagian' => 'nullable|max:80',
            'whatsapp' => 'nullable|max:20', 'role' => 'required|in:teknisi,manajemen',
        ]);
        // Kata sandi awal = NIK (minta pengguna menggantinya setelah login pertama).
        User::create($d + [
            'status' => 'aktif', 'password' => Hash::make($d['nik']),
            'email' => $d['nik'].'@simmes.local',   // hapus baris ini bila kolom email nullable / tidak dipakai
        ]);
        return redirect()->route('manajemen.pengguna.index')->with('ok', 'Pengguna ditambahkan (sandi awal = NIK).');
    }

    public function approve(Request $r, User $user)
    {
        $d = $r->validate(['role' => 'required|in:teknisi,manajemen']);
        $user->update(['role' => $d['role'], 'status' => 'aktif']);
        return back()->with('ok', "{$user->name} disetujui sebagai {$d['role']}.");
    }

    public function reject(User $user)
    {
        $user->update(['status' => 'ditolak']);
        return back()->with('ok', 'Pendaftaran ditolak.');
    }
}
