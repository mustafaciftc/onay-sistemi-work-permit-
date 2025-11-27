<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property bool $is_active
 */

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function admins()
    {
        return $this->users()->wherePivot('role', 'admin');
    }

    public function workPermits()
    {
        return $this->hasMany(WorkPermitForm::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function positions()
    {
        return $this->hasManyThrough(Position::class, Department::class);
    }


    public function activeDepartments()
    {
        return $this->departments()->where('is_active', true);
    }
}
