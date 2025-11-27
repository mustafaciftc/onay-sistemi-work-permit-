<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'is_active',
        'approval_workflow'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'approval_workflow' => 'array'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function activePositions(): HasMany
    {
        return $this->positions()->where('is_active', true);
    }

    public function workPermits(): HasMany
    {
        return $this->hasMany(WorkPermitForm::class);
    }

    public function getApproverForStep(string $step): ?User
    {
        $roleMap = [
            'unit_manager'           => 'birim_amiri',
            'area_manager'            => 'alan_amiri',
            'safety_specialist'      => 'isg_uzmani',
            'employer_representative' => 'isveren_vekili',
        ];

        $role = $roleMap[$step] ?? null;

        if (!$role) return null;

        return User::where('company_id', $this->company_id)
            ->where('role', $role)
            ->where('department_id', $this->id)
            ->first();
    }
}
