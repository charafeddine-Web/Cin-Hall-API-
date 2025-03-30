<?php

namespace App\Http\Controllers;

use App\Models\Seance;
use App\Services\SeanceService;
use Illuminate\Http\Request;


class SeanceController extends Controller
{
    protected $seanceService;

    public function __construct(SeanceService $seanceService)
    {
        $this->seanceService = $seanceService;
    }

    /**
     * @OA\Get(
     *     path="/api/seances",
     *     summary="Liste toutes les séances",
     *     description="Récupère la liste de toutes les séances de cinéma.",
     *     tags={"Séances"},
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="film_id", type="integer", example=10),
     *                 @OA\Property(property="salle_id", type="integer", example=3),
     *                 @OA\Property(property="start_time", type="string", format="date-time", example="2025-04-01T14:00:00Z"),
     *                 @OA\Property(property="session", type="string", example="Soir"),
     *                 @OA\Property(property="langue", type="string", example="Français"),
     *                 @OA\Property(property="type_seance", type="string", example="VIP"),
     *                 @OA\Property(property="prix", type="number", format="float", example=12.5)
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json($this->seanceService->getAllSeances());
    }

    /**
     * @OA\Post(
     *     path="/api/seances",
     *     summary="Créer une nouvelle séance",
     *     description="Ajoute une nouvelle séance de cinéma.",
     *     tags={"Séances"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"film_id", "salle_id", "start_time", "session", "langue", "type_seance", "prix"},
     *             @OA\Property(property="film_id", type="integer", example=10),
     *             @OA\Property(property="salle_id", type="integer", example=3),
     *             @OA\Property(property="start_time", type="string", format="date-time", example="2025-04-01T14:00:00Z"),
     *             @OA\Property(property="session", type="string", example="Soir"),
     *             @OA\Property(property="langue", type="string", example="Français"),
     *             @OA\Property(property="type_seance", type="string", enum={"Normale", "VIP"}, example="VIP"),
     *             @OA\Property(property="prix", type="number", format="float", example=12.5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Séance créée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="film_id", type="integer", example=10),
     *             @OA\Property(property="salle_id", type="integer", example=3),
     *             @OA\Property(property="start_time", type="string", format="date-time", example="2025-04-01T14:00:00Z"),
     *             @OA\Property(property="session", type="string", example="Soir"),
     *             @OA\Property(property="langue", type="string", example="Français"),
     *             @OA\Property(property="type_seance", type="string", example="VIP"),
     *             @OA\Property(property="prix", type="number", format="float", example=12.5)
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:films,id',
            'salle_id' => 'required|exists:salles,id',
            'start_time' => 'required|date',
            'session' => 'required|string',
            'langue' => 'required|string',
            'type_seance' => 'required|in:Normale,VIP',
            'prix' => 'required|numeric',
        ]);

        $seance = $this->seanceService->createSeance($validated);

        return response()->json($seance, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/seances/type/{type}",
     *     summary="Filtrer les séances par type",
     *     description="Récupère les séances en fonction de leur type (Normale ou VIP).",
     *     tags={"Séances"},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Type de séance (Normale ou VIP)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Succès"),
     *     @OA\Response(response=400, description="Type invalide"),
     *     @OA\Response(response=404, description="Aucune séance trouvée")
     * )
     */
    public function showByType($type) {
        if (!in_array($type, ['Normale', 'VIP'])) {
            return response()->json(['error' => 'Le type doit être soit "Normale" soit "VIP".'], 400);
        }
        $seances = $this->seanceService->getSeancesByType($type);

        if ($seances->isEmpty()) {
            return response()->json(['message' => 'Aucune séance trouvée pour ce type.'], 404);
        }

        return response()->json($seances, 200);
    }

    public function getAllSeancesWithFilms(){
        return response()->json($this->seanceService->getAllSeancesWithFilms());
    }
}
