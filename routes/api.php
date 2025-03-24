<?php

use App\Http\Controllers\FilmController;
use App\Http\Controllers\SalleController;
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

});
