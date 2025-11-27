<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Enums\Role;

class AdminController extends Controller
{
    // ==================== DASHBOARD ====================

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        $inactiveCompanies = Company::where('is_active', false)->count();
        $totalWorkPermits = \App\Models\WorkPermitForm::count();
        $pendingApprovals = \App\Models\WorkPermitForm::where('status', 'pending')->count();

        $recentUsers = User::latest()->take(5)->get();
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

    // ==================== KULLANICI YÖNETİMİ ====================

    public function users()
    {
        $users = User::with('company')->paginate(15);
        return view('admin.users', compact('users'));
    }

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
            'company_id' => auth()->user()->company_id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu');
    }

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

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Kendi hesabınızı silemezsiniz!']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi');
    }

    // ==================== ŞİRKET YÖNETİMİ ====================

    /**
     * Şirketler listesi
     */
    public function companies()
    {
        $companies = Company::withCount(['users', 'departments', 'workPermits'])
            ->latest()
            ->paginate(10);

        return view('admin.companies', compact('companies'));
    }

    /**
     * Şirket oluştur - OTOMATIK DEPARTMAN VE POZİSYON EKLER
     */
    public function createCompany(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Şirket adı zorunludur.',
            'email.required' => 'Email adresi zorunludur.',
            'email.email' => 'Geçerli bir email adresi giriniz.',
            'email.unique' => 'Bu email adresi zaten kullanılıyor.',
        ]);

        return DB::transaction(function () use ($validated) {
            try {
                // 1️⃣ Şirketi Oluştur
                $company = Company::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'is_active' => true,
                ]);

                Log::info('✅ Şirket oluşturuldu', [
                    'company_id' => $company->id,
                    'company_name' => $company->name
                ]);

                // 2️⃣ Varsayılan Departmanları ve Pozisyonları Oluştur
                $stats = $this->createDefaultDepartments($company);

                Log::info('🎉 Şirket kurulumu tamamlandı', [
                    'company_id' => $company->id,
                    'departments_count' => $stats['departments'],
                    'positions_count' => $stats['positions']
                ]);

                return redirect()->route('admin.companies.index')
                    ->with('success', "✅ {$company->name} başarıyla oluşturuldu! {$stats['departments']} departman ve {$stats['positions']} pozisyon otomatik eklendi.");

            } catch (\Exception $e) {
                Log::error('❌ Şirket oluşturma hatası: ' . $e->getMessage(), [
                    'exception' => $e->getTraceAsString()
                ]);

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Şirket oluşturulurken bir hata oluştu: ' . $e->getMessage());
            }
        });
    }

    /**
     * Şirket bilgilerini getir (AJAX - Modal için)
     */
    public function getCompanyForEdit(Company $company)
    {
        return response()->json([
            'success' => true,
            'company' => $company
        ]);
    }

    /**
     * Şirket güncelle
     */
    public function updateCompany(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $company->update($validated);

        Log::info('✏️ Şirket güncellendi', [
            'company_id' => $company->id,
            'company_name' => $company->name
        ]);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Şirket başarıyla güncellendi!');
    }

    /**
     * Şirket sil (Soft Delete)
     */
    public function deleteCompany(Company $company)
    {
        try {
            // Şirkete bağlı kullanıcı sayısını kontrol et
            $userCount = $company->users()->count();
            $departmentCount = $company->departments()->count();

            if ($userCount > 0) {
                return redirect()->back()
                    ->with('error', "Bu şirkete bağlı {$userCount} kullanıcı var. Önce kullanıcıları silin veya başka şirkete aktarın.");
            }

            // Departmanları ve pozisyonları sil (cascade)
            foreach ($company->departments as $department) {
                $department->positions()->delete();
                $department->delete();
            }

            // Şirketi sil
            $company->delete();

            Log::info('🗑️ Şirket silindi', [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'deleted_departments' => $departmentCount
            ]);

            return redirect()->route('admin.companies.index')
                ->with('success', "✅ Şirket başarıyla silindi! {$departmentCount} departman ve tüm pozisyonlar da kaldırıldı.");

        } catch (\Exception $e) {
            Log::error('❌ Şirket silme hatası: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Şirket silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Şirket durumunu aktif/pasif yap
     */
    public function toggleCompanyStatus(Company $company)
    {
        try {
            $company->is_active = !$company->is_active;
            $company->save();

            $status = $company->is_active ? 'aktif' : 'pasif';

            Log::info('🔄 Şirket durumu değiştirildi', [
                'company_id' => $company->id,
                'new_status' => $status
            ]);

            return redirect()->back()
                ->with('success', "Şirket durumu {$status} olarak güncellendi.");

        } catch (\Exception $e) {
            Log::error('❌ Şirket durum değiştirme hatası: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Durum güncellenirken bir hata oluştu.');
        }
    }

    /**
     * Varsayılan departmanları ve pozisyonları oluştur
     */
    private function createDefaultDepartments(Company $company): array
    {
        $defaultDepartments = [
            'Üretim' => [
                'Operatör',
                'Teknisyen',
                'Formen',
                'Üretim Mühendisi',
                'Vardiya Amiri',
                'Kalite Kontrol Teknisyeni',
            ],
            'Bakım-Onarım' => [
                'Bakım Teknisyeni',
                'Elektrikçi',
                'Mekanik Teknisyen',
                'Elektronik Teknisyeni',
                'Bakım Mühendisi',
                'Otomasyon Teknisyeni',
            ],
            'Kalite Kontrol' => [
                'Kalite Teknisyeni',
                'Kalite Müfettişi',
                'Laboratuvar Görevlisi',
                'Kalite Mühendisi',
                'Metroloji Teknisyeni',
            ],
            'Depo ve Lojistik' => [
                'Depo Görevlisi',
                'Forklift Operatörü',
                'Depo Amiri',
                'Sevkiyat Sorumlusu',
                'Lojistik Koordinatörü',
            ],
            'İnsan Kaynakları' => [
                'İK Uzmanı',
                'İK Müdürü',
                'Bordro Uzmanı',
                'İşe Alım Uzmanı',
            ],
            'Yönetim' => [
                'Yönetici',
                'Koordinatör',
                'Uzman',
                'Müdür Yardımcısı',
                'Genel Müdür',
            ],
            'İş Sağlığı ve Güvenliği' => [
                'İSG Uzmanı',
                'İSG Teknisyeni',
                'İşyeri Hekimi',
                'Acil Müdahale Ekibi',
            ],
            'Satın Alma' => [
                'Satın Alma Uzmanı',
                'Satın Alma Müdürü',
                'Tedarik Zinciri Uzmanı',
            ],
        ];

        $departmentCount = 0;
        $positionCount = 0;

        foreach ($defaultDepartments as $deptName => $positions) {
            $department = Department::create([
                'name' => $deptName,
                'company_id' => $company->id,
                'is_active' => true,
                'birim_amiri_id' => null,
                'alan_amiri_id' => null,
                'isg_uzmani_id' => null,
                'isveren_vekili_id' => null,
            ]);

            $departmentCount++;

            foreach ($positions as $positionName) {
                Position::create([
                    'name' => $positionName,
                    'department_id' => $department->id,
                    'is_active' => true,
                ]);
                $positionCount++;
            }

            Log::info('📂 Departman oluşturuldu', [
                'department' => $deptName,
                'positions_count' => count($positions)
            ]);
        }

        return [
            'departments' => $departmentCount,
            'positions' => $positionCount
        ];
    }
}
