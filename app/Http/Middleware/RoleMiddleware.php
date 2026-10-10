<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $roleValue = $user->role instanceof Role ? $user->role->value : (string) $user->role;
        if (!in_array($roleValue, $roles, true)) {
            abort(403, 'Anda tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
