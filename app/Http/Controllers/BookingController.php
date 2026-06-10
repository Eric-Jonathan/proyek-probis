<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function history() {
        $userId = Auth::id();

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

        // 1. Ambil data transaksi riwayat booking penyewa
        $bookings = \App\Models\Booking::with(['roomDetail.room', 'rating'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Hitung statistik untuk header card
        $stats = [
            'total'     => $bookings->count(),
            'completed' => $bookings->where('status', 2)->count(),
            'active'    => $bookings->where('status', 1)->count(),
            'cancelled' => $bookings->where('status', 0)->count(),
        ];

        return view('rooms.history', compact('bookings', 'stats'));
    }

    public function showBookingForm(Request $request, $room_id)
    {
        $room = Room::with(['images', 'facilities'])->findOrFail($room_id);

        // 1. Hitung parameter waktu nyata dari Kalender Frontend
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : Carbon::tomorrow();
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : Carbon::tomorrow();
        
        // Total hari sewa (inklusif)
        $totalDays = max(1, $startDate->diffInDays($endDate) + 1);

        // 2. Tentukan Default Hitungan Jam (Misal untuk kalkulasi awal invoice)
        $durationHours = 8; // Standar sewa harian/jam jika belum diubah di form

        // 3. MESIN UTAMA: Pilihan Rumus Berdasarkan Kolom `jenis_harga`
        $basePriceCalculated = 0;
        $price = $room->price;
        $minPax = $room->min_order;

        switch ($room->jenis_harga) {
            case 'Pax':
                // Rumus: Harga x Jumlah Minimal Pax (Awal)
                $basePriceCalculated = $price * $minPax;
                break;

            case 'Hari':
                // Rumus: Harga x Jumlah Hari Sewa
                $basePriceCalculated = $price * $totalDays;
                break;

            case 'Jam':
                // Rumus: Harga x Durasi Jam x Jumlah Hari Sewa
                $basePriceCalculated = $price * $durationHours * $totalDays;
                break;

            case 'Pax_jam':
                // Skenario Barumu: Harga x Min Pax x Durasi Jam x Jumlah Hari Sewa
                $basePriceCalculated = $price * $minPax * $durationHours * $totalDays;
                break;
                
            default:
                $basePriceCalculated = $price;
        }

        return view('rooms.booking', compact('room', 'startDate', 'endDate', 'totalDays', 'basePriceCalculated', 'durationHours'));
    }

    public function storeBooking(Request $request, $room_id)
    {
        $room = Room::findOrFail($room_id);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'instansi' => 'required|string|max:255',
            'jenis_acara' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'total_capacity' => 'required|integer|min:1|max:' . $room->capacity,
            'sewa_tipe' => 'required|in:harian,jam',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
            'jam_mulai' => 'nullable|string',
            'jam_selesai' => 'nullable|string'
        ]);

        $startDateVal = $request->start_date;
        $endDateVal = $request->end_date;

        if ($request->sewa_tipe === 'jam') {
            // Kombinasi tanggal dengan jam mulai & selesai
            $jamMulai = str_replace('.', ':', $request->input('jam_mulai', '08:00'));
            $jamSelesai = str_replace('.', ':', $request->input('jam_selesai', '16:00'));
            $reqStart = $startDateVal . ' ' . $jamMulai . ':00';
            $reqEnd = $startDateVal . ' ' . $jamSelesai . ':00';
        } else {
            $reqStart = $startDateVal . ' 00:00:00';
            $reqEnd = $endDateVal . ' 23:59:59';
        }

        // =========================================================================
        // VALIDASI KETERSEDIAAN WAKTU (LOCK & CONCURRENCY CHECK)
        // =========================================================================
        $overlap = \App\Models\Booking::whereIn('status', [1, 2, 3])
            ->whereHas('details', function($q) use ($room_id) {
                $q->where('item_type', 1)->where('item_id', $room_id);
            })
            ->where(function($query) use ($reqStart, $reqEnd) {
                $query->where('start_date', '<', $reqEnd)
                      ->where('end_date', '>', $reqStart);
            })
            ->exists();

        if ($overlap) {
            return back()->withInput()->with('error', 'Maaf, ruangan sudah penuh (booked) pada tanggal dan jam tersebut. Silakan pilih waktu lain.');
        }

        // =========================================================================
        // KALKULASI HARGA - HARUS PERSIS SAMA DENGAN FORMULA booking.js
        // =========================================================================
        $carbonStart = \Illuminate\Support\Carbon::parse($startDateVal);
        $carbonEnd = \Illuminate\Support\Carbon::parse($endDateVal);
        $totalDays = max(1, $carbonStart->diffInDays($carbonEnd) + 1);

        $totalPax = intval($request->total_capacity);
        if ($totalPax < $room->min_order) {
            $totalPax = $room->min_order;
        }

        // Durasi jam
        $durationHours = 8; // default
        if ($request->sewa_tipe === 'jam') {
            $timeStart = str_replace('.', ':', $request->input('jam_mulai', '08:00'));
            $timeEnd = str_replace('.', ':', $request->input('jam_selesai', '16:00'));
            $dateStart = \Illuminate\Support\Carbon::parse("2026-01-01 " . $timeStart);
            $dateEnd = \Illuminate\Support\Carbon::parse("2026-01-01 " . $timeEnd);
            $diffMs = $dateEnd->timestamp - $dateStart->timestamp;
            $durationHours = ceil($diffMs / 3600);
            if ($durationHours <= 0) {
                $durationHours = 1;
            }
        }

        $basePrice = 0;
        $jenisHarga = strtolower(trim($room->jenis_harga));
        if ($jenisHarga === 'pax') {
            $basePrice = $room->price * $totalPax * $totalDays;
        } elseif ($jenisHarga === 'hari') {
            $basePrice = $room->price * $totalDays;
        } elseif ($jenisHarga === 'jam') {
            $basePrice = $room->price * $durationHours;
        } elseif ($jenisHarga === 'pax_jam') {
            $basePrice = $room->price * $totalPax * $durationHours * $totalDays;
        } else {
            $basePrice = $room->price;
        }

        // Hitung ekstra biaya addons
        $extraCost = 0;
        $addons = [];
        if ($request->has('services')) {
            foreach ($request->services as $service) {
                if ($service === 'Catering') {
                    $cost = 50000 * $totalPax * $totalDays;
                    $addons[] = [
                        'name' => 'Layanan Paket Katering Konsumsi',
                        'price' => $cost,
                        'item_id' => 101
                    ];
                    $extraCost += $cost;
                } elseif ($service === 'Dekorasi') {
                    $cost = 1500000;
                    $addons[] = [
                        'name' => 'Paket Dekorasi Panggung',
                        'price' => $cost,
                        'item_id' => 102
                    ];
                    $extraCost += $cost;
                } elseif ($service === 'IT') {
                    $cost = 750000;
                    $addons[] = [
                        'name' => 'Operator Teknis & Sound Live Streaming',
                        'price' => $cost,
                        'item_id' => 103
                    ];
                    $extraCost += $cost;
                }
            }
        }

        $grandTotalFinal = $basePrice + $extraCost;

        // =========================================================================
        // INSERT DATABASE
        // =========================================================================
        $booking = \App\Models\Booking::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'total' => $grandTotalFinal,
            'method_payment' => $request->payment_method,
            'event' => $request->instansi . ' - ' . $request->jenis_acara,
            'phone' => $request->phone,
            'notes' => $request->notes,
            'start_date' => $reqStart,
            'end_date' => $reqEnd,
            'status' => 3 // 3 = Belum Bayar / Pending Payment
        ]);

        // Detail 1: Sewa Gedung/Ruangan Utama
        \App\Models\BookingDetail::create([
            'booking_id' => $booking->booking_id,
            'item_name' => $room->name,
            'item_id' => $room->room_id,
            'item_type' => 1, // Room
            'item_price' => $basePrice,
            'status' => 1 // Active
        ]);

        // Detail 2+: Addon Services
        foreach ($addons as $addon) {
            \App\Models\BookingDetail::create([
                'booking_id' => $booking->booking_id,
                'item_name' => $addon['name'],
                'item_id' => $addon['item_id'],
                'item_type' => 2, // Facility
                'item_price' => $addon['price'],
                'status' => 1 // Active
            ]);
        }

        return redirect()->route('booking.transaction', ['booking_id' => $booking->booking_id])
            ->with('success', 'Pemesanan ruangan ' . $room->name . ' berhasil dibuat! Silakan lakukan pembayaran.');
    }

    public function transaction($booking_id)
    {
        $booking = \App\Models\Booking::with(['roomDetail.room', 'serviceDetails'])
            ->findOrFail($booking_id);

        $roomDetail = $booking->roomDetail;
        $room = $roomDetail ? $roomDetail->room : null;

        // Konfigurasi Midtrans dari services.php
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = config('services.midtrans.is_sanitized');
        Config::$is3ds        = config('services.midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => 'BOOKING-' . $booking->booking_id . '-' . time(),
                'gross_amount' => (int) $booking->total,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->username ?? 'Penyewa',
                'email' => Auth::user()->email ?? 'penyewa@example.com',
                'phone' => $booking->phone ?? '',
            ],
            'item_details' => [
                [
                    'id' => $room ? $room->room_id : 1,
                    'price' => (int) ($roomDetail ? $roomDetail->item_price : $booking->total),
                    'quantity' => 1,
                    'name' => $room ? substr($room->name, 0, 50) : 'Sewa Ruangan',
                ]
            ]
        ];

        foreach ($booking->serviceDetails as $service) {
            $params['item_details'][] = [
                'id' => $service->item_id,
                'price' => (int) $service->item_price,
                'quantity' => 1,
                'name' => substr($service->item_name, 0, 50),
            ];
        }

        $snapToken = '';
        $isSimulated = false;
        try {
            $snapToken = Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            $snapToken = 'MOCK-SNAP-TOKEN-' . uniqid();
            $isSimulated = true;
        }

        return view('rooms.transaction', compact('booking', 'room', 'snapToken', 'isSimulated'));
    }

    public function paymentCallback(Request $request, $booking_id)
    {
        $booking = \App\Models\Booking::findOrFail($booking_id);
        if ($request->input('status') === 'success') {
            $booking->update(['status' => 1]); // 1 = Booked/Paid/Active
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }
}
