<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Seance;
use App\Models\Siege;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Reservation;


class PaymentController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
        $this->middleware('auth:api')->except(['success', 'cancel']);
    }

    /**
     * @OA\Post(
     *     path="/api/payment",
     *     summary="Créer une session de paiement Stripe",
     *     tags={"Payment"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reservation_id"},
     *             @OA\Property(property="reservation_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Session de paiement créée avec succès.",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string", example="https://checkout.stripe.com/pay/cs_test_abc123")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erreur lors de la création de la session de paiement."),
     *     @OA\Response(response=401, description="Utilisateur non authentifié."),
     *     @OA\Response(response=404, description="Réservation introuvable.")
     * )
     */
    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
            }

            $reservation = Reservation::find($request->reservation_id);
            if (!$reservation) {
                return response()->json(['error' => 'Réservation introuvable.'], 404);
            }

            $seance = Seance::find($reservation->seance_id);
            $film = Film::find($seance->film_id);
            $siege = Siege::find($reservation->siege_id);

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Réservation',
                            'description' => "Séance de {$seance->type_seance} pour le film {$film->title} - Siège {$siege->numero}",
                        ],
                        'unit_amount' => $reservation->prix * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success', ['reservation_id' => $reservation->id]),
                'cancel_url' => route('payment.cancel'),
                'metadata' => ['reservation_id' => $reservation->id],
            ]);

            return response()->json(['url' => $session->url], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/payment/success",
     *     summary="Gérer le succès du paiement",
     *     tags={"Payment"},
     *     @OA\Response(response=200, description="Réservation confirmée avec succès."),
     *     @OA\Response(response=404, description="Réservation introuvable.")
     * )
     */
    public function success(Request $request)
    {
        $reservation = Reservation::find($request->reservation_id);
        if (!$reservation) {
            return response()->json(['error' => 'Réservation introuvable.'], 404);
        }
        $reservation->update(['status' => 'accepted']);
        return response()->json(['message' => 'Réservation confirmée avec succès.'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/payment/cancel",
     *     summary="Gérer l'annulation du paiement",
     *     tags={"Payment"},
     *     @OA\Response(response=200, description="Le paiement a été annulé.")
     * )
     */
    public function cancel(Request $request)
    {
        return response()->json(['message' => 'Le paiement a été annulé.'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/payment/webhook",
     *     summary="Gérer les webhooks Stripe",
     *     tags={"Payment"},
     *     @OA\Response(response=200, description="Événement traité avec succès."),
     *     @OA\Response(response=500, description="Erreur lors du traitement du webhook.")
     * )
     */
    public function handleWebhook(Request $request)
    {
        \Log::info('Webhook Stripe reçu', ['payload' => $request->all()]);

        try {
            $event = $request->all();
            if ($event['type'] === 'checkout.session.completed') {
                $session = $event['data']['object'];
                $reservation = Reservation::find($session['metadata']['reservation_id']);
                if ($reservation) {
                    $reservation->update(['status' => 'reserved']);
                    return response()->json(['message' => 'Réservation mise à jour.'], 200);
                }
            }
            return response()->json(['message' => 'Événement ignoré.'], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur Webhook Stripe', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur Webhook'], 500);
        }
    }
}
