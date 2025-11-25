<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_permit_form_id',
        'form_template_id',
        'field_values',
    ];

    protected $casts = [
        'field_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // İlişkiler
    public function workPermitForm()
    {
        return $this->belongsTo(WorkPermitForm::class, 'work_permit_form_id');
    }

    public function formTemplate()
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }

    // Yardımcı metod
    public function getFieldValue($fieldKey, $default = null)
    {
        return data_get($this->field_values, $fieldKey, $default);
    }
}
