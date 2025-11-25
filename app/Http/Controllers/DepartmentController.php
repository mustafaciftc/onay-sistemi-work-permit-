<?php

namespace App\Http\Controllers;

use App\Models\CompanyDepartment;
use App\Models\Company;
use App\Models\DepartmentPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->companies->first();

        if (!$company) {
            return redirect()->route('admin.dashboard')->with('error', 'Şirket bulunamadı.');
        }

        $departments = $company->departments()->withCount(['formTemplates', 'workPermits'])->get();

        return view('admin.departments.index', compact('departments', 'company'));
    }

    public function create()
    {
        $user = Auth::user();
        $company = $user->companies->first();

        if (!$company) {
            return redirect()->route('admin.dashboard')->with('error', 'Şirket bulunamadı.');
        }

        return view('admin.departments.create', compact('company'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $company = $user->companies->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'approval_workflow' => 'nullable|array',
            'users' => 'nullable|array',
            'users.*.user_id' => 'required|exists:users,id',
            'users.*.role' => 'required|string',
        ]);

        // Departmanı oluştur
        $department = CompanyDepartment::create([
            'company_id' => $company->id,
            'name' => $validated['name'],
            'approval_workflow' => $validated['approval_workflow'] ?? (new CompanyDepartment())->getDefaultWorkflow(),
        ]);

        // Kullanıcıları departmana ata
        if (isset($validated['users'])) {
            foreach ($validated['users'] as $userData) {
                $department->users()->attach($userData['user_id'], [
                    'role' => $userData['role']
                ]);
            }
        }

        return redirect()->route('departments.show', $department)
            ->with('success', 'Departman başarıyla oluşturuldu.');
    }

// DepartmentController - positions metodunu kontrol et
public function positions($department)
{
    try {
        Log::info("Pozisyon isteği - Departman ID: {$department}");

        $department = CompanyDepartment::with('positions')->find($department);

        if (!$department) {
            return response()->json(['error' => 'Departman bulunamadı'], 404);
        }

        Log::info("Pozisyonlar: " . $department->positions->toJson());

        return response()->json($department->positions);

    } catch (\Exception $e) {
        Log::error('Hata: ' . $e->getMessage());
        return response()->json(['error' => 'Sunucu hatası'], 500);
    }
}


    public function show(CompanyDepartment $department)
    {
        $this->authorize('view', $department);

        $department->load(['users', 'formTemplates', 'workPermits' => function ($query) {
            $query->latest()->take(10);
        }]);

        return view('admin.departments.show', compact('department'));
    }

    public function edit(CompanyDepartment $department)
    {
        $this->authorize('update', $department);

        $companyUsers = $department->company->users;

        return view('admin.departments.edit', compact('department', 'companyUsers'));
    }

    public function update(Request $request, CompanyDepartment $department)
    {
        $this->authorize('update', $department);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'approval_workflow' => 'nullable|array',
            'users' => 'nullable|array',
            'users.*.user_id' => 'required|exists:users,id',
            'users.*.role' => 'required|string',
        ]);

        $department->update([
            'name' => $validated['name'],
            'approval_workflow' => $validated['approval_workflow'] ?? $department->approval_workflow,
        ]);

        // Kullanıcıları güncelle
        if (isset($validated['users'])) {
            $department->users()->detach();

            foreach ($validated['users'] as $userData) {
                $department->users()->attach($userData['user_id'], [
                    'role' => $userData['role']
                ]);
            }
        }

        return redirect()->route('admin.departments.show', $department)
            ->with('success', 'Departman başarıyla güncellendi.');
    }

    public function destroy(CompanyDepartment $department)
    {
        $this->authorize('delete', $department);

        // Departmanda iş izni varsa silme
        if ($department->workPermits()->count() > 0) {
            return redirect()->back()->with('error', 'Bu departmanda iş izinleri bulunuyor. Önce iş izinlerini silmelisiniz.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Departman başarıyla silindi.');
    }
}
