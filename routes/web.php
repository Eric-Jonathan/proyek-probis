<?php

use App\Http\Controllers\PeopleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PenyediaController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\OutsourceController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\WithdrawController;

use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/roomsList', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
Route::post('/rooms/store', [RoomController::class, 'store'])->name('rooms.store');
Route::get('/booking/{booking_id}/transaction', [BookingController::class, 'transaction'])->name('booking.transaction');
Route::post('/booking/{booking_id}/pay', [BookingController::class, 'payWithBalance'])->name('booking.pay');
Route::post('/booking/{booking_id}/payment-callback', [BookingController::class, 'paymentCallback'])->name('booking.payment_callback');
Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

Route::get('/autocompleteLocation', [ApiController::class, 'autocompleteLocation'])->name('autocompleteLocation');

Route::middleware(['auth', 'role:penyewa,penyedia,admin'])->group(function () {
    Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('rooms.show');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/chart-data', [AdminController::class, 'getChartData'])->name('admin.dashboard.chart');
    Route::get('/admin/acc_room', [AdminController::class, 'acc_room'])->name('admin.acc_room');
    
    Route::get('/admin/assign_outsource', [AdminController::class, 'outsourceAssignment'])->name('admin.assign_outsource');
    Route::post('/admin/outsource/assign/{assignment_id}', [AdminController::class, 'assignSurveyor'])->name('outsource.assign');
    Route::post('/admin/outsource/cancel/{assignment_id}', [AdminController::class, 'cancelAssignment'])->name('outsource.cancel');
    Route::post('/admin/room/approve/{room_id}', [AdminController::class, 'approveRoom'])->name('admin.room.approve');
    Route::post('/admin/room/reject/{room_id}', [AdminController::class, 'rejectRoom'])->name('admin.room.reject');

    Route::get('/admin/users', [PeopleController::class, 'people'])->name('admin.users');
    Route::post('/admin/users/insert', [PeopleController::class, 'insertPeople'])->name('users.insert');
    Route::post('/admin/users/update/{id}', [PeopleController::class, 'updatePeople'])->name('users.update');
    Route::post('/admin/users/delete/{id}', [PeopleController::class, 'deletePeople'])->name('users.delete');

    Route::get('/admin/formPeople', [PeopleController::class, 'formPeople'])->name('admin.formPeople');

    Route::get('/admin/outsource', [AdminController::class, 'outsource'])->name('admin.outsource');
    Route::get('/admin/outsource/create', [AdminController::class, 'create_outsource'])->name('admin.outsource.form');
    Route::post('/admin/outsource/store', [AdminController::class, 'store_outsource'])->name('admin.outsource.store');
    Route::get('/admin/outsource/edit/{outsource_id}', [AdminController::class, 'edit_outsource'])->name('admin.outsource.edit');
    Route::put('/admin/outsource/update/{outsource_id}', [AdminController::class, 'update_outsource'])->name('admin.outsource.update');
    Route::post('/admin/outsource/terminate/{id}', [AdminController::class, 'terminate_outsource'])->name('admin.outsource.terminate');
    
    Route::get('/admin/fines', [AdminController::class, 'fines'])->name('admin.fines');
    Route::post('/admin/fines/{id}/approve', [AdminController::class, 'approveFine'])->name('admin.fines.approve');
    Route::post('/admin/fines/{id}/reject', [AdminController::class, 'rejectFine'])->name('admin.fines.reject');
    Route::get('/admin/report/profitability', [AdminController::class, 'profitabilityReport'])->name('admin.report.profitability');
    Route::get('/admin/report/retention', [AdminController::class, 'retentionReport'])->name('admin.report.retention');
});

Route::middleware(['auth', 'role:penyedia'])->group(function () {
    Route::get('/penyedia/dashboard', [PenyediaController::class, 'index'])->name('penyedia.dashboard');
    Route::get('/penyedia/dashboard/chart', [PenyediaController::class, 'getChartData'])->name('penyedia.dashboard.chart');
    Route::get('/penyedia/history/{id}', [PenyediaController::class, 'detail_history'])->name('penyedia.detail_history');
    Route::get('/penyedia/fines/history', [PenyediaController::class, 'finesHistory'])->name('penyedia.fines.history');
    Route::get('/penyedia/report/occupancy', [PenyediaController::class, 'occupancyReport'])->name('penyedia.report.occupancy');
    Route::get('/penyedia/report/finance', [PenyediaController::class, 'financeReport'])->name('penyedia.report.finance');
});

