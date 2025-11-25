<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPosition extends Model
{
    protected $table = 'company_positions';

    protected $fillable = [
        'company_id',
        'name',
        'is_active'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
