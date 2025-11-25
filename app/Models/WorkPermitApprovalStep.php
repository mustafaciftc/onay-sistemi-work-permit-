<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkPermitApprovalStep extends Model
{
    protected $table = 'work_permit_approval_steps';

    protected $fillable = [
        'work_permit_form_id',
        'phase',
        'step',
        'order',
        'status',
        'assigned_user_id',
        'comments',
        'checklist',
        'completed_at'
    ];

    protected $casts = [
        'checklist' => 'array',
        'completed_at' => 'datetime'
    ];

    // Sabit workflow tanımları
    const OPENING_STEPS = [
        1 => 'unit_manager',
        2 => 'area_manager',
        3 => 'safety_specialist',
        4 => 'employer_representative'
    ];

    const CLOSING_STEPS = [
        1 => 'unit_manager',
        2 => 'area_manager',
        3 => 'safety_specialist',
        4 => 'employer_representative'
    ];

    public function workPermitForm()
    {
        return $this->belongsTo(WorkPermitForm::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function getStepLabelAttribute()
    {
        $labels = [
            'unit_manager' => 'Birim Amiri',
            'area_manager' => 'Alan Amiri',
            'safety_specialist' => 'İSG Uzmanı',
            'employer_representative' => 'İşveren Vekili'
        ];

        return $labels[$this->step] ?? $this->step;
    }

    public function getPhaseTextAttribute()
    {
        return $this->phase === 'opening' ? 'Açılış' : 'Kapanış';
    }
}
