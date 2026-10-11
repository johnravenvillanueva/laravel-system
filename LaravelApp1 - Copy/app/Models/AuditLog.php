<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'staff_id',
        'customer_id',
        'action',
        'table_name',
        'record_id',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    /* -------------------------------------------------
     |  Relationships
     |-------------------------------------------------*/

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'admin_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /* -------------------------------------------------
     |  Scopes
     |-------------------------------------------------*/

    public function scopeForRecord($query, string $table, int $id)
    {
        return $query->where('table_name', $table)->where('record_id', $id);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /* -------------------------------------------------
     |  Helpers
     |-------------------------------------------------*/

    public function getActorNameAttribute(): string
    {
        if ($this->admin) {
            return $this->admin->fullName() . ' (admin)';
        }
        if ($this->staff) {
            return $this->staff->fullName() . ' (staff)';
        }
        if ($this->customer) {
            return $this->customer->fullName() . ' (customer)';
        }
        return 'System';
    }

    public function decodedOld(): array
    {
        return $this->old_value ? (json_decode($this->old_value, true) ?: []) : [];
    }

    public function decodedNew(): array
    {
        return $this->new_value ? (json_decode($this->new_value, true) ?: []) : [];
    }
}