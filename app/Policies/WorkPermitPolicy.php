<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkPermit;
use App\Enums\Role;
use App\Models\WorkPermitForm;

class WorkPermitPolicy
{
    /**
     * Kullanıcı iş iznini görüntüleyebilir mi?
     */
    public function view(User $user, WorkPermitForm $workPermit): bool
    {
        // Admin her şeyi görebilir
        if ($user->isAdmin()) {
            return true;
        }

        // Aynı şirketten mi kontrol et
        if ($user->company_id !== $workPermit->company_id) {
            return false;
        }

        // İş iznini oluşturan kişi görebilir
        if ($workPermit->created_by === $user->id) {
            return true;
        }

        // Onaylayanlar görebilir
        $isApprover = $workPermit->approvals()
            ->where('user_id', $user->id)
            ->exists();

        if ($isApprover) {
            return true;
        }

        // Belirli roller görebilir
        return $user->isBirimAmiri() ||
               $user->isAlanAmiri() ||
               $user->isIsgUzmani() ||
               $user->isIsverenVekili();
    }

    /**
     * Kullanıcı iş izni oluşturabilir mi?
     */
    public function create(User $user): bool
    {
        // Admin ve tüm roller oluşturabilir
        return true;
    }

    /**
     * Kullanıcı iş iznini güncelleyebilir mi?
     */
    public function update(User $user, WorkPermit $workPermit): bool
    {
        // Admin her şeyi güncelleyebilir
        if ($user->isAdmin()) {
            return true;
        }

        // Sadece pending durumunda olan ve kendisinin oluşturduğu izinleri güncelleyebilir
        return $workPermit->created_by === $user->id
               && $workPermit->status === 'pending';
    }

    /**
     * Kullanıcı iş iznini silebilir mi?
     */
    public function delete(User $user, WorkPermit $workPermit): bool
    {
        // Admin her şeyi silebilir
        if ($user->isAdmin()) {
            return true;
        }

        // Sadece pending durumunda olan ve kendisinin oluşturduğu izinleri silebilir
        return $workPermit->created_by === $user->id
               && $workPermit->status === 'pending';
    }

    /**
     * Kullanıcı iş iznini onaylayabilir mi?
     */
    public function approve(User $user, WorkPermit $workPermit): bool
    {
        // Admin onaylayabilir
        if ($user->isAdmin()) {
            return true;
        }

        // Aynı şirketten değilse onaylayamaz
        if ($user->company_id !== $workPermit->company_id) {
            return false;
        }

        // Bekleyen onayı var mı kontrol et
        $hasPendingApproval = $workPermit->approvals()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        return $hasPendingApproval;
    }

    /**
     * Kullanıcı iş iznini reddedebilir mi?
     */
    public function reject(User $user, WorkPermit $workPermit): bool
    {
        // Onaylama ile aynı yetki
        return $this->approve($user, $workPermit);
    }
}
