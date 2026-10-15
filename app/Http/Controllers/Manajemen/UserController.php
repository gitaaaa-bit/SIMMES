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
            'name' => 'required|max:120',
            'nik' => 'nullable|unique:users,nik|max:30',
            'email' => 'nullable|email|unique:users,email|max:255',
            'department' => 'required|max:80',
            'bagian' => 'nullable|max:80',
            'whatsapp' => 'nullable|max:20',
            'role' => 'required|in:teknisi,manajemen',
        ]);

        // Jika NIK kosong / tidak diisi, buatkan otomatis oleh sistem (TEK001, MGR001, dst)
        $nik = !empty($d['nik']) ? trim($d['nik']) : User::nextNik($d['role']);
        $d['nik'] = $nik;

        $email = !empty($d['email']) ? strtolower(trim($d['email'])) : strtolower($nik) . '@simmes.local';
        $d['email'] = $email;

        // Kata sandi awal = NIK
        $user = User::create($d + [
            'status' => 'aktif',
            'password' => Hash::make($nik),
        ]);

        return redirect()->route('manajemen.pengguna.index')
            ->with('ok', "Pengguna {$user->name} berhasil ditambahkan dengan NIK {$user->nik} (Kata sandi awal = {$user->nik}).");
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
