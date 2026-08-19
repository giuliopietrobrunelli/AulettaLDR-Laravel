<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistrationController;

Route::middleware('guest')->group(function () {
    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::get('/register/alternative', fn () => view('auth.register-alternative'))->name('register.alternative');

    // stessa URI '/register', metodi diversi: nessun conflitto di nome
    Route::post('/register', [RegistrationController::class, 'store'])->name('register');

    Route::get('/register/verify', [RegistrationController::class, 'showVerifyForm'])->name('register.verify');
    Route::post('/register/verify', [RegistrationController::class, 'verify'])->name('register.verify.store');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register-alternative', function(){
    return view('auth.register-alternative');
})->middleware(['guest'])->name('register.alternative');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/turni', fn () => view('admin.turni'))->name('admin.turni');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/app', function () {
        return view('app');
    })->name('dashboard');
});
