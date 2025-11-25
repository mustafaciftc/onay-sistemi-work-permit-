<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPermitApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_permit_id',
        'user_id',
        'approval_type',
        'status',
        'notes',
        'checklist',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'checklist' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

     public function workPermit()
    {
        return $this->belongsTo(WorkPermitForm::class, 'work_permit_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeOpeningApprovals($query)
    {
        return $query->whereIn('approval_type', [
            'area_manager',
            'safety_expert',
        ]);
    }

    public function scopeClosingApprovals($query)
    {
        return $query->whereIn('approval_type', [
            'area_manager_close',
            'safety_expert_close',
            'employer_representative',
        ]);
    }

    // ==================== ATTRIBUTES ====================

    /**
     * Onay tipi adı (Türkçe)
     */
    public function getApprovalTypeNameAttribute()
    {
        $typeMap = [
            'area_manager' => 'Alan Amiri (Açılış)',
            'safety_expert' => 'İSG Uzmanı (Açılış)',
            'area_manager_close' => 'Alan Amiri (Kapatma)',
            'safety_expert_close' => 'İSG Uzmanı (Kapatma)',
            'employer_representative' => 'İşveren Vekili (Final)',
        ];

        return $typeMap[$this->approval_type] ?? $this->approval_type;
    }

    /**
     * Durum metni
     */
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'pending' => 'Bekliyor',
            'approved' => 'Onaylandı',
            'rejected' => 'Reddedildi',
        ];

        return $statusMap[$this->status] ?? $this->status;
    }

    /**
     * Durum rengi
     */
    public function getStatusColorAttribute()
    {
        $colorMap = [
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
        ];

        return $colorMap[$this->status] ?? 'gray';
    }

    // ==================== METHODS ====================

    /**
     * Onaylama işlemi
     */
    public function approve($notes = null, $checklist = null)
    {
        $this->status = 'approved';
        $this->approved_at = now();

        if ($notes) {
            $this->notes = $notes;
        }

        if ($checklist) {
            $this->checklist = $checklist;
        }

        $this->save();

        // İş izninin durumunu güncelle
        $this->workPermit->moveToNextStatus();

        return $this;
    }

    /**
     * Reddetme işlemi
     */
    public function reject($reason)
    {
        $this->status = 'rejected';
        $this->rejected_at = now();
        $this->notes = $reason;
        $this->save();

        // İş iznini reddet
        $this->workPermit->reject($reason);

        return $this;
    }

    /**
     * Beklemede mi?
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Onaylandı mı?
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Reddedildi mi?
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Açılış onayı mı?
     */
    public function isOpeningApproval()
    {
        return in_array($this->approval_type, [
            'area_manager',
            'safety_expert',
        ]);
    }

    /**
     * Kapatma onayı mı?
     */
    public function isClosingApproval()
    {
        return in_array($this->approval_type, [
            'area_manager_close',
            'safety_expert_close',
            'employer_representative',
        ]);
    }
}
