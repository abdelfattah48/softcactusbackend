<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'public_card',
        'details',
    ];

    protected $casts = [
        'public_card' => 'array',
        'details' => 'array',
    ];
}
