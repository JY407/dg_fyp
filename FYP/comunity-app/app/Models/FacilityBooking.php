<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityBooking extends Model
{
    protected $fillable = [
        'user_id',
        'facility_name',
        'booking_date',
        'start_time',
        'end_time',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
