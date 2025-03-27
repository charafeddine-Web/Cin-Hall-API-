<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservations extends Model
{
    use HasFactory;

    protected $fillable=[
        "user_id",
        "seance_id",
        'status',
        'expires_at'
    ];
    protected $casts = [
        'expires_at' => 'datetime',
    ];


    public function sieges(){
        return $this->belongsToMany(Sieges::class, 'paiements', 'reservation_id', 'siege_id');
    }
}
