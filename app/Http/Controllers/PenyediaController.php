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
}
