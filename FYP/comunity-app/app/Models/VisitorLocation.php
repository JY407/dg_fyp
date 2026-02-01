<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLocation extends Model
{
    protected $fillable = ['visitor_id', 'latitude', 'longitude'];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }
}
