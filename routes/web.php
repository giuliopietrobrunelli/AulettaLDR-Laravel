<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistrationController;
use App\Livewire\CalendarioMese;
use App\Livewire\CalendarioGiorno;

Route::middleware('guest')->group(function () {
    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::get('/register/alternative', fn() => view('auth.register-alternative'))->name('register.alternative');

    Route::post('/register', [RegistrationController::class, 'store'])->name('register');

    Route::get('/register/verify', [RegistrationController::class, 'showVerifyForm'])->name('register.verify');
    Route::post('/register/verify', [RegistrationController::class, 'verify'])->name('register.verify.store');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register-alternative', function () {
    return view('auth.register-alternative');
})->middleware(['guest'])->name('register.alternative');

Route::middleware(['auth'])->group(function () {
    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/turni', fn() => view('admin.layout'))->name('turni');
        Route::get('/utenti', fn() => view('admin.layout'))->name('utenti');
        Route::get('/prenotazioni', fn() => view('admin.layout'))->name('prenotazioni');
        Route::get('/feedback', fn() => view('admin.layout'))->name('feedback');
        Route::get('/impostazioni', fn() => view('admin.layout'))->name('impostazioni');
        Route::get('/statistiche', fn() => view('admin.layout'))->name('statistiche');
    });
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/app', function () {
        return view('app');
    })->name('dashboard');

    Route::get('/calendario', fn() => view('app'))->name('calendario.mese');
    Route::get('/calendario/giorno/{data}', fn() => view('app'))->name('calendario.giorno');
    Route::get('/prenotazioni', fn() => view('app'))->name('prenotazioni');
    Route::get('/impostazioni', fn() => view('app'))->name('impostazioni');
});