<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Seance;
use App\Models\Siege;
use App\Services\ReservationService;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use App\Models\Reservation;

class PaymentController extends Controller
{
    protected $reservationService;
    /**
     * @OA\Post(
     *     path="/api/payment",
     *     summary="Créer une session de paiement Stripe",
     *     description="Cette méthode crée une session de paiement Stripe pour la réservation d'un film.",
     *     tags={"Payment"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reservation_id"},
     *             @OA\Property(property="reservation_id", type="integer", example=1, description="ID de la réservation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Session de paiement créée avec succès.",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string", example="https://checkout.stripe.com/pay/cs_test_abc123", description="URL pour le paiement")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur lors de la création de la session de paiement.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="message d'erreur", description="Message d'erreur détaillé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Utilisateur non authentifié.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Utilisateur non authentifié.", description="Message d'erreur pour l'authentification")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réservation introuvable.",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Réservation introuvable.", description="Message d'erreur si la réservation n'existe pas")
     *         )
     *     )
     * )
     */
    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
        $this->middleware('auth:api')->except(['success', 'cancel']); // Exclure success et cancel
    }
    // Méthode pour créer la session de paiement
    public function createCheckoutSession(Request $request)
    {
        // Définir la clé API Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié.'], 401);
            }

            // Récupérer la réservation depuis la base de données
            $reservation = Reservation::find($request->reservation_id);
            if (!$reservation) {
                return response()->json(['error' => 'Réservation introuvable.'], 404);
            }

            $seance = Seance::find($reservation->seance_id);
            $film = Film::find($seance->film_id);
            $siege = Siege::find($reservation->siege_id);
            //return response()->json(['$film' => $film], 200);
            // Convertir le prix en cents (Stripe attend un montant en cents)
            $priceInCents = intval($reservation->prix * 100);

            // Créer une session de paiement Stripe
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'], // Type de paiement (carte)
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd', // Devise
                            'product_data' => [
                                'name' => 'test', // Utilisation de 'title' ici
                                'description' => "Séance de {$seance->type_seance} pour le film {$film->title} - Siège numéro {$siege->numero} à {$seance->start_time}", // Description détaillée
                            ],
                            'unit_amount' => $reservation->prix, // Prix en cents
                        ],
                        'quantity' => 1, // Quantité
                    ],
                ],
                'mode' => 'payment', // Mode de paiement
                //'success_url' => route('payment.success', ['reservation_id' => $reservation->id]), // URL de succès
                'success_url' => route('payment.success', ['reservation_id' => $reservation->id]),
                'metadata' => [
                    'reservation_id' => $reservation->id, // On garde l'ID pour le récupérer plus tard
                ],
                'cancel_url' => route('payment.cancel'), // URL d'annulation
                'client_reference_id' => $user->id, // Lien avec la réservation
            ]);

            // Retourner l'URL de la session de paiement
            return response()->json(['url' => $session->url], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // Méthode pour gérer le succès du paiement
    public function success(Request $request)
    {
        try {
            $reservation = Reservation::find($request->reservation_id);
            if (!$reservation) {
                return response()->json(['error' => 'Réservation introuvable.'], 404);
            }

            // Mise à jour du statut de la réservation
            $reservation->update(['status' => 'accepted']);

            return response()->json(['message' => 'Réservation confirmée avec succès.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la mise à jour.',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    // Méthode pour gérer l'annulation du paiement
    public function cancel(Request $request)
    {
        return response()->json(['message' => 'Le paiement a été annulé.'], 200);
    }

    // Méthode pour gérer les webhooks de Stripe (confirmer le paiement)



    public function handleWebhook(Request $request)
    {
        \Log::info('Webhook Stripe reçu', ['payload' => $request->all()]);

        try {
            // Récupérer l'événement envoyé par Stripe
            $event = $request->all();

            // Vérifier que c'est bien un paiement réussi
            if ($event['type'] === 'checkout.session.completed') {
                $session = $event['data']['object'];

                // Récupérer la réservation liée
                $reservation = Reservation::find($session['metadata']['reservation_id']);

                if ($reservation) {
                    // Mise à jour du statut
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
