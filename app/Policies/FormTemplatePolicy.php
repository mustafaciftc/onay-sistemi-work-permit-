<?php

namespace App\Policies;

use App\Models\FormTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormTemplatePolicy
{
    use HandlesAuthorization;

    public function view(User $user, FormTemplate $template)
    {
        return $template->company->users->contains($user->id);
    }

    public function update(User $user, FormTemplate $template)
    {
        return $template->company->users->contains($user->id) &&
            $user->hasRoleInCompany($template->company_id, 'admin');
    }

    public function delete(User $user, FormTemplate $template)
    {
        return $template->company->users->contains($user->id) &&
            $user->hasRoleInCompany($template->company_id, 'admin') &&
            !$template->is_default;
    }
}
