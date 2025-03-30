<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
        $this->middleware('auth:api');
    }
    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     summary="Obtenir toutes les réservations",
     *     description="Récupère toutes les réservations de l'utilisateur authentifié.",
     *     tags={"Réservations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des réservations de l'utilisateur",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=3),
     *                 @OA\Property(property="seance_id", type="integer", example=2),
     *                 @OA\Property(property="siege_id", type="integer", example=15),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-28 14:00:00"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-28 14:00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function index()
    {
        $userId = Auth::id();
        $reservations = Reservation::where('user_id', $userId)
            ->with('seance', 'siege') // Charger les relations de la séance et du siège
            ->get();

        // Vérifier si l'utilisateur a des réservations
        if ($reservations->isEmpty()) {
            return response()->json(['message' => 'Aucune réservation trouvée.'], 404);
        }

        return response()->json($reservations);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Créer une nouvelle réservation",
     *     description="Ajoute une réservation pour l'utilisateur authentifié.",
     *     tags={"Réservations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"seance_id", "siege_id"},
     *             @OA\Property(property="seance_id", type="integer", example=2, description="ID de la séance à réserver"),
     *             @OA\Property(property="siege_id", type="integer", example=15, description="ID du siège sélectionné")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Réservation créée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="reservation", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=3),
     *                 @OA\Property(property="seance_id", type="integer", example=2),
     *                 @OA\Property(property="siege_id", type="integer", example=15),
     *                 @OA\Property(property="status", type="string", example="pending")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Le siège est déjà réservé.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    // Pour créer une réservation
    public function store(Request $request)
    {
        $userId = Auth::id();
        $data = $request->all();
        $data['user_id'] = $userId;

        return $this->reservationService->createReservation($data);
    }
    /**
     * @OA\Put(
     *     path="/api/reservations/{id}",
     *     summary="Mettre à jour une réservation",
     *     description="Mise à jour des informations d'une réservation existante.",
     *     tags={"Réservations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réservation à mettre à jour",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "siege_id", "seance_id", "status"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="siege_id", type="integer", example=10),
     *             @OA\Property(property="seance_id", type="integer", example=3),
     *             @OA\Property(property="status", type="string", example="confirmed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réservation mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="siege_id", type="integer", example=10),
     *             @OA\Property(property="seance_id", type="integer", example=3),
     *             @OA\Property(property="status", type="string", example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réservation non trouvée"
     *     )
     * )
     */
    public function update(Request $request  , $id)
    {
        $data = $request->all();
        $data['user_id'] = auth()->id();
      //  return response()->json($data);
        return $this->reservationService->updateResevation($data , $id);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations/{id}/confirm",
     *     summary="Confirmer une réservation",
     *     description="Confirme une réservation en attente.",
     *     tags={"Réservations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réservation à confirmer",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réservation confirmée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Réservation confirmée."),
     *             @OA\Property(property="reservation", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=3),
     *                 @OA\Property(property="status", type="string", example="confirmed"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-28 14:00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Impossible de confirmer la réservation"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réservation non trouvée"
     *     )
     * )
     */
    // Pour confirmer une réservation après paiement
    public function confirm($id)
    {
        $userId = Auth::id();
        return $this->reservationService->confirmReservation($id, $userId);
    }
    /**
     * @OA\Put(
     *     path="/api/reservations/{id}/cancel",
     *     summary="Annuler une réservation",
     *     description="Permet à l'utilisateur authentifié d'annuler une réservation si elle est encore en attente.",
     *     tags={"Réservations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réservation à annuler",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réservation annulée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Réservation annulée.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="La réservation ne peut pas être annulée",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cette réservation ne peut pas être annulée.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réservation non trouvée"
     *     )
     * )
     */
    // Pour annuler une réservation si le paiement n'a pas été effectué dans les 15 minutes
    public function cancel($id)
    {
        $userId = Auth::id();  // Récupérer l'ID de l'utilisateur authentifié
        return $this->reservationService->cancelReservation($id, $userId); // Annuler la réservation
    }

//    public function getReservations($userId)
//    {
//        $user = User::with('reservations.siege', 'reservations.seance')->find($userId);
//
//        if (!$user) {
//            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
//        }
//
//        return response()->json($user->reservations);
//    }


}
