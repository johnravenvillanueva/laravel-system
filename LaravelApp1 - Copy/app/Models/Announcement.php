<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'announcement_id';

    protected $fillable = [
        'created_by',
        'title',
        'content',
        'target_audience',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* -------------------------------------------------
     |  Relationships
     |-------------------------------------------------*/

    public function author()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'admin_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'announcement_id', 'announcement_id');
    }

    /* -------------------------------------------------
     |  Scopes
     |-------------------------------------------------*/

    /**
     * Only currently visible announcements:
     * - is_active = true
     * - not expired (or no expiry)
     */
    public function scopeVisible($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    public function scopeForCustomers($query)
    {
        return $query->whereIn('target_audience', ['all', 'customers']);
    }

    public function scopeForStaff($query)
    {
        return $query->whereIn('target_audience', ['all', 'staff']);
    }

    /* -------------------------------------------------
     |  Helpers
     |-------------------------------------------------*/

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isVisible(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }
}