<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
     *     tags={"Réservations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Liste des réservations de l'utilisateur"),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=404, description="Aucune réservation trouvée")
     * )
     */
    public function index()
    {
        $userId = Auth::id();
        $reservations = Reservation::where('user_id', $userId)->with('seance', 'siege')->get();

        if ($reservations->isEmpty()) {
            return response()->json(['message' => 'Aucune réservation trouvée.'], 404);
        }
        return response()->json($reservations);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="Créer une nouvelle réservation",
     *     tags={"Réservations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"seance_id", "siege_id"},
     *             @OA\Property(property="seance_id", type="integer", example=2),
     *             @OA\Property(property="siege_id", type="integer", example=15)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Réservation créée avec succès"),
     *     @OA\Response(response=400, description="Erreur de validation"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'seance_id' => 'required|exists:seances,id',
            'siege_id' => 'required|array',
            'user_id' => 'required|exists:users,id',
        ]);

        $data = $request->all();
        return $this->reservationService->createReservation($data);
    }

    /**
     * @OA\Put(
     *     path="/api/reservations/{id}",
     *     summary="Mettre à jour une réservation",
     *     tags={"Réservations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"siege_id", "seance_id", "status"},
     *         @OA\Property(property="siege_id", type="integer"),
     *         @OA\Property(property="seance_id", type="integer"),
     *         @OA\Property(property="status", type="string")
     *     )),
     *     @OA\Response(response=200, description="Réservation mise à jour"),
     *     @OA\Response(response=400, description="Données invalides"),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        return $this->reservationService->updateResevation($data, $id);
    }

    /**
     * @OA\Post(
     *     path="/api/reservations/{id}/confirm",
     *     summary="Confirmer une réservation",
     *     tags={"Réservations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Réservation confirmée"),
     *     @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
    public function confirm($id)
    {
        return $this->reservationService->confirmReservation($id, Auth::id());
    }

    /**
     * @OA\Put(
     *     path="/api/reservations/{id}/cancel",
     *     summary="Annuler une réservation",
     *     tags={"Réservations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Réservation annulée"),
     *     @OA\Response(response=400, description="La réservation ne peut pas être annulée"),
     *     @OA\Response(response=404, description="Réservation non trouvée")
     * )
     */
    public function cancel($id)
    {
        return $this->reservationService->cancelReservation($id, Auth::id());
    }
}
