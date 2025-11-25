<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Enums\Role;

class AdminController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        // Temel istatistikler
        $totalUsers = User::count();
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        $inactiveCompanies = Company::where('is_active', false)->count();

        // İş izni istatistikleri
        $totalWorkPermits = \App\Models\WorkPermitForm::count();
        $pendingApprovals = \App\Models\WorkPermitForm::where('status', 'pending')->count();

        // Son kullanıcılar (opsiyonel)
        $recentUsers = User::latest()->take(5)->get();

        // Son şirketler (opsiyonel)
        $recentCompanies = Company::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCompanies',
            'activeCompanies',
            'inactiveCompanies',
            'totalWorkPermits',
            'pendingApprovals',
            'recentUsers',
            'recentCompanies'
        ));
    }

    // Kullanıcı Listesi
    public function users()
    {
        $users = User::with('company')->paginate(15);
        return view('admin.users', compact('users'));
    }

    // Yeni Kullanıcı Oluştur
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:' . implode(',', array_column(Role::cases(), 'value')),
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'company_id' => auth()->user()->company_id, // Admin'in şirketi
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu');
    }

    // Kullanıcı Bilgilerini Getir (AJAX için)
    public function getUserForEdit($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => is_string($user->role) ? $user->role : $user->role->value,
            ]
        ]);
    }

    // Kullanıcı Güncelle
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|in:' . implode(',', array_column(Role::cases(), 'value')),
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi');
    }

    // Kullanıcı Sil
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Kendi hesabını silemesin
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Kendi hesabınızı silemezsiniz!']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi');
    }

    // Şirket Listesi
    public function companies()
    {
        $companies = Company::withCount('users')->paginate(15);
        return view('admin.companies', compact('companies'));
    }

    // Yeni Şirket Oluştur
    public function createCompany(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        Company::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Şirket başarıyla oluşturuldu');
    }

    // Şirket Bilgilerini Getir
    public function getCompanyForEdit($id)
    {
        $company = Company::findOrFail($id);

        return response()->json([
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'email' => $company->email,
                'phone' => $company->phone,
                'address' => $company->address,
                'is_active' => $company->is_active,
            ]
        ]);
    }

    // Şirket Güncelle
    public function updateCompany(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $company->update($validated);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Şirket başarıyla güncellendi');
    }

    // Şirket Durumunu Değiştir (Aktif/Pasif)
    public function toggleCompanyStatus($id)
    {
        $company = Company::findOrFail($id);
        $company->update(['is_active' => !$company->is_active]);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Şirket durumu güncellendi');
    }
}
