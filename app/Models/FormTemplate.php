<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'fields',
        'workflow',
        'is_active',
        'is_default',
        'is_published',
        'form_category',
    ];

    protected $casts = [
        'fields' => 'array',
        'workflow' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function formFields()
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function workPermits()
    {
        return $this->hasMany(WorkPermitForm::class);
    }

    public function getFieldTypesAttribute()
    {
        return [
            'text' => 'Tek Satır Metin',
            'textarea' => 'Çok Satır Metin',
            'number' => 'Sayı',
            'select' => 'Seçim Kutusu',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Buton',
            'date' => 'Tarih',
            'datetime' => 'Tarih ve Saat',
            'file' => 'Dosya Yükleme',
            'signature' => 'Dijital İmza',
        ];
    }

    public function getWorkflowStepsAttribute()
    {
        return [
            'unit_manager' => 'Birim Amiri',
            'area_manager' => 'Alan Amiri',
            'safety_specialist' => 'İSG Uzmanı',
            'employer_representative' => 'İşveren Vekili',
        ];
    }
}
