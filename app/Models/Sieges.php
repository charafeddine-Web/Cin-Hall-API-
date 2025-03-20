<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sieges extends Model
{
    use HasFactory;

    protected $fillable=[
        'numero',
        'status',
    ];
}
