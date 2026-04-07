<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenyediaController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\RoomController;

use Illuminate\Support\Facades\Route;

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:penyedia'])->group(function () {
    Route::get('/penyedia/dashboard', [PenyediaController::class, 'index'])->name('penyedia.dashboard');
});

Route::middleware(['auth', 'role:penyewa'])->group(function () {
    Route::get('/penyewa/dashboard', [PenyewaController::class, 'index'])->name('penyewa.dashboard');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/makan', function () {
    return view('rooms/pesan_ruang');
});

Route::get('/minum', [RoomController::class, 'show']);