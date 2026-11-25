<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    public function showLuminousLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->company?->code === 'LUMINOUS'
                ? redirect()->route('luminous.invoices.index')
                : redirect()->route('login');
        }

        return view('auth.luminous-login');
    }

    public function luminousLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $companyId = Company::idFor('LUMINOUS');

        abort_unless($companyId, 500, 'Company Luminous belum disiapkan.');

        if (Auth::attempt($credentials + ['company_id' => $companyId], $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('luminous.invoices.index'));
        }

        throw ValidationException::withMessages([
            'email' => 'Akun tidak memiliki akses Luminous atau kredensial tidak sesuai.',
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        $redirect = Auth::user()?->company?->code === 'LUMINOUS' ? 'luminous.login' : 'login';
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($redirect);
    }
}
