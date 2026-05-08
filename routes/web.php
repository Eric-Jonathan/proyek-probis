<?php

use App\Http\Controllers\PeopleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenyediaController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RatingController;

use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

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
Route::get('/rooms/{id}/transaction', [RoomController::class, 'transaction'])
    ->name('rooms.transaction');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/acc_room', [AdminController::class, 'acc_room'])->name('admin.acc_room');
    Route::get('/admin/users', [PeopleController::class, 'people'])->name('admin.users');
    Route::get('/admin/outsource', [AdminController::class, 'outsource'])->name('admin.outsource');
});

Route::middleware(['auth', 'role:penyedia'])->group(function () {
    Route::get('/penyedia/dashboard', [PenyediaController::class, 'index'])->name('penyedia.dashboard');
});

Route::middleware(['auth', 'role:penyewa'])->group(function () {
    Route::get('/penyewa/dashboard', [PenyewaController::class, 'index'])->name('penyewa.dashboard');
    Route::get('/penyewa/search', [PenyewaController::class, 'searchPage'])->name('penyewa.search');
    Route::post('/ratings/store', [RatingController::class, 'store'])->name('ratings.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/form_penyewa', [PenyediaController::class, 'form'])->name('form');


Route::get('/room', [RoomController::class, 'show'])->name('room.show');

Route::get('/booking', [PenyewaController::class, 'show'])->name('booking.show');

Route::get('/test-rating', function () {

    $booking = (object) ['booking_id' => 123];
    $room    = (object) ['room_id' => 45, 'name' => 'Grand Ballroom Kencana'];

    return view('rooms.rating', compact('booking', 'room'));
});

Route::post('/ratings', function () {
    return back()->with('success', 'Pengecekan Berhasil! Ini adalah pesan sukses simulasi.');
})->name('ratings.store');


// Route untuk melihat tampilan form denda
Route::get('/denda', function () {
    // Data dummy simulasi pesanan yang bermasalah
    $booking = (object) [
        'booking_id' => 1001,
        'customer_name' => 'Andi Wijaya',
        'room_name' => 'Grand Kencana Ballroom'
    ];
    return view('penyedia.denda', compact('booking'));
})->name('bookings.denda');

// Route simulasi proses kirim
Route::post('/admin/denda/store', function () {
    return back()->with('success', 'Pengajuan denda telah berhasil dikirim ke penyewa.');
})->name('penyedia.denda.store');



Route::get('/penyedia/list_booking', [PenyediaController::class, 'show_booking'])->name('bookings.index');


// Route untuk melihat tampilan form laporan
Route::get('/report/{id}', function ($id) {
    // Data dummy simulasi pesanan
    $booking = (object) [
        'booking_id' => $id,
        'customer_name' => 'Ahmad Subarjo',
        'room_name' => 'Grand Kencana Ballroom',
        'start_date' => '2026-05-12 08:00:00',
        'end_date' => '2026-05-12 18:00:00'
    ];
    return view('penyedia.report', compact('booking'));
})->name('bookings.report');

// Route simulasi simpan laporan
Route::post('/penyedia/report/store', function () {
    return redirect()->route('bookings.index')->with('success', 'Laporan penggunaan ruangan telah disimpan.');
})->name('penyedia.report.store');