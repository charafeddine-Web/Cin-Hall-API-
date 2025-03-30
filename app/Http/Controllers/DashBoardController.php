<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashBoardController
{
    /**
     * @OA\Get(
     *     path="/api/dashboard/stats",
     *     summary="Obtenir les statistiques du tableau de bord",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques du tableau de bord récupérées avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="stats", type="object",
     *                 @OA\Property(property="total_films", type="integer"),
     *                 @OA\Property(property="total_seances", type="integer"),
     *                 @OA\Property(property="total_reservations", type="integer")
     *             ),
     *             @OA\Property(property="tauxOccupation", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="titre", type="string"),
     *                     @OA\Property(property="places_reservees", type="integer"),
     *                     @OA\Property(property="taux_occupation", type="number")
     *                 )
     *             ),
     *             @OA\Property(property="revenusParFilm", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="titre", type="string"),
     *                     @OA\Property(property="tickets_vendus", type="integer"),
     *                     @OA\Property(property="revenus_total", type="number")
     *                 )
     *             ),
     *             @OA\Property(property="filmsPopulaires", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="titre", type="string"),
     *                     @OA\Property(property="nombre_reservations", type="integer")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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
        ]);
    }
}
