<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\SeanceRepositoryInterface;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Séances",
 *     description="Gestion des séances"
 * )
 */
class SeanceController extends Controller
{
    protected $seanceRepository;

    public function __construct(SeanceRepositoryInterface $seanceRepository)
    {
        $this->seanceRepository = $seanceRepository;

        $this->middleware('permission:view_seances')->only(['index', 'show']);
        $this->middleware('permission:create_seance')->only(['store']);
        $this->middleware('permission:edit_seance')->only(['update']);
        $this->middleware('permission:delete_seance')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/seances",
     *     summary="Liste toutes les séances",
     *     tags={"Séances"},
     *     @OA\Response(response=200, description="Liste des séances"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index()
    {
        return response()->json($this->seanceRepository->all(), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/seances",
     *     summary="Créer une nouvelle séance",
     *     tags={"Séances"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date", "heure", "type_séance", "salle_id"},
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(property="heure", type="string", format="time"),
     *             @OA\Property(property="type_séance", type="string"),
     *             @OA\Property(property="salle_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Séance créée"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'heure' => 'required|date_format:H:i',
            'type_séance' => 'required|string',
            'salle_id' => 'required|integer|exists:salles,id',
        ]);

        return response()->json($this->seanceRepository->create($data), Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/seances/{id}",
     *     summary="Afficher une séance spécifique",
     *     tags={"Séances"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Séance trouvée"),
     *     @OA\Response(response=404, description="Séance non trouvée")
     * )
     */
    public function show($id)
    {
        $seance = $this->seanceRepository->find($id);
        if (!$seance) {
            return response()->json(['message' => 'Séance non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($seance, Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/seances/{id}",
     *     summary="Mettre à jour une séance",
     *     tags={"Séances"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(property="heure", type="string", format="time"),
     *             @OA\Property(property="type_séance", type="string"),
     *             @OA\Property(property="salle_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Séance mise à jour"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=404, description="Séance non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'date' => 'sometimes|date',
            'heure' => 'sometimes|date_format:H:i',
            'type_séance' => 'sometimes|string',
            'salle_id' => 'sometimes|integer|exists:salles,id',
        ]);

        $seance = $this->seanceRepository->update($id, $data);
        if (!$seance) {
            return response()->json(['message' => 'Séance non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($seance, Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/seances/{id}",
     *     summary="Supprimer une séance",
     *     tags={"Séances"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Séance supprimée"),
     *     @OA\Response(response=404, description="Séance non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $deleted = $this->seanceRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Séance non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'Séance supprimée'], Response::HTTP_OK);
    }
}
