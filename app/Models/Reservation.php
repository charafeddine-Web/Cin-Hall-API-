<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{

    protected $fillable = ['user_id', 'siege_id', 'seance_id', 'status' , 'prix'];

//    public function spectateur()
//    {
//        return $this->belongsTo(User::class, 'spectateur_id');
//    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');  // Assure-toi que 'user_id' est utilisé ici
    }

    public function siege()
    {
        return $this->belongsTo(Siege::class);
    }

    public function seance()
    {
        return $this->belongsTo(Seance::class );
    }



}
