<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Wallet\Dashboard;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::post('/logout', LogoutController::class)->name('logout');
});
