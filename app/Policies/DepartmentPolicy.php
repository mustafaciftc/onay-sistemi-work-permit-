<?php

namespace App\Policies;

use App\Models\CompanyDepartment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function view(User $user, CompanyDepartment $department)
    {
        return $department->company->users->contains($user->id);
    }

    public function update(User $user, CompanyDepartment $department)
    {
        return $department->company->users->contains($user->id) &&
            $user->hasRoleInCompany($department->company_id, 'admin');
    }

    public function delete(User $user, CompanyDepartment $department)
    {
        return $department->company->users->contains($user->id) &&
            $user->hasRoleInCompany($department->company_id, 'admin');
    }
}
