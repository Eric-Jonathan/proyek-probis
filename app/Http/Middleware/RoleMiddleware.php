<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
 * Handle an incoming request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure  $next
 * @param  string  ...$roles  <-- Dokumentasi PHPDoc untuk array of strings
 * @return \Symfony\Component\HttpFoundation\Response
 */
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    // $roles di sini akan otomatis menjadi array berisi string
    if (!in_array(Auth::user()->role, $roles)) {
        abort(403, 'Unauthorized role.');
    }

    return $next($request);
}
}
