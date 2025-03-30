<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashBoardController
{
    public function getDashboardStats()
    {
        $stats = [
            'total_films' => DB::table('films')->count(),
            'total_seances' => DB::table('seances')->count(),
            'total_reservations' => DB::table('reservations')->count(),
        ];

        $tauxOccupation = DB::table('seances')
            ->select('seances.id', 'films.titre',
                DB::raw('COUNT(reservations.id) as places_reservees'),
                DB::raw('(COUNT(reservations.id) / salles.capacite) * 100 as taux_occupation'))
            ->join('films', 'seances.film_id', '=', 'films.id')
            ->join('salles', 'seances.salle_id', '=', 'salles.id')
            ->leftJoin('reservations', 'seances.id', '=', 'reservations.seance_id')
            ->groupBy('seances.id', 'films.titre', 'salles.capacite')
            ->get();

        $revenusParFilm = DB::table('films')
            ->select('films.titre',
                DB::raw('COUNT(reservations.id) as tickets_vendus'),
                DB::raw('SUM(seances.prix) as revenus_total'))
            ->join('seances', 'films.id', '=', 'seances.film_id')
            ->join('reservations', 'seances.id', '=', 'reservations.seance_id')
            ->groupBy('films.titre')
            ->get();

        $filmsPopulaires = DB::table('films')
            ->select('films.titre', DB::raw('COUNT(reservations.id) as nombre_reservations'))
            ->join('seances', 'films.id', '=', 'seances.film_id')
            ->join('reservations', 'seances.id', '=', 'reservations.seance_id')
            ->groupBy('films.titre')
            ->orderByDesc('nombre_reservations')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'tauxOccupation' => $tauxOccupation,
            'revenusParFilm' => $revenusParFilm,
            'filmsPopulaires' => $filmsPopulaires,

        ]) ;
}
}
