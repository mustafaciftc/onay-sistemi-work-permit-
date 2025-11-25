<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentPosition extends Model
{
    protected $fillable = ['department_id', 'name'];

    public function department()
    {
        return $this->belongsTo(CompanyDepartment::class, 'department_id');
    }
}
