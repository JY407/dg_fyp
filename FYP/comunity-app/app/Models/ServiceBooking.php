<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    protected $fillable = [
        'user_id',
        'community_service_id',
        'booking_date',
        'requested_time',
        'status',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function communityService()
    {
        return $this->belongsTo(CommunityService::class);
    }
}
