<?php

namespace App\Http\Controllers;

class HomeRedirectController extends Controller
{
    /** Arahkan setelah login sesuai peran. */
    public function __invoke()
    {
        return redirect()->route(auth()->user()->isTeknisi() ? 'teknisi.home' : 'manajemen.dashboard');
    }
}
