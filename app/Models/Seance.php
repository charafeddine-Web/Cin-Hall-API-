<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seance extends Model
{
    use HasFactory;

    protected $fillable = [
        'film_id', 'salle_id', 'start_time', 'session', 'langue', 'type_seance' , 'prix'
    ];

    public static function create(array $validated)
    {
    }

    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

//    public function sieges()
//    {
//        return $this->belongsToMany(Siege::class, 'reservations')
//            ->withPivot('user_id', 'status');
//    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function isVIP()
    {
        return $this->type_seance === 'VIP';
    }
}
