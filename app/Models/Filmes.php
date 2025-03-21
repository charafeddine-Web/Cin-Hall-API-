<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filmes extends Model
{
    use HasFactory;

    protected $fillable=[
        'titre',
        'description',
        'durée',
        'image',
        'age_minimum',
        'genre',
    ];

    public function salles()
    {
        return $this->belongsToMany(Salles::class,'seances');
    }
}
