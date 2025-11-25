<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDepartment extends Model
{

    protected $table = 'company_departments';

    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'is_active',
        'unit_manager_id',
        'area_manager_id',
        'safety_specialist_id',
        'employer_representative_id'
    ];

    // İlişkiler
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function unitManager()
    {
        return $this->belongsTo(User::class, 'unit_manager_id');
    }

    public function areaManager()
    {
        return $this->belongsTo(User::class, 'area_manager_id');
    }

    public function safetySpecialist()
    {
        return $this->belongsTo(User::class, 'safety_specialist_id');
    }

    public function employerRepresentative()
    {
        return $this->belongsTo(User::class, 'employer_representative_id');
    }

    public function positions()
    {
        return $this->hasMany(DepartmentPosition::class, 'department_id');
    }

    public function workPermits()
    {
        return $this->hasMany(WorkPermitForm::class);
    }

    /**
     * Adım için onaycıyı getir
     */
    public function getApproverForStep($step)
    {
        $columnMap = [
            'unit_manager' => 'unit_manager_id',
            'area_manager' => 'area_manager_id',
            'safety_specialist' => 'safety_specialist_id',
            'employer_representative' => 'employer_representative_id'
        ];

        $column = $columnMap[$step] ?? null;

        if (!$column || !$this->$column) {
            return null;
        }

        return User::find($this->$column);
    }
}
