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
        $recentBookings = \App\Models\Booking::with(['roomDetail.room', 'rating', 'fines'])
            ->where('user_id', $userId)
            ->get();

        // Urutkan koleksi dengan logika yang sama dengan riwayat sewa:
        $recentBookings = $recentBookings->sort(function ($a, $b) {
            $weightA = match ($a->status) {
                4 => 1,
                3 => 2,
                1 => 3,
                2 => 4,
                0 => 5,
                default => 6,
            };
            $weightB = match ($b->status) {
                4 => 1,
                3 => 2,
                1 => 3,
                2 => 4,
                0 => 5,
                default => 6,
            };

            if ($weightA !== $weightB) {
                return $weightA <=> $weightB;
            }

            // Jika keduanya status 3 (Cicilan), urutkan berdasarkan jatuh tempo terdekat
            if ($a->status == 3) {
                $dateA = $a->installment_due_date ? strtotime($a->installment_due_date) : 9999999999;
                $dateB = $b->installment_due_date ? strtotime($b->installment_due_date) : 9999999999;
                if ($dateA !== $dateB) {
                    return $dateA <=> $dateB;
                }
            }

            // Jika keduanya status 2 (Selesai), dahulukan yang belum dinilai (rating null)
            if ($a->status == 2) {
                $hasRatingA = $a->rating !== null ? 1 : 0;
                $hasRatingB = $b->rating !== null ? 1 : 0;
                if ($hasRatingA !== $hasRatingB) {
                    return $hasRatingA <=> $hasRatingB;
                }
            }

            // Fallback: created_at desc
            $timeA = $a->created_at ? strtotime($a->created_at) : 0;
            $timeB = $b->created_at ? strtotime($b->created_at) : 0;
            return $timeB <=> $timeA;
        })->take(5)->values();

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

        // 4. Deteksi denda aktif yang belum dibayar oleh penyewa
        $activeFine = \App\Models\Fine::whereHas('booking', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 1) // Approved
            ->where('is_paid', 0) // Unpaid
            ->with(['booking.roomDetail.room'])
            ->first();

        return view('penyewa.dashboard', compact('stats', 'recentBookings', 'unratedBooking', 'activeFine'));
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

    public function dismissFine(Request $request, $fine_id)
    {
        $fine = \App\Models\Fine::findOrFail($fine_id);

        // Verify that the fine booking belongs to the logged in user
        if ($fine->booking->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $fine->update(['is_dismissed' => 1]);

        return response()->json(['success' => true]);
    }

    public function payFine(Request $request, $fine_id)
    {
        $fine = \App\Models\Fine::with('booking.roomDetail.room')->findOrFail($fine_id);
        $renter = auth()->user();

        // 1. Verify that the fine booking belongs to the logged in user
        if ($fine->booking->user_id !== $renter->user_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // 2. Verify fine is approved and not yet paid
        if ($fine->status != 1) {
            return response()->json(['success' => false, 'message' => 'Denda ini belum disetujui atau telah ditolak.'], 400);
        }

        if ($fine->is_paid == 1) {
            return response()->json(['success' => false, 'message' => 'Denda ini sudah dibayar.'], 400);
        }

        // 3. Verify user has enough balance
        $amount = (int) $fine->nominal_denda;
        if ($renter->saldo < $amount) {
            return response()->json([
                'success' => false, 
                'message' => 'Saldo Tempat-In Anda (Rp ' . number_format($renter->saldo, 0, ',', '.') . ') tidak mencukupi untuk membayar denda sebesar Rp ' . number_format($amount, 0, ',', '.') . '.'
            ], 400);
        }

        // 4. Process deduction & transfer in a database transaction
        \Illuminate\Support\Facades\DB::transaction(function() use ($fine, $renter, $amount) {
            // Deduct renter
            $renter->saldo -= $amount;
            $renter->save();

            // Add to room owner (provider)
            $room = $fine->booking->roomDetail->room ?? null;
            if ($room) {
                $owner = \App\Models\People::find($room->user_id);
                if ($owner) {
                    $owner->saldo += $amount;
                    $owner->save();
                }
            }

            // Mark fine as paid
            $fine->is_paid = 1;
            $fine->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran denda sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil memotong saldo Tempat-In Anda!',
            'new_balance' => $renter->saldo
        ]);
    }

    public function fineDetail($fine_id)
    {
        $fine = \App\Models\Fine::with(['booking.roomDetail.room', 'booking.user'])->findOrFail($fine_id);
        $renter = auth()->user();

        // Verify that the fine booking belongs to the logged in user
        if ($fine->booking->user_id !== $renter->user_id) {
            abort(403, 'Akses ditolak.');
        }

        return view('penyewa.fine_detail', compact('fine'));
    }
}
