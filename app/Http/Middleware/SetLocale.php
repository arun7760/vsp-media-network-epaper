<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('lang') && in_array($request->query('lang'), ['en', 'te'], true)) {
            $chosen = $request->query('lang');
            Session::put('locale', $chosen);
            Session::put('admin_locale', $chosen);
        }

        if ($request->is('admin*') || $request->is('login')) {
            $locale = Session::get('admin_locale', Session::get('locale', 'en'));
        } else {
            $locale = Session::get('locale', 'te');
        }

        if (! in_array($locale, ['te', 'en'], true)) {
            $locale = ($request->is('admin*') || $request->is('login')) ? 'en' : 'te';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
