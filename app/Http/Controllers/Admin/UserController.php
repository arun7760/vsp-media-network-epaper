<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);
        $roles = [
            'super_admin' => 'Super Admin (పూర్తి నియంత్రణ)',
            'admin' => 'Administrator (నిర్వాహకుడు)',
            'editor' => 'Editor (సంచిక అప్‌లోడ్ & ఎడిటింగ్)',
            'approver' => 'Approver (ఆమోదించే అధికారి)',
            'publisher' => 'Publisher (లైవ్ పబ్లిషర్)',
            'viewer' => 'Viewer (వీక్షణ మాత్రమే)',
        ];

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['super_admin', 'admin', 'editor', 'approver', 'reviewer', 'publisher', 'viewer'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        User::create($validated);

        return back()->with('success', "యూజర్ '{$validated['name']}' విజయవంతంగా సృష్టించబడ్డారు.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['super_admin', 'admin', 'editor', 'approver', 'reviewer', 'publisher', 'viewer'])],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', "యూజర్ '{$user->name}' వివరాలు అప్‌డేట్ చేయబడ్డాయి.");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'మీ స్వంత ఖాతాను మీరు తొలగించలేరు.']);
        }

        $name = $user->name;
        $user->delete();

        return back()->with('info', "యూజర్ '{$name}' తొలగించబడ్డారు.");
    }
}
