<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    use HasFactory;

    protected $fillable = [
        'paiement_id',
        'qr_code',
        'pdf_path'
    ];

    public function paiement()
    {
        return $this->belongsTo(Paiements::class);
    }


}
