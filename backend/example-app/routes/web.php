<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return Inertia::render('Welcome');
    })->name('home');
    

Route::get('/calendar', function () {
    return view('app'); // or inertia render if using Inertia
});

Route::post('/register-user', [AuthController::class, 'registerUser']);
Route::post('/register-psychologist', [AuthController::class, 'registerPsychologist']);
