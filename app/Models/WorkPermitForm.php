<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'department_id',
        'position_id',
        'form_template_id',
        'created_by',
        'title',
        'work_type',
        'work_description',
        'location',
        'risks',
        'control_measures',
        'start_date',
        'end_date',
        'status',
        'worker_name',
        'worker_position',
        'tools_equipment',
        'emergency_procedures',
        'energy_cut_off',
        'area_cleaned',
        'no_conflict_with_other_works',
        'area_manager_notes',
        'area_manager_approved_at',
        'gas_measurement_done',
        'ppe_checked',
        'additional_procedures_verified',
        'safety_specialist_notes',
        'safety_specialist_approved_at',
        'work_completed',
        'equipment_collected',
        'emergency_equipment_closed',
        'fire_risk_eliminated',
        'cleaning_done',
        'closing_photos',
        'employer_representative_approved_at',
        'closed_at',
        'area_manager_id',
        'safety_specialist_id',
        'employer_representative_id',
        'pdf_path',
        'rejection_reason',
        'permit_number',
        'permit_code',
        'unit_manager_approved_at',
        'unit_manager_comments',
        'area_manager_comments',
        'safety_specialist_comments',
        'employer_representative_comments',
        'final_approval_at',
        'pdf_path',
        'final_pdf_path'
    ];

    protected $casts = [
        'risks' => 'array',
        'control_measures' => 'array',
        'closing_photos' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'area_manager_approved_at' => 'datetime',
        'safety_specialist_approved_at' => 'datetime',
        'employer_representative_approved_at' => 'datetime',
        'closed_at' => 'datetime',
        'energy_cut_off' => 'boolean',
        'area_cleaned' => 'boolean',
        'no_conflict_with_other_works' => 'boolean',
        'gas_measurement_done' => 'boolean',
        'ppe_checked' => 'boolean',
        'additional_procedures_verified' => 'boolean',
        'work_completed' => 'boolean',
        'equipment_collected' => 'boolean',
        'emergency_equipment_closed' => 'boolean',
        'fire_risk_eliminated' => 'boolean',
        'cleaning_done' => 'boolean',
        'unit_manager_approved_at' => 'datetime',
        'final_approval_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workPermit) {
            // Firma bazlı otomatik numaralama
            $lastPermit = static::where('company_id', $workPermit->company_id)
                ->latest('id')
                ->first();

            $nextNumber = $lastPermit ? $lastPermit->permit_number + 1 : 1;

            $workPermit->permit_number = $nextNumber;

            $companyPrefix = 'IZN';
            if ($workPermit->company && $workPermit->company->name) {
                $companyPrefix = strtoupper(substr($workPermit->company->name, 0, 3));
            }

            $workPermit->permit_code = sprintf(
                '%s-%s-%04d',
                $companyPrefix,
                now()->format('Ymd'),
                $nextNumber
            );
        });
    }

    // ==================== RELATIONSHIPS ====================

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(CompanyDepartment::class, 'department_id');
    }

    public function position()
    {
        return $this->belongsTo(DepartmentPosition::class, 'position_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function approvals()
    {
        return $this->hasMany(WorkPermitApproval::class, 'work_permit_id');
    }

    public function formTemplate()
    {
        return $this->belongsTo(FormTemplate::class);
    }

    // ==================== SCOPES ====================

    public function scopePendingApproval($query)
    {
        return $query->whereIn('status', [
            'pending_unit_approval',
            'pending_area_approval',
            'pending_safety_approval',
            'pending_employer_approval'
        ]);
    }

    public function scopeClosingProcess($query)
    {
        return $query->whereIn('status', [
            'closing_requested',
            'pending_area_closing',
            'pending_safety_closing',
            'pending_employer_closing'
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
            ->where('end_date', '>=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'approved')
            ->where('end_date', '<', now());
    }

    // ==================== ATTRIBUTES ====================

    /**
     * Mevcut onay adımını getir
     */
    public function getCurrentStepAttribute()
    {
        return $this->approvals()
            ->where('status', 'pending')
            ->orderBy('id')
            ->first();
    }

    /**
     * Durum metni
     */
    public function getStatusTextAttribute()
    {
        $statusMap = [
            // Açılış
            'pending_unit_approval' => 'Birim Amiri Onayı Bekliyor',
            'pending_area_approval' => 'Alan Amiri Onayı Bekliyor',
            'pending_safety_approval' => 'İSG Uzmanı Onayı Bekliyor',
            'pending_employer_approval' => 'İşveren Vekili Onayı Bekliyor',
            'approved' => '✅ Onaylandı - Çalışma Devam Ediyor',

            // Kapanış
            'closing_requested' => 'Kapatma Talebi Gönderildi',
            'pending_area_closing' => 'Alan Kapatma Onayı Bekliyor',
            'pending_safety_closing' => 'İSG Kapatma Onayı Bekliyor',
            'pending_employer_closing' => 'İşveren Vekili Final Onayı Bekliyor',
            'closed' => '✅ Tamamlandı ve Kapatıldı',

            // Diğer
            'rejected' => '❌ Reddedildi'
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    public function isOverdue(): bool
    {
        if (!$this->end_date || $this->status !== 'approved') {
            return false;
        }

        return now()->gt($this->end_date);
    }

    /**
     * Status etiketi getir
     */
    public function getStatusLabel(): string
    {
        $statusLabels = [
            'pending_area_approval' => 'Alan Amiri Onayı Bekliyor',
            'pending_safety_approval' => 'İSG Uzmanı Onayı Bekliyor',
            'approved' => 'İş Devam Ediyor',
            'work_completed' => 'İş Tamamlandı',
            'pending_area_close' => 'Alan Amiri Kapatma Bekliyor',
            'pending_safety_close' => 'İSG Uzmanı Kapatma Bekliyor',
            'pending_employer_close' => 'İşveren Vekili Onayı Bekliyor',
            'closed' => 'Kapatıldı',
            'rejected' => 'Reddedildi',
        ];

        return $statusLabels[$this->status] ?? 'Bilinmiyor';
    }


    /**
     * Durum rengi
     */
    public function getStatusColorAttribute()
    {
        $colorMap = [
            'pending_unit_approval' => 'yellow',
            'pending_area_approval' => 'orange',
            'pending_safety_approval' => 'blue',
            'pending_employer_approval' => 'purple',
            'approved' => 'green',
            'closing_requested' => 'indigo',
            'pending_area_closing' => 'orange',
            'pending_safety_closing' => 'blue',
            'pending_employer_closing' => 'purple',
            'closed' => 'gray',
            'rejected' => 'red'
        ];

        return $colorMap[$this->status] ?? 'gray';
    }

    /**
     * İlerleme yüzdesi
     */
    public function getProgressPercentageAttribute()
    {
        $totalSteps = $this->approvals()->where('type', 'opening')->count();
        $completedSteps = $this->approvals()->where('type', 'opening')->where('status', 'approved')->count();

        return $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
    }

    /**
     * Kalan gün sayısı
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->status !== 'approved') {
            return null;
        }

        return now()->diffInDays($this->end_date, false);
    }

    /**
     * Süresi geçmiş mi?
     */
    public function getIsOverdueAttribute()
    {
        return $this->status === 'approved' && $this->end_date->isPast();
    }

    // ==================== METHODS ====================

    /**
     * Kullanıcı bu iş iznini onaylayabilir mi?
     */
    public function canBeApprovedBy(User $user)
    {
        $currentApproval = $this->currentStep;

        if (!$currentApproval) {
            return false;
        }

        return $currentApproval->user_id === $user->id || $user->isAdmin();
    }

    /**
     * Açılış sürecinde mi?
     */
    public function isInOpeningProcess()
    {
        return in_array($this->status, [
            'pending_unit_approval',
            'pending_area_approval',
            'pending_safety_approval',
            'pending_employer_approval'
        ]);
    }

    /**
     * Kapanış sürecinde mi?
     */
    public function isInClosingProcess()
    {
        return in_array($this->status, [
            'closing_requested',
            'pending_area_closing',
            'pending_safety_closing',
            'pending_employer_closing'
        ]);
    }

    /**
     * Onay süreci tamamlandı mı?
     */
    public function isApprovalComplete()
    {
        return $this->approvals()
            ->where('type', 'opening')
            ->where('status', 'pending')
            ->count() === 0;
    }

    /**
     * Kapanış süreci tamamlandı mı?
     */
    public function isClosingComplete()
    {
        return $this->approvals()
            ->where('type', 'closing')
            ->where('status', 'pending')
            ->count() === 0;
    }
}
