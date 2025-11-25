<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = [
        'form_template_id',
        'name',
        'label',
        'type',
        'options',
        'description',
        'required',
        'sort_order',
        'validation_rules',
        'conditional_display'
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'conditional_display' => 'array',
        'required' => 'boolean'
    ];

    public function formTemplate()
    {
        return $this->belongsTo(FormTemplate::class);
    }
}
