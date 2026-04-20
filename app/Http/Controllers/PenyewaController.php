<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenyewaController extends Controller
{
    public function index() {
        return view('penyewa.dashboard');
    }

    public function searchPage() {
        return view('rooms.search_room');
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
