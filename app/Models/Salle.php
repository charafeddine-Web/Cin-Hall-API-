<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    use HasFactory;
    protected $fillable = ['nom' ,'type' , 'capacite'];
    public function sieges()
    {
        return $this->hasMany(Siege::class);
    }
    // Relation many-to-many avec Salle via la table pivot
    public function films()
    {
        return $this->belongsToMany(Salle::class, 'seances')
            ->withPivot('start_time', 'session', 'langue')
            ->withTimestamps();
    }
}
