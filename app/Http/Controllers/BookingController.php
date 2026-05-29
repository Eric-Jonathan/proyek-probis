<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    public function history() {
        return view('rooms.history');
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

        // Hitung total hari dari input date asli
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $totalDays = max(1, $startDate->diffInDays($endDate) + 1);

        // Ambil input pax dari form
        $totalPax = intval($request->total_capacity);
        if ($totalPax < $room->min_order) {
            $totalPax = $room->min_order;
        }

        // 1. Hitung ulang base price ruangan sesuai jenis_harga di DB dikali totalDays
        $basePrice = 0;
        if ($room->jenis_harga === 'pax') {
            $basePrice = $room->price * $totalPax * $totalDays;
        } elseif ($room->jenis_harga === 'hari') {
            $basePrice = $room->price * $totalDays;
        } // ... tambahkan kondisi skema 'jam' dan 'pax_jam' sesuai durasi jam input jika sewa_tipe == jam

        // 2. Hitung ekstra biaya addons layanan tambahan dikali totalDays (khusus katering)
        $extraCost = 0;
        if ($request->has('services')) {
            foreach ($request->services as $service) {
                if ($service === 'Catering') {
                    $extraCost += (50000 * $totalPax * $totalDays); // Dikali pax dan hari booking
                } elseif ($service === 'Dekorasi') {
                    $extraCost += 1500000; // Flat per event
                } elseif ($service === 'IT') {
                    $extraCost += 750000;  // Flat per event
                }
            }
        }

        $grandTotalFinal = $basePrice + $extraCost;

        // 3. Simpan data bersih hasil kalkulator ERP ke tabel bookings
        // $booking = Booking::create([
        //     'user_id' => Auth::id(),
        //     'total' => $grandTotalFinal,
        //     ...
        // ]);
    }
}
