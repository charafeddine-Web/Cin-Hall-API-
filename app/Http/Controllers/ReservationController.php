<?php

namespace App\Http\Controllers;

use App\Models\Paiements;
use App\Models\Reservations;
use App\Models\Seances;
use App\Models\Sieges;
use App\Models\Tickets;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
     *     summary="Create a new reservation",
     *     description="This endpoint creates a reservation for a user. If the session is VIP, it requires two seats to be reserved in a couple.",
     *     tags={"Reservations"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Reservation details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"user_id", "seance_id", "status", "seat_ids", "montant"},
     *                 @OA\Property(property="user_id", type="integer", description="User ID who is making the reservation"),
     *                 @OA\Property(property="seance_id", type="integer", description="Session ID for the reservation"),
     *                 @OA\Property(property="status", type="string", description="Reservation status"),
     *                 @OA\Property(property="seat_ids", type="array", @OA\Items(type="integer"), description="Array of seat IDs"),
     *                 @OA\Property(property="montant", type="number", format="float", description="Total amount for the reservation"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reservation successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="reservation", type="object", ref="#/components/schemas/Reservation"),
     *             @OA\Property(property="ticket", type="object", ref="#/components/schemas/Ticket")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request, invalid data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Session not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error, failed to create reservation",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", description="Error message")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'seance_id' => 'required|integer|exists:seances,id',
            'status' => 'required',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'integer|exists:sieges,id',
            'montant' => 'required|numeric',
        ]);

        $seance = Seances::find($data['seance_id']);
        if (!$seance) {
            return response()->json(['message' => 'Séance non trouvée'], Response::HTTP_NOT_FOUND);
        }

        DB::beginTransaction();
        try {
            if ($seance->type === 'VIP') {
                $seats = Sieges::whereIn('id', $data['seat_ids'])->get();

                if ($seats->count() < 2) {
                    return response()->json(['message' => 'Pour une séance VIP, nous vous recommandons de réserver deux sièges.'], Response::HTTP_BAD_REQUEST);
                }

                if ($seats[0]->couple_seat_id !== $seats[1]->id || $seats[1]->couple_seat_id !== $seats[0]->id) {
                    throw new \Exception('Les sièges doivent être réservés en couple pour une séance VIP.');
                }
            }
            $reservation = Reservations::create([
                'user_id' => $data['user_id'],
                'seance_id' => $data['seance_id'],
                'status' => $data['status'],
                'expires_at' => now()->addMinutes(15),
                'seat_ids' => json_encode($data['seat_ids']),  // Store the seat_ids in the reservation

            ]);
            if ($reservation->status === 'confirmée') {
                foreach ($data['seat_ids'] as $seatId) {
                    $payment = Paiements::create([
                        'reservation_id' => $reservation->id,
                        'siege_id' => $seatId,
                        'montant' => $data['montant'],
                    ]);

                    // Mark seats as reserved
                    Sieges::where('id', $seatId)->update(['status' => 'reserved']);
                }

                // Create ticket based on the payment (for the last one created)
                $ticket = Tickets::create([
                    'paiement_id' => $payment->id,
                    'qr_code' => Str::random(10),
                ]);
            }
            DB::commit();

            return response()->json([
                'reservation' => $reservation,
                'ticket' => $ticket ?? null  // If no ticket created, return null
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
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
