<?php

use App\Http\Controllers\FilmController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\SeanceController;
use App\Http\Controllers\SeatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users', [AuthController::class, 'index']);

Route::middleware(['auth:api'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/{id}', [AuthController::class, 'deletecompte']);
    Route::PUT('/user/{id}', [AuthController::class, 'updateProfile']);


    Route::get('/user', [AuthController::class, 'user']);


    //fillms
    Route::get('films', [FilmController::class, 'index']);
    Route::get('films/{id}', [FilmController::class, 'show']);
    Route::post('films', [FilmController::class, 'store']);
    Route::put('films/{id}', [FilmController::class, 'update']);
    Route::delete('films/{id}', [FilmController::class, 'destroy']);

    //Salles
    Route::get('salles', [SalleController::class, 'index']);
    Route::get('salles/{id}', [SalleController::class, 'show']);
    Route::post('salles', [SalleController::class, 'store']);
    Route::put('salles/{id}', [SalleController::class, 'update']);
    Route::delete('salles/{id}', [SalleController::class, 'destroy']);

    //Seance
    Route::get('seances', [SeanceController::class, 'index']);
    Route::get('seances/{id}', [SeanceController::class, 'show']);
    Route::post('seances', [SeanceController::class, 'store']);
    Route::put('seances/{id}', [SeanceController::class, 'update']);
    Route::delete('seances/{id}', [SeanceController::class, 'destroy']);

    //Seat
    Route::get('seats', [SeatController::class, 'index']);
    Route::get('seats/{id}', [SeatController::class, 'show']);
    Route::post('seats', [SeatController::class, 'store']);
    Route::put('seats/{id}', [SeatController::class, 'update']);
    Route::delete('seats/{id}', [SeatController::class, 'destroy']);

    //Reservations
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::get('reservations/{id}', [ReservationController::class, 'show']);
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::put('reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('reservations/{id}', [ReservationController::class, 'destroy']);

});
