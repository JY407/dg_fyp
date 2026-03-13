<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = [
        'name',
        'capacity',
        'type', // e.g. 'Capacity', 'Internet', 'Court'
        'image_path',
    ];
}
