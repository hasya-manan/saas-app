<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = \App\Models\User::withTrashed()->where('email', $request->email)->first();

        if ($user && $user->trashed()) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact your manager or HR for assistance.',
            ]);
        }
        $request->authenticate();
        $request->session()->regenerate();
        $user = $request->user();
        // Logic: If I am SuperAdmin, send me to the Admin Dashboard role (ID: 1)
       
        if ($user->role_id === 1) { 
        // SuperAdmin (Platform Owner)
        return redirect()->route('superadmin.dashboard');
        } 

        if ($user->role_id === 2) {
        return redirect()->route('admin_company.dashboard');        
        }

        //else, send me to the Tenant Dashboard
        if ($user->role_id === 3) {
            return redirect()->route('dashboard');
        }
       
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
