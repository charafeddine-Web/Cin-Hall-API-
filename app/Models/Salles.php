<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salles extends Model
{
    use HasFactory;


    protected $fillable = [
        'nom',
        'nbr_places',
    ];



    public function sieges(){
        return $this->hasMany(Sieges::class);
    }

    public function filmes(){
        return $this->belongsToMany(Filmes::class,'seances');
    }
}
