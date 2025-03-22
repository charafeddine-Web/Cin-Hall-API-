<?php

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

Route::middleware(['auth:api'])->group(function () {
    Route::post('/users', [AuthController::class, 'index']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/{id}', [AuthController::class, 'deletecompte']);
    Route::post('/user/{id}', [AuthController::class, 'updateProfile']);


    Route::get('/user', [AuthController::class, 'user']);
});
