<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;

Route::get('/', [EventController::class, 'index']);
Route::resource('auth', AuthController::class)->only([
    'index',
    'store',
    'update',
    'destroy',
]);

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');
    Route::delete('/tasks/clear', [TaskController::class, 'clear'])->name('tasks.clear');
    Route::resource('tasks', TaskController::class)->except(['index', 'create', 'edit']);
});
