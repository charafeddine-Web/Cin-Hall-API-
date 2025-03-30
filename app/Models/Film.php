<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;
    protected $fillable=['titre' , 'description' , 'image' , 'duree' , 'age_minimum' , 'bande_annonce' , 'genre'  ];
    protected $casts = [
        'acteurs' => 'array',
    ];
    // Relation many-to-many avec Salle via la table pivot
    public function salles()
    {
        return $this->belongsToMany(Salle::class, 'seances')
            ->withPivot('start_time', 'session', 'langue')
            ->withTimestamps();
    }
}
