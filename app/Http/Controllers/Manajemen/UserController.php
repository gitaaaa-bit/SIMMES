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
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'nullable|string|min:4|max:64',
            'department' => 'required|max:80',
            'bagian' => 'nullable|max:80',
            'job_title' => 'nullable|max:100',
            'work_location' => 'nullable|max:120',
            'whatsapp' => 'nullable|max:25',
            'role' => 'required|in:teknisi,manajemen',
        ]);

        // Jika NIK kosong / tidak diisi, buatkan otomatis oleh sistem (TEK001, MGR001, dst)
        $nik = !empty($d['nik']) ? strtoupper(trim($d['nik'])) : User::nextNik($d['role']);
        $d['nik'] = $nik;

        $email = strtolower(trim($d['email']));
        $d['email'] = $email;
        if (!empty($d['whatsapp'])) {
            $d['whatsapp'] = \App\Services\WhatsAppService::normalizeNumber($d['whatsapp']) ?? $d['whatsapp'];
        }

        $plainPassword = !empty($d['password']) ? trim($d['password']) : $nik;

        // Kata sandi awal = password ditentukan atau NIK
        unset($d['password']);
        $user = User::create($d + [
            'status' => 'aktif',
            'password' => Hash::make($plainPassword),
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('manajemen.pengguna.index')
            ->with('ok', "Pengguna {$user->name} berhasil ditambahkan dengan NIK {$user->nik} dan langsung aktif (Password: {$plainPassword}).");
    }

    public function edit(User $user)
    {
        return view('manajemen.pengguna.form', compact('user'));
    }

    public function update(Request $r, User $user)
    {
        $d = $r->validate([
            'name' => 'required|max:120',
            'nik' => 'required|max:30|unique:users,nik,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:4|max:64',
            'department' => 'required|max:80',
            'bagian' => 'nullable|max:80',
            'job_title' => 'nullable|max:100',
            'work_location' => 'nullable|max:120',
            'whatsapp' => 'nullable|max:25',
            'role' => 'required|in:teknisi,manajemen',
        ]);

        $d['nik'] = strtoupper(trim($d['nik']));
        $d['email'] = strtolower(trim($d['email']));
        if (!empty($d['whatsapp'])) {
            $d['whatsapp'] = \App\Services\WhatsAppService::normalizeNumber($d['whatsapp']) ?? $d['whatsapp'];
        }
        if (blank($d['password'])) {
            unset($d['password']);
        } else {
            $d['password'] = Hash::make($d['password']);
        }

        $user->update($d);
        return redirect()->route('manajemen.pengguna.index')->with('ok', "Data kerja {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        abort_if($user->is(auth()->user()), 422, 'Akun yang sedang digunakan tidak dapat dihapus.');
        $name = $user->name;
        $user->delete();

        return back()->with('ok', "Pengguna {$name} berhasil dihapus.");
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
