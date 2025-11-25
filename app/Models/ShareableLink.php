<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShareableLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_permit_id',
        'token',
        'password',
        'expires_at',
        'max_views',
        'view_count',
        'is_active',
        'permissions'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'permissions' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($link) {
            $link->token = Str::uuid()->toString();
        });
    }

    public function workPermit()
    {
        return $this->belongsTo(WorkPermitForm::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function hasReachedViewLimit()
    {
        return $this->max_views && $this->view_count >= $this->max_views;
    }

    public function canBeAccessed()
    {
        return $this->is_active &&
            !$this->isExpired() &&
            !$this->hasReachedViewLimit();
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function getDefaultPermissions()
    {
        return [
            'view' => true,
            'download_pdf' => true,
            'view_attachments' => false,
        ];
    }

    public function hasPermission($permission)
    {
        $permissions = $this->permissions ?? $this->getDefaultPermissions();
        return $permissions[$permission] ?? false;
    }

    public function getShareUrl()
    {
        return route('admin.work-permits.shareable-links.show', $this->token);
    }
}
