<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Company;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['company', 'positions'])
            ->withCount('positions')
            ->withCount('workPermits')
            ->orderBy('company_id')
            ->orderBy('name')
            ->paginate(20);

        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('admin.departments', compact('departments', 'companies'));
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'company_id' => 'required|exists:companies,id',
                'name' => 'required|string|max:255|unique:departments,name,NULL,id,company_id,' . $request->company_id,
                'description' => 'nullable|string|max:1000',
                'is_active' => 'sometimes|boolean',
            ]);

            $department = Department::create([
                'company_id' => $validated['company_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->boolean('is_active', true),
                'approval_workflow' => ['unit_manager', 'area_manager', 'safety_specialist', 'employer_representative']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Departman başarıyla oluşturuldu',
                'department' => $department
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Departman oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Department $department): JsonResponse
    {
        return response()->json([
            'department' => $department->load('company')
        ]);
    }

    public function update(Request $request, Department $department): JsonResponse
    {
        try {
            $validated = $request->validate([
                'company_id' => 'required|exists:companies,id',
                'name' => 'required|string|max:255|unique:departments,name,' . $department->id . ',id,company_id,' . $request->company_id,
                'description' => 'nullable|string|max:1000',
                'is_active' => 'sometimes|boolean',
            ]);

            $department->update([
                'company_id' => $validated['company_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Departman başarıyla güncellendi',
                'department' => $department
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Departman güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Department $department): JsonResponse
    {
        try {
            if ($department->workPermits()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu departmanda iş izinleri bulunuyor, silinemez!'
                ], 422);
            }

            if ($department->positions()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu departmanda pozisyonlar bulunuyor, önce pozisyonları silin!'
                ], 422);
            }

            $department->delete();

            return response()->json([
                'success' => true,
                'message' => 'Departman başarıyla silindi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Silme işlemi sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function positions(Department $department)
    {
        $positions = $department->positions()
            ->where('is_active', 1)
            ->select('id', 'name')
            ->get();

        return response()->json($positions);
    }

    public function getDepartmentPositions(Department $department): JsonResponse
    {
        try {
            $positions = $department->positions()
                ->where('is_active', true)
                ->select('id', 'name')
                ->get();

            return response()->json($positions);
        } catch (\Exception $e) {
            Log::error('Positions fetch error:', ['error' => $e->getMessage()]);
            return response()->json([], 500);
        }
    }

    public function toggleStatus(Department $department): JsonResponse
    {
        try {
            $department->update(['is_active' => !$department->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Departman durumu güncellendi',
                'is_active' => $department->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Durum güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
