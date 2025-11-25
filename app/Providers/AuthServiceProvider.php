<?php

namespace App\Providers;

use App\Models\CompanyDepartment;
use App\Models\WorkPermitForm;
use App\Policies\DepartmentPolicy;
use App\Policies\WorkPermitPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        CompanyDepartment::class => DepartmentPolicy::class,
        WorkPermitForm::class => WorkPermitPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
