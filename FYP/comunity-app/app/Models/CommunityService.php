<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityService extends Model
{
    protected $fillable = [
        'service_name',
        'provider_name',
        'frequency',
        'day_of_week',
        'time_slot',
        'description',
        'contact_number',
        'status',
    ];
}
