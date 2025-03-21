<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seances extends Model
{
    use HasFactory;

    protected $fillable=[
        'type',
        'date_heure',
        'langue',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class,'reservations');
    }

}
