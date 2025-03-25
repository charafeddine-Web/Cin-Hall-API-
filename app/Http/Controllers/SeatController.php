<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\SeatRepositoryInterface;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Places",
 *     description="Gestion des places"
 * )
 */
class SeatController extends Controller
{
    protected $seatRepository;

    public function __construct(SeatRepositoryInterface $seatRepository)
    {
        $this->seatRepository = $seatRepository;

        $this->middleware('permission:view_seats')->only(['index', 'show']);
        $this->middleware('permission:create_seat')->only(['store']);
        $this->middleware('permission:edit_seat')->only(['update']);
        $this->middleware('permission:delete_seat')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/seats",
     *     summary="Liste toutes les places",
     *     tags={"Places"},
     *     @OA\Response(response=200, description="Liste des places"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index()
    {
        return response()->json($this->seatRepository->all(), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/seats",
     *     summary="Créer une nouvelle place",
     *     tags={"Places"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"place_number", "row", "status", "session_id"},
     *             @OA\Property(property="place_number", type="integer"),
     *             @OA\Property(property="row", type="string"),
     *             @OA\Property(property="status", type="string", enum={"available", "reserved", "occupied"}),
     *             @OA\Property(property="session_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Place créée"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => 'required|integer',
            'type' => 'required|string',
            'status' => 'required|string|in:available,reserved,occupied',
            'salle_id' => 'required|integer|exists:salles,id',
        ]);

        return response()->json($this->seatRepository->create($data), Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/seats/{id}",
     *     summary="Afficher une place spécifique",
     *     tags={"Places"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Place trouvée"),
     *     @OA\Response(response=404, description="Place non trouvée")
     * )
     */
    public function show($id)
    {
        $seat = $this->seatRepository->find($id);
        if (!$seat) {
            return response()->json(['message' => 'Place non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($seat, Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/seats/{id}",
     *     summary="Mettre à jour une place",
     *     tags={"Places"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="place_number", type="integer"),
     *             @OA\Property(property="row", type="string"),
     *             @OA\Property(property="status", type="string", enum={"available", "reserved", "occupied"}),
     *             @OA\Property(property="session_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Place mise à jour"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=404, description="Place non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'place_number' => 'sometimes|integer',
            'row' => 'sometimes|string',
            'status' => 'sometimes|string|in:available,reserved,occupied',
            'session_id' => 'sometimes|integer|exists:sessions,id',
        ]);

        $seat = $this->seatRepository->update($id, $data);
        if (!$seat) {
            return response()->json(['message' => 'Place non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($seat, Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/seats/{id}",
     *     summary="Supprimer une place",
     *     tags={"Places"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Place supprimée"),
     *     @OA\Response(response=404, description="Place non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $deleted = $this->seatRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Place non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'Place supprimée'], Response::HTTP_OK);
    }
}
