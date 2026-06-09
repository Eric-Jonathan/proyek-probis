<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|integer',
            'kebersihan' => 'required|integer|between:1,5',
            'pelayanan'  => 'required|integer|between:1,5',
            'kenyamanan' => 'required|integer|between:1,5',
            'komentar'   => 'nullable|string',
        ]);

        // Cari booking untuk mengambil room_id jika item_id tidak terkirim
        $booking = \App\Models\Booking::with('roomDetail')->findOrFail($request->booking_id);
        $itemId = $request->item_id ?? ($booking->roomDetail->item_id ?? 0);

        \App\Models\Rating::create([
            'booking_id' => $request->booking_id,
            'item_id'    => $itemId,
            'item_type'  => $request->item_type ?? 1, // Default 1 = Room
            'kebersihan' => $request->kebersihan,
            'pelayanan'  => $request->pelayanan,
            'kenyamanan' => $request->kenyamanan,
            'komentar'   => $request->komentar,
        ]);

        return back()->with('success', 'Terima kasih atas penilaian Anda!');
    }
}