<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;

class PenyediaController extends Controller
{
    public function index() {
        return view('penyedia.dashboard');
    }

    public function form() {
        return view('penyedia.form');
    }

    public function detail_history($id) {
        return view('penyedia.detail_history', compact('id'));
    }

    public function show_booking(Request $request) {
        $penyediaId = auth::id();

        // Query menggunakan Eager Loading
        $query = Booking::with(['user', 'details.room'])
            ->whereHas('details.room', function ($q) use ($penyediaId) {
                $q->where('user_id', $penyediaId);
            });

        // Pencarian simpel
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_id', 'like', "%$search%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('username', 'like', "%$search%");
                  });
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Statistik
        $statsQuery = Booking::whereHas('details.room', function ($q) use ($penyediaId) {
            $q->where('user_id', $penyediaId);
        });

        $totalOrder   = (clone $statsQuery)->count();
        $pendingOrder = (clone $statsQuery)->where('status', 1)->count();
        $successOrder = (clone $statsQuery)->where('status', 2)->count();
        $cancelOrder  = (clone $statsQuery)->where('status', 0)->count();

        $bookings = $query->latest()->paginate(10)->withQueryString();

        return view('penyedia.list_booking', compact(
            'bookings', 'totalOrder', 'pendingOrder', 'successOrder', 'cancelOrder'
        ));
    }

    public function report($id) {
        $booking = Booking::with(['user', 'details.room'])->findOrFail($id);

        // Pastikan penyedia hanya bisa membuat laporan untuk ruangan miliknya
        if ($booking->details->room->user_id !== auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melaporkan pesanan ini.');
        }

        return view('penyedia.report', compact('booking'));
    }

    public function denda($id)
    {
        // Mengambil data booking beserta relasi user dan room
        $booking = Booking::with(['user', 'details.room'])->findOrFail($id);

        // Proteksi: Hanya penyedia pemilik ruangan yang bisa akses
        if ($booking->details->room->user_id !== auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('penyedia.denda', compact('booking'));
    }
}
