<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PsychologistClientController;

Route::get('/events', [EventController::class, 'index']); // Public
Route::post('/register-user', [AuthController::class, 'registerUser']);
Route::post('/register-psychologist', [AuthController::class, 'registerPsychologist']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::post('/events/{id}/signup', [EventController::class, 'signup']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/psychologist/clients', [PsychologistClientController::class, 'index']);
    Route::post('/psychologist/clients/{client}/comments', [PsychologistClientController::class, 'storeComment']);
    Route::get('/client/profile', [PsychologistClientController::class, 'clientProfile']);
});
