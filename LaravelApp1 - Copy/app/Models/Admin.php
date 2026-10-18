<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'phone',
        'avatar',
        'role',
        'is_active',
        'last_login',
        'created_by',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'last_login' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'admin_id');
    }

    public function createdAccounts()
    {
        return $this->hasMany(Admin::class, 'created_by', 'admin_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'staff_id', 'admin_id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by', 'admin_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'admin_id', 'admin_id');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'performed_by', 'admin_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
        public function getIdAttribute()
    {
        return $this->admin_id;
    }
}