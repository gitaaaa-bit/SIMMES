<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function create()
    {
        return view('manajemen.pengguna.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'role'          => ['required', Rule::in(['teknisi', 'manajemen'])],
            'nik'           => ['required', 'string', 'max:30', 'unique:users,nik'],
            'password'      => ['required', 'string', 'min:4'],
            'email'         => ['required', 'email', 'max:255', 'unique:users,email'],
            'department'    => ['required', 'string', 'max:100'],
            'bagian'        => ['nullable', 'string', 'max:100'],
            'job_title'     => ['nullable', 'string', 'max:100'],
            'work_location' => ['nullable', 'string', 'max:100'],
            'whatsapp'      => ['nullable', 'string', 'max:20'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['status']   = $data['status'] ?? 'aktif';

        User::create($data);

        return $this->redirectBack($request, $data['role'], 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('manajemen.pengguna.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'role'          => ['required', Rule::in(['teknisi', 'manajemen'])],
            'nik'           => ['required', 'string', 'max:30', Rule::unique('users', 'nik')->ignore($user->id)],
            'password'      => ['nullable', 'string', 'min:4'],
            'email'         => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'department'    => ['required', 'string', 'max:100'],
            'bagian'        => ['nullable', 'string', 'max:100'],
            'job_title'     => ['nullable', 'string', 'max:100'],
            'work_location' => ['nullable', 'string', 'max:100'],
            'whatsapp'      => ['nullable', 'string', 'max:20'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $this->redirectBack($request, $data['role'], 'Data pengguna berhasil diperbarui.');
    }

    private function redirectBack(Request $request, string $role, string $message)
    {
        $route = ($request->input('from') === 'teknisi' || $role === 'teknisi')
            ? route('manajemen.teknisi.index')
            : route('manajemen.pengguna.index');

        return redirect($route)->with('success', $message);
    }
}