<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        $bookings = \App\Models\Booking::with(['roomDetail.room', 'rating', 'fines'])
            ->where('user_id', $userId)
            ->get();

        // Urutkan koleksi:
        // - status 4 (Menunggu Pembayaran) -> Urutan ke-1
        // - status 3 (Cicilan) -> Urutan ke-2, diurutkan berdasarkan jatuh tempo terdekat sampai terjauh (installment_due_date ASC)
        // - status 1 (Terjadwal / Lunas) -> Urutan ke-3
        // - status 2 (Selesai) -> Urutan ke-4, prioritaskan yang belum dirating (rating null) sebelum yang sudah dirating
        // - status 0 (Batal) -> Urutan ke-5
        $bookings = $bookings->sort(function ($a, $b) {
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
                    return $hasRatingA <=> $hasRatingB; // 0 (belum dinilai) pertama, lalu 1 (sudah dinilai)
                }
            }

            // Default fallback: urutkan berdasarkan created_at desc (terbaru ke terlama)
            $timeA = $a->created_at ? strtotime($a->created_at) : 0;
            $timeB = $b->created_at ? strtotime($b->created_at) : 0;
            return $timeB <=> $timeA;
        })->values();

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
        $totalDays = (int) round(max(1, $startDate->diffInDays($endDate) + 1));

        // 2. Tentukan Default Hitungan Jam (Misal untuk kalkulasi awal invoice)
        $durationHours = 8; // Standar sewa harian/jam jika belum diubah di form

        // 3. MESIN UTAMA: Pilihan Rumus Berdasarkan Kolom `jenis_harga`
        $basePriceCalculated = 0;
        $price = $room->price;
        $minPax = $room->min_order;

        switch ($room->jenis_harga) {
            case 'Pax':
            case 'pax':
                // Rumus: Harga x Jumlah Minimal Pax (Awal) - Tanpa Hari
                $basePriceCalculated = $price * $minPax;
                break;

            case 'Pax_hari':
            case 'pax_hari':
                // Rumus: Harga x Jumlah Minimal Pax (Awal) x Jumlah Hari Sewa
                $basePriceCalculated = $price * $minPax * $totalDays;
                break;

            case 'Hari':
            case 'hari':
                // Rumus: Harga x Jumlah Hari Sewa
                $basePriceCalculated = $price * $totalDays;
                break;

            case 'Jam':
            case 'jam':
                // Rumus: Harga x Durasi Jam x Jumlah Hari Sewa
                $basePriceCalculated = $price * $durationHours * $totalDays;
                break;

            case 'Pax_jam':
            case 'pax_jam':
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
            'phone' => 'required|string|regex:/^[0-9]{10,12}$/',
            'total_capacity' => 'required|integer|min:1|max:' . $room->capacity,
            'sewa_tipe' => 'required|in:harian,jam',
            'payment_scheme' => 'required|in:full,installment',
            'notes' => 'nullable|string',
            'jam_mulai' => 'nullable|string',
            'jam_selesai' => 'nullable|string'
        ], [
            'phone.regex' => 'Nomor WhatsApp aktif harus terdiri dari 10 hingga 12 digit angka.',
            'phone.required' => 'Nomor WhatsApp aktif wajib diisi.'
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
        $totalDays = (int) round(max(1, $carbonStart->diffInDays($carbonEnd) + 1));

        $totalPax = intval($request->total_capacity);
        $jenisHarga = strtolower(trim($room->jenis_harga));
        $needsMinOrder = ($jenisHarga === 'pax' || $jenisHarga === 'pax_jam');

        if ($needsMinOrder && $totalPax < $room->min_order) {
            return back()->withInput()->with('error', 'Jumlah tamu kurang dari batas minimal order (' . $room->min_order . ' pax).');
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
            $basePrice = $room->price * $totalPax;
        } elseif ($jenisHarga === 'pax_hari') {
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

        $baseTotal = $basePrice + $extraCost;
        $serviceFee = (int) round($baseTotal * 0.05);
        $grandTotalFinal = $baseTotal + $serviceFee;

        // Calculate payment amount based on selected scheme
        $scheme = $request->input('payment_scheme', 'full');
        $renter = Auth::user();
        $booking = null;

        // Perform booking creation inside a transaction (no balance deduction yet)
        DB::transaction(function() use ($renter, $grandTotalFinal, $scheme, $reqStart, $reqEnd, $request, $basePrice, $addons, $room, &$booking) {
            // Create booking with status 4 (Menunggu Pembayaran)
            $booking = \App\Models\Booking::create([
                'user_id' => $renter->user_id,
                'total' => $grandTotalFinal,
                'paid_amount' => 0,
                'installments_paid' => 0,
                'method_payment' => ($scheme === 'full') ? 'Saldo (Lunas)' : 'Saldo (Cicilan 3x)',
                'event' => $request->instansi . ' - ' . $request->jenis_acara,
                'phone' => $request->phone,
                'notes' => $request->notes,
                'start_date' => $reqStart,
                'end_date' => $reqEnd,
                'status' => 4 // 4 = Menunggu Pembayaran
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
        });

        $msg = 'Pemesanan ruangan ' . $room->name . ' berhasil dibuat! Silakan selesaikan pembayaran.';

        return redirect()->route('booking.transaction', $booking->booking_id)->with('success', $msg);
    }

    public function transaction($booking_id)
    {
        $booking = \App\Models\Booking::with(['roomDetail.room.owner', 'serviceDetails'])
            ->findOrFail($booking_id);

        $roomDetail = $booking->roomDetail;
        $room = $roomDetail ? $roomDetail->room : null;

        $depositPercent = $room ? $room->deposit_percent : 0;

        // Dynamic base and total calculations
        $baseTotal = ($roomDetail->item_price ?? 0) + $booking->serviceDetails->sum('item_price');
        $serviceFee = (int) round($baseTotal * 0.05);
        $renterTotal = $baseTotal + $serviceFee;

        $deposit = (int)ceil(($depositPercent / 100) * $baseTotal);

        $nextPayment = 0;
        if ($booking->status == 4) {
            $isInstallment = str_contains(strtolower($booking->method_payment), 'cicilan');
            if ($isInstallment) {
                $nextPayment = (int)ceil($baseTotal / 3) + $deposit + $serviceFee;
            } else {
                $nextPayment = $renterTotal;
            }
        } elseif ($booking->status == 3) {
            if ($booking->installments_paid == 1) {
                $nextPayment = (int)ceil($baseTotal / 3);
            } elseif ($booking->installments_paid == 2) {
                $nextPayment = $baseTotal - ($booking->paid_amount - $deposit - $serviceFee);
            }
        } else {
            $nextPayment = $renterTotal - $booking->paid_amount;
        }

        return view('rooms.transaction', compact('booking', 'room', 'nextPayment', 'deposit'));
    }

    public function payWithBalance(Request $request, $booking_id)
    {
        $booking = \App\Models\Booking::with('roomDetail.room')->findOrFail($booking_id);
        $renter = Auth::user();

        // 1. Verify user is the booking requester
        if ($booking->user_id !== $renter->user_id) {
            abort(403, 'Akses ditolak.');
        }

        // 2. Verify booking is unpaid (status 4) or partially paid (status 3)
        if ($booking->status != 3 && $booking->status != 4) {
            return redirect()->route('booking.transaction', $booking->booking_id)->with('error', 'Transaksi ini sudah lunas atau dibatalkan.');
        }

        // 3. Calculate next payment amount
        $room = $booking->roomDetail->room ?? null;
        $depositPercent = $room ? $room->deposit_percent : 0;

        // Dynamic base and total calculations
        $baseTotal = ($booking->roomDetail->item_price ?? 0) + $booking->serviceDetails->sum('item_price');
        $serviceFee = (int) round($baseTotal * 0.05);
        $renterTotal = $baseTotal + $serviceFee;

        $deposit = (int)ceil(($depositPercent / 100) * $baseTotal);
        $nextPayment = 0;

        if ($booking->status == 4) {
            $isInstallment = str_contains(strtolower($booking->method_payment), 'cicilan');
            if ($isInstallment) {
                $nextPayment = (int)ceil($baseTotal / 3) + $deposit + $serviceFee;
            } else {
                $nextPayment = $renterTotal;
            }
        } elseif ($booking->status == 3) {
            if ($booking->installments_paid == 1) {
                $nextPayment = (int)ceil($baseTotal / 3);
            } elseif ($booking->installments_paid == 2) {
                $nextPayment = $baseTotal - ($booking->paid_amount - $deposit - $serviceFee);
            } else {
                return redirect()->route('booking.transaction', $booking->booking_id)->with('error', 'Cicilan Anda sudah lunas.');
            }
        } else {
            return redirect()->route('booking.transaction', $booking->booking_id)->with('error', 'Transaksi ini sudah lunas.');
        }

        // 4. Check balance
        if ($renter->saldo < $nextPayment) {
            return back()->withErrors(['saldo' => 'Saldo Anda tidak mencukupi untuk melakukan pembayaran sebesar Rp ' . number_format($nextPayment, 0, ',', '.') . '. Silakan top up saldo terlebih dahulu.']);
        }

        // 5. Perform deduction and transfer in a database transaction
        DB::transaction(function() use ($booking, $renter, $nextPayment, $baseTotal, $serviceFee) {
            // Deduct renter
            $renter->saldo -= $nextPayment;
            $renter->save();

            // Calculate 5% service fee + 5% provider commission
            $isFirstPayment = ($booking->status == 4);
            if ($isFirstPayment) {
                $basePayment = $nextPayment - $serviceFee;
                $adminShare = $serviceFee + (int) round($basePayment * 0.05);
                $ownerShare = $nextPayment - $adminShare;
            } else {
                $basePayment = $nextPayment;
                $adminShare = (int) round($basePayment * 0.05);
                $ownerShare = $nextPayment - $adminShare;
            }

            $admin = People::where('role', 'admin')->first();
            if ($admin) {
                $admin->saldo += $adminShare;
                $admin->save();
            } else {
                $ownerShare = $nextPayment;
            }

            // Add to room owner (provider)
            $room = $booking->roomDetail->room ?? null;
            if ($room) {
                $owner = People::find($room->user_id);
                if ($owner) {
                    $owner->saldo += $ownerShare;
                    $owner->save();
                }
            }

            // Update booking status & tracking
            if ($booking->status == 4) {
                $isInstallment = str_contains(strtolower($booking->method_payment), 'cicilan');
                $booking->paid_amount = $nextPayment;
                if ($isInstallment) {
                    $booking->installments_paid = 1;
                    $booking->status = 3; // Cicilan (Belum Lunas)
                    
                    // Hitung jatuh tempo dinamis berdasarkan durasi sewa, dihitung relatif terhadap created_at booking
                    $createdAt = \Carbon\Carbon::parse($booking->created_at);
                    $startDateTime = \Carbon\Carbon::parse($booking->start_date);
                    $endDateTime = \Carbon\Carbon::parse($booking->end_date);
                    $durationDays = max(1, $startDateTime->diffInDays($endDateTime) + 1);

                    if ($durationDays < 7) {
                        $offset = $booking->installments_paid * 2;
                        $booking->installment_due_date = $createdAt->copy()->addDays($offset)->toDateString();
                    } elseif ($durationDays <= 30) {
                        $offset = $booking->installments_paid;
                        $booking->installment_due_date = $createdAt->copy()->addWeeks($offset)->toDateString();
                    } else {
                        $offset = $booking->installments_paid;
                        $booking->installment_due_date = $createdAt->copy()->addMonths($offset)->toDateString();
                    }
                } else {
                    $booking->installments_paid = 3;
                    $booking->status = 1; // Terjadwal (Lunas)
                    $booking->installment_due_date = null;
                }
            } else {
                $booking->paid_amount += $nextPayment;
                $booking->installments_paid += 1;
                
                if ($booking->installments_paid >= 3) {
                    $booking->status = 1; // 1 = Terjadwal (Lunas)
                    $booking->installment_due_date = null;
                } else {
                    // Hitung jatuh tempo dinamis berdasarkan durasi sewa, dihitung relatif terhadap created_at booking
                    $createdAt = \Carbon\Carbon::parse($booking->created_at);
                    $startDateTime = \Carbon\Carbon::parse($booking->start_date);
                    $endDateTime = \Carbon\Carbon::parse($booking->end_date);
                    $durationDays = max(1, $startDateTime->diffInDays($endDateTime) + 1);

                    if ($durationDays < 7) {
                        $offset = $booking->installments_paid * 2;
                        $booking->installment_due_date = $createdAt->copy()->addDays($offset)->toDateString();
                    } elseif ($durationDays <= 30) {
                        $offset = $booking->installments_paid;
                        $booking->installment_due_date = $createdAt->copy()->addWeeks($offset)->toDateString();
                    } else {
                        $offset = $booking->installments_paid;
                        $booking->installment_due_date = $createdAt->copy()->addMonths($offset)->toDateString();
                    }
                }
            }
            $booking->save();
        });

        $msg = ($booking->status == 1)
            ? 'Pembayaran berhasil! Booking #' . $booking->booking_id . ' Anda sekarang LUNAS.'
            : 'Pembayaran cicilan ke-' . $booking->installments_paid . ' berhasil sebesar Rp ' . number_format($nextPayment, 0, ',', '.') . '!';

        return redirect()->route('penyewa.dashboard')->with('success', $msg);
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
