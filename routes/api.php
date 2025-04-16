<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\SeanceController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiegeController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'show']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::delete('/profile', [AuthController::class, 'deleteAccount']);
});


//  Consultation des séances et films (disponible pour tout le monde)
Route::get('/seances/{idfilm}', [SeanceController::class, 'getAllSeancesWithFilms']);
Route::get('/seances/{type}', [SeanceController::class, 'showByType']);// avec query ?type=VIP

// Pour tout utilisateur connecté
Route::middleware('auth:api')->group(function () {
    Route::apiResource('films', FilmController::class);
    Route::apiResource('seances', SeanceController::class);
    Route::get('/seances/{id}/sieges', [SiegeController::class, 'getBySeance']);
});

// Routes accessibles uniquement aux **administrateurs**
Route::middleware(['auth:api', 'role:admin'])->group(function () {
//    Route::apiResource('films', FilmController::class);
//    Route::apiResource('salles', SalleController::class);
//    Route::apiResource('seances', SeanceController::class);
//    Route::apiResource('sieges', SiegeController::class);

    // Méthodes personnalisées de reservation
//    Route::patch('/reservations/{id}/confirm', [ReservationController::class, 'confirm']);
//    Route::patch('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

    //statistique
    Route::get('/admin/dashboard', [DashboardController::class, 'getDashboardStats']);
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('reservations', ReservationController::class);
//    Route::post('/reservations', [ReservationController::class, 'createReservation']);

    //paiment
    Route::post('/payment', [PaymentController::class, 'createCheckoutSession'])->name('payment.create');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook']);

//Quand tu ouvres le lien de paiement Stripe dans le navigateur, Laravel ne sait pas que tu es authentifié sur Postman. L'authentification est stockée dans une session ou via un token, mais quand Stripe redirige après le paiement, le navigateur ne transmet pas l'authentification.
//👉 Résultat : Laravel pense que tu n'es pas connecté et essaie de te rediriger vers /login, mais cette route n'existe pas.
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');

    //generer pdf ticket
    Route::get('/generate-ticket/{reservationId}', [TicketController::class, 'generateTicket']);

});







