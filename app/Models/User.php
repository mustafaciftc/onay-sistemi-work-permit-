<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'department_id',
        'position_id',
        'is_active',
        'email_verified_at'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,  // ENUM CASTING
        ];
    }


    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Varsayılan rol — Sadece admin bırakıldı
     */
    const DEFAULT_ROLE = Role::CALISAN;

    /**
     * Rol açıklamaları
     */
    public static function getRoles(): array
    {
        return [
            Role::ADMIN->value            => 'Yönetici',
            Role::CALISAN->value          => 'Çalışan',
            Role::BIRIM_AMIRI->value      => 'Birim Amiri',
            Role::ALAN_AMIRI->value       => 'Alan Amiri',
            Role::ISG_UZMANI->value       => 'İSG Uzmanı',
            Role::ISVEREN_VEKILI->value   => 'İşveren Vekili',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::getRoles()[$this->role->value] ?? 'Tanımsız';
    }

    /**
     * Tekil rol kontrolü
     */
    public function hasRole($role): bool
    {
        // String geldiyse enum'a çevir
        if (is_string($role)) {
            $role = Role::from($role);
        }

        // Enum instance geldiyse
        if ($role instanceof Role) {
            return $this->role->value === $role->value;
        }

        return false;
    }

    /**
     * Shortcut role methods
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }
    public function isCalisan(): bool
    {
        return $this->hasRole(Role::CALISAN);
    }
    public function isBirimAmiri(): bool
    {
        return $this->hasRole(Role::BIRIM_AMIRI);
    }
    public function isAlanAmiri(): bool
    {
        return $this->hasRole(Role::ALAN_AMIRI);
    }
    public function isIsgUzmani(): bool
    {
        return $this->hasRole(Role::ISG_UZMANI);
    }
    public function isIsverenVekili(): bool
    {
        return $this->hasRole(Role::ISVEREN_VEKILI);
    }

    /**
     * Varsayılan rol ataması
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->role) {
                $user->role = self::DEFAULT_ROLE;
            }
        });
    }

    /**
     * Kullanıcının ana firması
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Kullanıcının aktif firması (session)
     */
    public function currentCompany()
    {
        // Admin ise seçilebilir company mantığı
        if ($this->isAdmin()) {
            $companyId = session('current_company_id');

            return $companyId
                ? Company::find($companyId)
                : Company::first();
        }

        // Admin olmayanlar → kendi firmasını kullanır
        return $this->company;
    }

    /**
     * Admin → Firma değiştirme
     */
    public function switchCompany($companyId): void
    {
        if (!$this->isAdmin()) {
            throw new \Exception("Sadece admin firma değiştirebilir.");
        }

        session(['current_company_id' => $companyId]);
    }

    /**
     * Kullanıcı rol değişimi (sadece admin kullanır)
     */
    public function changeRole(Role $newRole): bool
    {
        $this->role = $newRole;
        return $this->save();
    }
}
