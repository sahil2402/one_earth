<?php

namespace App\Http\Controllers\Auth;

use App\Constants\OwnerCredentials;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (session()->has('authenticated_role')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $emailMatches = hash_equals(OwnerCredentials::EMAIL, $credentials['email']);
        $passwordMatches = Hash::check($credentials['password'], Hash::make(OwnerCredentials::PASSWORD));

        if ($emailMatches && $passwordMatches) {
            $request->session()->regenerate();
            $request->session()->put([
                'authenticated_role' => OwnerCredentials::ROLE,
                'authenticated_email' => OwnerCredentials::EMAIL,
            ]);
            return redirect()->intended(route('dashboard'));
        }

        // Check if database user exists
        $user = \App\Models\User::with('role')->where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if (!$user->is_active) {
                return back()->withInput($request->only('email'))->withErrors([
                    'email' => 'Your account is deactivated.',
                ]);
            }

            $request->session()->regenerate();
            $request->session()->put([
                'authenticated_role' => $user->role?->name ?? 'user',
                'authenticated_email' => $user->email,
            ]);
            return redirect()->intended(route('dashboard'));
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'The provided login details are incorrect.',
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
