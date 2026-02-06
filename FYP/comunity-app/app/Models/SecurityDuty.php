<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SecurityDuty extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'shift',
        'guard_name',
        'location',
        'status',
        'contact_number',
        'notes',
    ];
}
