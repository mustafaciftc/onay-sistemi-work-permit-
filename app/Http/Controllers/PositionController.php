<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $position = Position::create([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pozisyon başarıyla oluşturuldu',
            'position' => $position
        ]);
    }

    public function edit(Position $position): JsonResponse
    {
        return response()->json([
            'success' => true,
            'position' => $position
        ]);
    }

    public function update(Request $request, Position $position): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $position->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pozisyon başarıyla güncellendi',
            'position' => $position
        ]);
    }

    public function destroy(Position $position): JsonResponse
    {
        if ($position->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu pozisyonda kullanıcılar bulunuyor, silinemez!'
            ], 422);
        }

        if ($position->workPermits()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu pozisyonda iş izinleri bulunuyor, silinemez!'
            ], 422);
        }

        $position->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pozisyon başarıyla silindi'
        ]);
    }

    public function toggleStatus(Position $position): JsonResponse
    {
        $position->update(['is_active' => !$position->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Pozisyon durumu güncellendi',
            'is_active' => $position->is_active
        ]);
    }
}
