<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Company;

class AuthController extends Controller
{
    // Login Form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Rol bazlı yönlendirme
            return $this->redirectByRole($user);
        }

        return back()->withErrors([
            'email' => 'Girilen bilgiler hatalı.',
        ])->onlyInput('email');
    }

    // Register Form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email',
            'company_phone' => 'nullable|string',
            'company_address' => 'nullable|string|max:500',
        ]);

        $user = DB::transaction(function () use ($validated) {

            // Şirket oluştur
            $company = Company::create([
                'name' => $validated['company_name'],
                'email' => $validated['company_email'],
                'phone' => $validated['company_phone'],
                'address' => $validated['company_address'] ?? null,
                'is_active' => true,
            ]);

            // Kullanıcı oluşturuyoruz
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'admin',   // ENUM rol atanıyor
                'company_id' => $company->id, // basit ilişki
            ]);

            return $user;
        });

        Auth::login($user);

        return $this->redirectByRole($user);
    }

    private function redirectByRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isBirimAmiri()) {
            return redirect()->route('company.birim-amiri');
        }

        if ($user->isAlanAmiri()) {
            return redirect()->route('company.alan-amiri');
        }

        if ($user->isIsgUzmani()) {
            return redirect()->route('company.isg-uzmani');
        }

        if ($user->isIsverenVekili()) {
            return redirect()->route('company.isveren-vekili');
        }

        // Default
        return redirect()->route('company.calisan');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
