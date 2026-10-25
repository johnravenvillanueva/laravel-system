<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'phone',
        'address',
        'avatar',
        'is_guest',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_guest'   => 'boolean',
        'is_active'  => 'boolean',
        'last_login' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cart()
    {
        return $this->hasOne(Cart::class, 'customer_id', 'customer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'customer_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'customer_id', 'customer_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'customer_id', 'customer_id');
    }


    public function isGuest(): bool
    {
        return (bool) $this->is_guest;
    }

    public function isRegistered(): bool
    {
        return ! $this->is_guest;
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
        public function getIdAttribute()
    {
        return $this->customer_id;
    }
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}