<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Paiements extends Model
    {
        use HasFactory;

        protected $fillable=[
            'user_id',
            'seance_id',
            'montant',
            'status',
            'reservation_id',
            'siege_id'
        ];

        public function ticket()
        {
            return $this->belongsTo(Tickets::class);
        }
    }
