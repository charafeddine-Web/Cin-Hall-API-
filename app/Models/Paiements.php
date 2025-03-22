<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Paiements extends Model
    {
        use HasFactory;

        protected $fillable=[
            'montant',
            'status',
            'ticket_id',
        ];

        public function ticket()
        {
            return $this->belongsTo(Tickets::class);
        }
    }
