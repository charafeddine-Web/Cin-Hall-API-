<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sieges extends Model
{
    use HasFactory;

    protected $fillable=[
        'numero',
        'type',
        'status',
        'salle_id'
    ];


    public function reservations()
    {
        return $this->belongstoMany(Reservations::class,'paiements');
    }

    public function salles(){
        return $this->belongsTo(Salles::class);
    }
}
