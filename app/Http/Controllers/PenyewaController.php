<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenyewaController extends Controller
{
    public function index() {
        $userId = auth()->id();

        // Seeding data dummy booking jika user belum memiliki booking selesai agar bisa mencoba fitur rating
        $completedBookingExists = \App\Models\Booking::where('user_id', $userId)
            ->where(function($q) {
                $q->where('status', 2)
                  ->orWhere('end_date', '<', now());
            })
            ->exists();

        if (!$completedBookingExists) {
            $room = \App\Models\Room::first();
            if ($room) {
                $bookingSelesai = \App\Models\Booking::create([
                    'user_id'        => $userId,
                    'total'          => $room->price * 2 + 1000000, // Room + Catering
                    'method_payment' => 'Manual',
                    'event'          => 'Reuni Akbar Kuliah',
                    'phone'          => '8123456789',
                    'notes'          => 'Mohon sediakan kursi tambahan di barisan depan.',
                    'start_date'     => now()->subDays(5)->format('Y-m-d') . ' 08:00:00',
                    'end_date'       => now()->subDays(5)->format('Y-m-d') . ' 17:00:00',
                    'status'         => 2, // Selesai
                ]);

                \App\Models\BookingDetail::create([
                    'booking_id' => $bookingSelesai->booking_id,
                    'item_id'    => $room->room_id,
                    'item_name'  => $room->name,
                    'item_type'  => 1, // Room
                    'item_price' => $room->price * 2,
                    'status'     => 1,
                ]);

                \App\Models\BookingDetail::create([
                    'booking_id' => $bookingSelesai->booking_id,
                    'item_id'    => 101, // Catering
                    'item_name'  => 'Layanan Paket Katering Konsumsi',
                    'item_type'  => 2, // Service
                    'item_price' => 1000000,
                    'status'     => 1,
                ]);
            }
        }

        // 1. Hitung statistik persewaan
        $stats = [
            'total'     => \App\Models\Booking::where('user_id', $userId)->count(),
            'active'    => \App\Models\Booking::where('user_id', $userId)->where('status', 1)->count(),
            'completed' => \App\Models\Booking::where('user_id', $userId)->where('status', 2)->count(),
            'cancelled' => \App\Models\Booking::where('user_id', $userId)->where('status', 0)->count(),
        ];

        // 2. Load recent bookings (terbaru)
        $recentBookings = \App\Models\Booking::with(['roomDetail.room', 'rating'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 3. Deteksi booking selesai yang BELUM di-rate untuk modal rating otomatis
        $unratedBooking = \App\Models\Booking::with('roomDetail.room')
            ->where('user_id', $userId)
            ->whereIn('status', [1, 2]) // Hanya booking aktif/selesai
            ->where(function($q) {
                $q->where('status', 2)
                  ->orWhere('end_date', '<', now());
            })
            ->whereDoesntHave('rating')
            ->orderBy('end_date', 'desc')
            ->first();

        return view('penyewa.dashboard', compact('stats', 'recentBookings', 'unratedBooking'));
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

        // Filter tipe sewa (jenis_harga)
        if ($request->filled('jenis_harga') && $request->jenis_harga !== 'all') {
            $query->where('jenis_harga', $request->jenis_harga);
        }

        // Filter range harga
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int)$request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int)$request->max_price);
        }

        // Filter ketersediaan tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            // Dapatkan ID ruangan yang sudah terbooking di tanggal tsb
            $bookedRoomIds = \App\Models\Booking::whereIn('status', [1, 2, 3])
                ->where(function($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $endDate . ' 23:59:59')
                      ->where('end_date', '>=', $startDate . ' 00:00:00');
                })
                ->whereHas('details', function($q) {
                    $q->where('item_type', 1);
                })
                ->get()
                ->pluck('details.item_id')
                ->unique()
                ->toArray();

            // Filter out ruangan yang terbooking
            $query->whereNotIn('room_id', $bookedRoomIds);
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
