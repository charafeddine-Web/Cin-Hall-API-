<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seances extends Model
{
    use HasFactory;

    protected $fillable=[
        'session',
        'date_start',
        'type',
        'film_id',
        'salle_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class,'reservations');
    }

}
