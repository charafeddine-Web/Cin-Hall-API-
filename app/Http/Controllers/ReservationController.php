<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Réservations",
 *     description="Gestion des réservations"
 * )
 */
class ReservationController extends Controller
{
    protected $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;

        $this->middleware('permission:view_reservations')->only(['index', 'show']);
        $this->middleware('permission:create_reservation')->only(['store']);
        $this->middleware('permission:edit_reservation')->only(['update']);
        $this->middleware('permission:delete_reservation')->only(['destroy']);
    }

    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     summary="Liste toutes les réservations",
     *     tags={"Réservations"},
     *     @OA\Response(response=200, description="Liste des réservations"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index()
    {
        return response()->json($this->reservationRepository->all(), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Créer une nouvelle réservation",
     *     tags={"Réservations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "seat_id", "reservation_date"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="seat_id", type="integer"),
     *             @OA\Property(property="reservation_date", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Réservation créée"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'seat_id' => 'required|integer|exists:seats,id',
            'reservation_date' => 'required|date',
        ]);

        return response()->json($this->reservationRepository->create($data), Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/reservations/{id}",
     *     summary="Afficher une réservation spécifique",
     *     tags={"Réservations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Réservation trouvée"),
     *     @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
    public function show($id)
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return response()->json(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($reservation, Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/reservations/{id}",
     *     summary="Mettre à jour une réservation",
     *     tags={"Réservations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="seat_id", type="integer"),
     *             @OA\Property(property="reservation_date", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Réservation mise à jour"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=404, description="Réservation non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'user_id' => 'sometimes|integer|exists:users,id',
            'seat_id' => 'sometimes|integer|exists:seats,id',
            'reservation_date' => 'sometimes|date',
        ]);

        $reservation = $this->reservationRepository->update($id, $data);
        if (!$reservation) {
            return response()->json(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($reservation, Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/reservations/{id}",
     *     summary="Supprimer une réservation",
     *     tags={"Réservations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Réservation supprimée"),
     *     @OA\Response(response=404, description="Réservation non trouvée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function destroy($id)
    {
        $deleted = $this->reservationRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Réservation non trouvée'], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['message' => 'Réservation supprimée'], Response::HTTP_OK);
    }
}
