<?php

namespace App\Http\Controllers;

use App\Models\Paiements;
use App\Models\Reservations;
use App\Models\Seances;
use App\Models\Sieges;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
        // Validate request
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'seance_id' => 'required|integer|exists:seances,id',
            'status' => 'required',
            'seat_ids' => 'required|array|min:2', // Ensure two seats are provided
            'seat_ids.*' => 'integer|exists:sieges,id', // Validate each seat ID
            'montant' => 'required|numeric',
        ]);

        // Fetch the session
        $seance = Seances::find($data['seance_id']);
        if (!$seance) {
            return response()->json(['message' => 'Séance non trouvée'], Response::HTTP_NOT_FOUND);
        }

        DB::beginTransaction(); // Start transaction

        try {
            // Check if the session is VIP
            if ($seance->type === 'VIP') {
                $seats = Sieges::whereIn('id', $data['seat_ids'])->get();

                // Ensure exactly two seats are provided
                if ($seats->count() !== 2) {
                    throw new \Exception('Vous devez réserver deux sièges pour une séance VIP.');
                }

                // Ensure the two seats belong to the same couple
                if ($seats[0]->couple_seat_id !== $seats[1]->id || $seats[1]->couple_seat_id !== $seats[0]->id) {
                    throw new \Exception('Les sièges doivent être réservés en couple pour une séance VIP.');
                }
            }

            // Create the reservation
            $reservation = Reservations::create([
                'user_id' => $data['user_id'],
                'seance_id' => $data['seance_id'],
                'status' => $data['status'],
            ]);

            // Insert payments and update seat statuses
            foreach ($data['seat_ids'] as $seatId) {
                // Insert into paiements table
                Paiements::create([
                    'reservation_id' => $reservation->id,
                    'siege_id' => $seatId,
                    'montant' => $data['montant'],
                    'status' => 'en attente',
                ]);

                // Update seat status to "reserved"
                Sieges::where('id', $seatId)->update(['status' => 'reserved']);
            }

            DB::commit(); // Commit transaction

            return response()->json($reservation, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction in case of error
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
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
            'seance_id' => 'sometimes|integer|exists:seats,id',
            'status' => 'sometimes',
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
