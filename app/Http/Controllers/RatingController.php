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
        ]);

        DB::table('ratings')->insert([
            'booking_id' => $request->booking_id,
            'item_id'    => $request->item_id ?? 0, // ID Room/Facility
            'item_type'  => 1, 
            'kebersihan' => $request->kebersihan,
            'pelayanan'  => $request->pelayanan,
            'kenyamanan' => $request->kenyamanan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Terima kasih atas penilaian Anda!');
    }
}