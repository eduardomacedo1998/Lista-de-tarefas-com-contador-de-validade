<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// cria direcionamento para a view welcome.blade.php usando controller
// Route::get('/', [App\Http\Controllers\UsuarioController::class, 'index']);

use App\Http\Controllers\AuthController;

Route::get('/',[App\Http\Controllers\EventController::class, 'index']);

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

// Dashboard for authenticated users - handled by TaskController index
Route::get('/dashboard', [App\Http\Controllers\TaskController::class, 'index'])->middleware('auth')->name('dashboard');

// Clear all tasks for authenticated user - register before resource routes to avoid collisions with tasks/{task}
Route::delete('tasks/clear', [App\Http\Controllers\TaskController::class, 'clear'])->name('tasks.clear')->middleware('auth');
// Confirm page for clearing tasks (GET)
Route::get('tasks/clear/confirm', [App\Http\Controllers\TaskController::class, 'confirmClear'])->name('tasks.clear.confirm')->middleware('auth');

// Task management routes - restrict {task} to numeric ids to prevent collisions (e.g., 'clear')
Route::resource('tasks', App\Http\Controllers\TaskController::class)
	->except(['create', 'show', 'edit'])
	->middleware('auth')
	->where(['task' => '[0-9]+']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');