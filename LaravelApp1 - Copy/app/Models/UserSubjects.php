<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubjects extends Model
{
    use HasFactory;

    protected $table = 'user_subjects';

    protected $fillable = [
        'user_id',
        'subject_id',
    ];
}
