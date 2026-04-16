<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadNotice extends Model
{
    protected $fillable = [
        'posted_by',
        'title',
        'description',
        'location',
        'notice_type',
        'severity',
        'status',
        'image_path',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }
}
