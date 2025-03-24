<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    // Processus de paiement
    public function pay(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric',
            'currency' => 'required|string',
        ]);

        $paymentIntent = PaymentIntent::create([
            'amount' => $data['amount'],
            'currency' => $data['currency'],
        ]);

        return response()->json(['client_secret' => $paymentIntent->client_secret]);
    }
}