Route::middleware(['auth', 'role:penyewa'])->group(function () {
    Route::get('/penyewa/dashboard', [PenyewaController::class, 'index'])->name('penyewa.dashboard');
    Route::get('/penyewa/search', [PenyewaController::class, 'searchPage'])->name('penyewa.search');
    Route::post('/ratings/store', [RatingController::class, 'store'])->name('ratings.store');
    Route::post('/penyewa/fine/{fine_id}/dismiss', [PenyewaController::class, 'dismissFine'])->name('penyewa.fine.dismiss');
    Route::post('/penyewa/fine/{fine_id}/pay', [PenyewaController::class, 'payFine'])->name('penyewa.fine.pay');
    Route::get('/penyewa/fine/{fine_id}/detail', [PenyewaController::class, 'fineDetail'])->name('penyewa.fine.detail');
});

Route::middleware(['auth', 'role:admin,outsource'])->group(function () {
    Route::get('/outsource/history/{id}', [OutsourceController::class, 'historyDetail'])->name('outsource.history.detail');
    Route::get('/outsource/history/{id}/pdf', [OutsourceController::class, 'downloadPDF'])->name('outsource.history.pdf');
});

Route::middleware(['auth', 'role:outsource'])->group(function () {
    Route::prefix('outsource')->name('outsource.')->group(function () {
        Route::get('/', [OutsourceController::class, 'index'])->name('dashboard');
        Route::get('/form/{assignment_id}', [OutsourceController::class, 'form'])->name('form');
        Route::get('/list_job', [OutsourceController::class, 'jobList'])->name('job');
        Route::post('/job/take/{assignment_id}', [OutsourceController::class, 'takeJob'])->name('job.take');
        Route::post('/job/submit/{assignment_id}', [OutsourceController::class, 'submitReport'])->name('job.submit');

        // Halaman Tabel History
        Route::get('/history', [OutsourceController::class, 'history'])->name('history');

        // Halaman Laporan Kinerja
        Route::get('/report', [OutsourceController::class, 'performanceReport'])->name('report');
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/form_penyewa', [PenyediaController::class, 'form'])->name('form');
Route::get('/bookings/history', [BookingController::class, 'history'])->name('bookings.history');

// Route::get('/room', [RoomController::class, 'show'])->name('room.show');

// Route::get('/booking', [PenyewaController::class, 'show'])->name('booking.show');
Route::get('/booking/{room_id}', [BookingController::class, 'showBookingForm'])->name('booking.show');
Route::post('/booking/{room_id}/store', [BookingController::class, 'storeBooking'])->name('booking.store');

Route::get('/test-rating', function () {

    $booking = (object) ['booking_id' => 123];
    $room    = (object) ['room_id' => 45, 'name' => 'Grand Ballroom Kencana'];

    return view('rooms.rating', compact('booking', 'room'));
});

// Route::post('/ratings', function () {
//     return back()->with('success', 'Pengecekan Berhasil! Ini adalah pesan sukses simulasi.');
// })->name('ratings.store');



Route::get('/penyedia/denda/{id}', [PenyediaController::class, 'denda'])->name('bookings.denda');

// Route simulasi proses kirim
Route::post('/penyedia/denda/store', [PenyediaController::class, 'storeDenda'])->name('penyedia.denda.store');



Route::get('/penyedia/list_booking', [PenyediaController::class, 'show_booking'])->name('bookings.index');


// Route untuk menampilkan halaman report dengan data asli
Route::get('/penyedia/report/{id}', [PenyediaController::class, 'report'])->name('bookings.report');

// Route simulasi simpan laporan
Route::post('/penyedia/report/store', function () {
    return redirect()->route('bookings.index')->with('success', 'Laporan penggunaan ruangan telah disimpan.');
})->name('penyedia.report.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/topup', [TopUpController::class, 'show'])->name('topup.show');
    Route::post('/topup/process', [TopUpController::class, 'process'])->name('topup.process');
    Route::post('/topup/callback', [TopUpController::class, 'callback'])->name('topup.callback');

    Route::get('/withdraw', [WithdrawController::class, 'show'])->name('withdraw.show');
    Route::post('/withdraw/process', [WithdrawController::class, 'process'])->name('withdraw.process');
});