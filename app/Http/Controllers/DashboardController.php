<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user && ($user->isTeknisi() || $user->role === Role::Teknisi || $user->role === 'teknisi')) {
            return redirect()->route('teknisi.home');
        }

        return redirect()->route('manajemen.dashboard');
    }
}
