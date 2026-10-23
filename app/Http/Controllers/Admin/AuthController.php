<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'మీ ఖాతా ప్రస్తుతం నిలిపివేయబడింది. అడ్మినిస్ట్రేటర్‌ను సంప్రదించండి.']);
            }

            $user->update(['last_login_at' => now()]);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'లాగిన్ విజయవంతమైంది. స్వాగతం, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'ఇచ్చిన వివరాలు సరిపోలలేదు. దయచేసి సరైన ఈమెయిల్ మరియు పాస్‌వర్డ్ నమోదు చేయండి.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'మీరు విజయవంతంగా లాగౌట్ అయ్యారు.');
    }
}
