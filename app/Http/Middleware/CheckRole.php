<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            auth()->logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'మీ ఖాతా లాగిన్ చేయబడలేదు లేదా నిలిపివేయబడింది.']);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'అనధికారిక ప్రవేశం: ఈ విభాగాన్ని యాక్సెస్ చేయడానికి మీకు అనుమతి లేదు. (Unauthorized).');
        }

        return $next($request);
    }
}
