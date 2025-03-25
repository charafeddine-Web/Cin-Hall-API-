<?php

namespace App\Http\Controllers;

use App\Models\Filmes;
use App\Models\Seances;
use App\Models\Reservations;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $filmCount = Filmes::count();
        $sessionCount = Seances::count();
        $reservationCount = Reservations::count();

        $revenue = Reservations::sum('amount');

        return response()->json([
            'film_count' => $filmCount,
            'session_count' => $sessionCount,
            'reservation_count' => $reservationCount,
            'revenue' => $revenue,
        ]);
    }
}
