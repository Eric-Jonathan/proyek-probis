<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenyewaController extends Controller
{
    public function index() {
        return view('penyewa.dashboard');
    }

    public function searchPage(Request $request) {
        $query = \App\Models\Room::with(['images', 'facilities', 'ratings'])
            ->where('status', 2); // Hanya ruangan yang disetujui Admin

        // Filter lokasi (pencarian nama ruangan atau alamat detail)
        if ($request->filled('location')) {
            $location = $request->location;
            $query->where(function($q) use ($location) {
                $q->where('location', 'like', '%' . $location . '%')
                  ->orWhere('name', 'like', '%' . $location . '%');
            });
        }

        // Filter kapasitas minimum
        if ($request->filled('capacity')) {
            $capacity = (int)$request->capacity;
            $query->where('capacity', '>=', $capacity);
        }

        // Urutkan di tingkat database (jika bukan berdasarkan rating)
        $sort = $request->input('sort', 'recommended');
        if ($sort === 'highest_price') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'lowest_price') {
            $query->orderBy('price', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $rooms = $query->get();

        // Hitung rata-rata rating secara dinamis untuk masing-masing ruangan
        $rooms = $rooms->map(function($room) {
            $ratings = $room->ratings;
            $count = $ratings->count();
            
            if ($count > 0) {
                // Rata-rata dari ketiga metrik kenyamanan, pelayanan, dan kebersihan
                $totalSum = $ratings->sum(function($r) {
                    return ($r->kebersihan + $r->pelayanan + $r->kenyamanan) / 3;
                });
                $average = round($totalSum / $count, 1);
            } else {
                $average = 0.0;
            }
            
            $room->average_rating = $average;
            $room->rating_count = $count;
            return $room;
        });

        // Urutkan berdasarkan rating di memori jika diminta
        if ($sort === 'highest_rating' || $sort === 'highest_star') {
            $rooms = $rooms->sortByDesc('average_rating')->values();
        }

        return view('rooms.search_room', compact('rooms'));
    }

    public function show()
    {
        $room = (object)[
            'id' => 1,
            'name' => 'Kontena Hotel',
            'capacity' => 50,
            'price' => 100000,
            'deposit_percent' => 30,
            'location' => "KH. Agus Salim No.106, Sisir, Kec. Batu, Kota Batu, Jawa Timur 65314",
            'rules' => [
                'Dilarang merokok di dalam kamar',
                'Tidak diperbolehkan membawa hewan peliharaan',
                'Check-in mulai pukul 14:00',
                'Menunjukkan identitas saat check-in'
            ],
            'description' => "tempatnya bagus mungkin",
            'embed_url' => "https://www.google.com/maps/embed?pb=..." // Pastikan ini URL embed yang valid
        ];
    
        // GANTI 'book' MENJADI 'room'
        return view('rooms/booking', compact('room'));
    }

    
}
