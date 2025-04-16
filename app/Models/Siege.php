<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siege extends Model
{
    use HasFactory;
    protected $table = 'sieges';

    protected $fillable=['numero' , 'type' , 'salle_id','reserve'];



    public function salle(){
        return $this->belongsTo(Salle::class);

    }

//    public function seances()
//    {
//        return $this->belongsToMany(Seance::class, 'reservations')
//            ->withPivot('user_id', 'status')
//            ->wherePivot('status', '!=', 'reserved');
//    }

    public function reservations()
    {
        return $this->belongsToMany(User::class, 'reservations')
            ->withPivot('seance_id', 'status');
    }
}
