<?php

use App\Http\Controllers\LlaveController;
use App\Livewire\Pages\Seguimientos\Seguimientos;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');



//===== Rutas para autenticación con LlaveMX ==========

Route::get('/auth-llave/login', [LlaveController::class, 'redirectToLlave'])->name('llave.login');
Route::get('/auth-llave/callback', [LlaveController::class, 'callback'])->name('llave.callback');
Route::get('/auth-llave/getRoles', [LlaveController::class, 'getRoles'])->name('llave.roles')->middleware('auth');
Route::get('/auth-llave/logout', [LlaveController::class, 'logout'])->name('llave.logout');

//===== End Rutas para autenticación con LlaveMX ==========


Route::get('/seguimientos', Seguimientos::class)->name('seguimientos');


//===== End Rutas para autenticación con LlaveMX ==========
Route::middleware('llave.auth')->group(function () {
    
});