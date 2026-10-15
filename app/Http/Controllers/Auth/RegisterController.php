<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register', ['roles' => Role::cases()]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $roleEnum = Role::from($request->string('role')->toString());
        $prefix = $roleEnum === Role::Manajemen ? 'MGR' : 'TEK';

        // Auto-generate NIK unik sesuai peran (misal: TEK002, MGR002)
        $latest = User::where('nik', 'LIKE', "{$prefix}%")
            ->pluck('nik')
            ->map(function ($val) use ($prefix) {
                return (int) substr($val, strlen($prefix));
            })
            ->max();

        $nextNumber = ($latest && $latest >= 1) ? $latest + 1 : 1;
        $nik = sprintf('%s%03d', $prefix, $nextNumber);
        while (User::where('nik', $nik)->exists()) {
            $nextNumber++;
            $nik = sprintf('%s%03d', $prefix, $nextNumber);
        }

        // Format nomor WhatsApp jika memungkinkan
        $rawWa = trim($request->string('whatsapp')->toString());
        $cleanWa = \App\Services\WhatsAppService::normalizeNumber($rawWa) ?? $rawWa;

        $user = User::create([
            'name' => trim($request->string('name')->toString()),
            'nik' => $nik,
            'email' => strtolower(trim($request->string('email')->toString())),
            'whatsapp' => $cleanWa,
            'password' => $request->string('password')->toString(), // di-hash otomatis oleh cast
            'role' => $roleEnum,
            'status' => AccountStatus::Aktif, // langsung aktif, tanpa persetujuan manajemen
        ]);

        return redirect()
            ->route('login')
            ->with('status', "Pendaftaran berhasil! Akun kamu sudah aktif. Silakan masuk menggunakan Email ({$user->email}) dan kata sandi Anda.");
    }
}
