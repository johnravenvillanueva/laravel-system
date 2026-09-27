<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'name',
        'day',
        'time',
        'room',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_subjects')
            ->withTimestamps();
    }
}
