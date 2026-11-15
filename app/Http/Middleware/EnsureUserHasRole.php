<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allRoles = [];
        foreach ($roles as $r) {
            foreach (explode('|', $r) as $sub) {
                $trimmed = trim($sub);
                if ($trimmed !== '') {
                    $allRoles[] = $trimmed;
                }
            }
        }

        if (! empty($allRoles) && ! $user->hasRole($allRoles)) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }
}
