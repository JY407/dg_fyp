<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'ic_number',
        'vehicle_number',
        'visit_purpose',
        'expected_arrival',
        'check_in_time',
        'check_out_time',
        'pass_code',
        'status',
        'latitude',
        'longitude',
        'location_address',
        'location_captured_at',
        'ip_address',
    ];

    protected $casts = [
        'expected_arrival' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'location_captured_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function locations()
    {
        return $this->hasMany(VisitorLocation::class);
    }
}
