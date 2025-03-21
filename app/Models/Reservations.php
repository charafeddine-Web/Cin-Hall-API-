<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservations extends Model
{
    use HasFactory;

    protected $fillable=[
        'status',
    ];


    public function sieges(){
        return $this->belongsToMany(Sieges::class,'paiements');
    }
}
