<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;

class PenyediaController extends Controller
{
    public function index() {
        $penyediaId = Auth::id();

        // 1. Ambil data rating rata-rata untuk properti penyedia
        $roomsQuery = \App\Models\Room::where('user_id', $penyediaId)->where('status', '>=', 0);
        $roomIds = $roomsQuery->pluck('room_id')->toArray();

        $avgKebersihan = \App\Models\Rating::whereIn('item_id', $roomIds)->where('item_type', 1)->avg('kebersihan') ?: 0;
        $avgPelayanan = \App\Models\Rating::whereIn('item_id', $roomIds)->where('item_type', 1)->avg('pelayanan') ?: 0;
        $avgKenyamanan = \App\Models\Rating::whereIn('item_id', $roomIds)->where('item_type', 1)->avg('kenyamanan') ?: 0;

        $avgKebersihan = round($avgKebersihan, 1);
        $avgPelayanan = round($avgPelayanan, 1);
        $avgKenyamanan = round($avgKenyamanan, 1);

        // 2. Ambil data ruangan untuk tabel
        $rooms = $roomsQuery->latest()->get();

        // 3. Statistik performa properti
        $totalRooms = $roomsQuery->count();
        $activeRooms = \App\Models\Room::where('user_id', $penyediaId)->where('status', 2)->count();
        
        $bookingsForEarnings = \App\Models\Booking::whereIn('status', [1, 2])
            ->whereHas('details.room', function ($q) use ($penyediaId) {
                $q->where('user_id', $penyediaId);
            })->get();
            
        $totalEarnings = $bookingsForEarnings->sum(function($b) {
            $baseTotal = (int) round($b->total / 1.05);
            return $baseTotal - (int) round($baseTotal * 0.05);
        });

        // 4. Data untuk grafik pendapatan bulanan tahun ini (Jan-Des)
        $monthlyEarningsRaw = \App\Models\Booking::whereIn('status', [1, 2])
            ->whereHas('roomDetail', function($q) use ($roomIds) {
                $q->whereIn('item_id', $roomIds);
            })
            ->whereYear('start_date', date('Y'))
            ->get();

        $chartData = array_fill(1, 12, 0);
        foreach ($monthlyEarningsRaw as $b) {
            $month = (int) date('n', strtotime($b->start_date));
            $baseTotal = (int) round($b->total / 1.05);
            $netRevenue = $baseTotal - (int) round($baseTotal * 0.05);
            $chartData[$month] += $netRevenue;
        }
        $chartData = array_values($chartData);

        // 5. Query booking masuk (aktif & akan datang/sedang berjalan)
        $incomingBookings = \App\Models\Booking::where('status', '!=', 0)
            ->whereHas('roomDetail.room', function($q) use ($penyediaId) {
                $q->where('user_id', $penyediaId);
            })
            ->where('end_date', '>=', now()->startOfDay())
            ->with(['user', 'roomDetail.room'])
            ->orderBy('start_date', 'asc')
            ->get();

        return view('penyedia.dashboard', compact(
            'avgKebersihan', 'avgPelayanan', 'avgKenyamanan', 
            'rooms', 'totalRooms', 'activeRooms', 'totalEarnings', 'chartData',
            'incomingBookings'
        ));
    }

    public function getChartData(Request $request) {
        $penyediaId = Auth::id();
        $roomsQuery = \App\Models\Room::where('user_id', $penyediaId)->where('status', '>=', 0);
        $roomIds = $roomsQuery->pluck('room_id')->toArray();

        $filter = $request->query('filter', 'month');

        if (empty($roomIds)) {
            return response()->json([
                'labels' => [],
                'values' => [],
            ]);
        }

        $labels = [];
        $values = [];

        if ($filter === 'year') {
            $currentYear = (int)date('Y');
            $yearsData = [];
            for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
                $labels[] = (string)$y;
                $yearsData[$y] = 0;
            }

            $bookings = \App\Models\Booking::whereIn('status', [1, 2])
                ->whereHas('roomDetail', function($q) use ($roomIds) {
                    $q->whereIn('item_id', $roomIds);
                })
                ->whereYear('start_date', '>=', $currentYear - 4)
                ->get();

            foreach ($bookings as $b) {
                $bYear = (int)date('Y', strtotime($b->start_date));
                if (isset($yearsData[$bYear])) {
                    $baseTotal = (int) round($b->total / 1.05);
                    $netRevenue = $baseTotal - (int) round($baseTotal * 0.05);
                    $yearsData[$bYear] += $netRevenue;
                }
            }
            $values = array_values($yearsData);

        } elseif ($filter === 'week') {
            $bookings = \App\Models\Booking::whereIn('status', [1, 2])
                ->whereHas('roomDetail', function($q) use ($roomIds) {
                    $q->whereIn('item_id', $roomIds);
                })
                ->whereYear('start_date', date('Y'))
                ->whereMonth('start_date', date('m'))
                ->get();

            $weeks = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
            foreach ($bookings as $b) {
                $day = (int)date('d', strtotime($b->start_date));
                $baseTotal = (int) round($b->total / 1.05);
                $netRevenue = $baseTotal - (int) round($baseTotal * 0.05);
                if ($day <= 7) $weeks[1] += $netRevenue;
                elseif ($day <= 14) $weeks[2] += $netRevenue;
                elseif ($day <= 21) $weeks[3] += $netRevenue;
                elseif ($day <= 28) $weeks[4] += $netRevenue;
                else $weeks[5] += $netRevenue;
            }
            $labels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5'];
            $values = array_values($weeks);

        } elseif ($filter === 'day') {
            $dayMap = [
                'Sun' => 'Min', 'Mon' => 'Sen', 'Tue' => 'Sel', 'Wed' => 'Rab',
                'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab'
            ];
            $monthMap = [
                'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
                'Jul' => 'Jul', 'Aug' => 'Agu', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
            ];

            $daysData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                
                $engDay = date('D', strtotime($date));
                $engMonth = date('M', strtotime($date));
                $dayNum = date('d', strtotime($date));
                
                $indDay = $dayMap[$engDay] ?? $engDay;
                $indMonth = $monthMap[$engMonth] ?? $engMonth;
                
                $labels[] = "$indDay, $dayNum $indMonth";
                $daysData[$date] = 0;
            }

            $bookings = \App\Models\Booking::whereIn('status', [1, 2])
                ->whereHas('roomDetail', function($q) use ($roomIds) {
                    $q->whereIn('item_id', $roomIds);
                })
                ->where('start_date', '>=', date('Y-m-d 00:00:00', strtotime("-6 days")))
                ->get();

            foreach ($bookings as $b) {
                $bDate = date('Y-m-d', strtotime($b->start_date));
                if (isset($daysData[$bDate])) {
                    $baseTotal = (int) round($b->total / 1.05);
                    $netRevenue = $baseTotal - (int) round($baseTotal * 0.05);
                    $daysData[$bDate] += $netRevenue;
                }
            }
            $values = array_values($daysData);

        } else {
            $monthlyEarningsRaw = \App\Models\Booking::whereIn('status', [1, 2])
                ->whereHas('roomDetail', function($q) use ($roomIds) {
                    $q->whereIn('item_id', $roomIds);
                })
                ->whereYear('start_date', date('Y'))
                ->get();

            $chartData = array_fill(1, 12, 0);
            foreach ($monthlyEarningsRaw as $b) {
                $month = (int)date('n', strtotime($b->start_date));
                $baseTotal = (int) round($b->total / 1.05);
                $netRevenue = $baseTotal - (int) round($baseTotal * 0.05);
                $chartData[$month] += $netRevenue;
            }
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $values = array_values($chartData);
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    public function form() {
        return view('penyedia.form');
    }

    public function detail_history($id) {
        $booking = Booking::with(['user', 'roomDetail.room', 'serviceDetails'])->findOrFail($id);
        
        // Proteksi: Hanya penyedia pemilik ruangan yang bisa akses
        if ($booking->roomDetail->room->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('penyedia.detail_history', compact('booking'));
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
        $unpaidOrder  = (clone $statsQuery)->whereIn('status', [3, 4])->count();
        $cancelOrder  = (clone $statsQuery)->where('status', 0)->count();

        $bookings = $query->latest()->get();

        return view('penyedia.list_booking', compact(
            'bookings', 'totalOrder', 'pendingOrder', 'successOrder', 'unpaidOrder', 'cancelOrder'
        ));
    }

    public function report($id) {
        $booking = Booking::with(['user', 'details.room'])->findOrFail($id);

        // Pastikan penyedia hanya bisa membuat laporan untuk ruangan miliknya
        if ($booking->details->room->user_id !== auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melaporkan pesanan ini.');
        }

        return view('penyedia.report', compact('booking'));
    }

    public function denda($id)
    {
        // Mengambil data booking beserta relasi user dan room
        $booking = Booking::with(['user', 'details.room'])->findOrFail($id);

        // Proteksi: Hanya penyedia pemilik ruangan yang bisa akses
        if ($booking->details->room->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('penyedia.denda', compact('booking'));
    }

    public function storeDenda(Request $request)
    {
        $request->validate([
            'booking_id'    => 'required|integer',
            'jenis_denda'   => 'required|string|in:kerusakan,kebersihan,overtime,lainnya',
            'nominal_denda' => 'required|numeric|min:1',
            'keterangan'    => 'required|string',
            'bukti_denda'   => 'required|array',
            'bukti_denda.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $booking = Booking::with('details.room')->findOrFail($request->booking_id);

        // Proteksi: Hanya penyedia pemilik ruangan yang bisa akses
        if ($booking->details->room->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        // Simpan file bukti denda
        $filePaths = [];
        if ($request->hasFile('bukti_denda')) {
            foreach ($request->file('bukti_denda') as $file) {
                $filename = 'denda_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('upload_denda'), $filename);
                $filePaths[] = 'upload_denda/' . $filename;
            }
        }

        // Create Fine record
        \App\Models\Fine::create([
            'booking_id'    => $booking->booking_id,
            'jenis_denda'   => $request->jenis_denda,
            'nominal_denda' => $request->nominal_denda,
            'keterangan'    => $request->keterangan,
            'bukti_denda'   => $filePaths,
            'status'        => 0, // Pending
            'is_dismissed'  => 0
        ]);

        return redirect()->route('penyedia.fines.history')->with('success', 'Pengajuan denda telah berhasil dikirim ke Admin untuk ditinjau.');
    }

    public function finesHistory()
    {
        $penyediaId = Auth::id();

        $fines = \App\Models\Fine::whereHas('booking.roomDetail.room', function($q) use ($penyediaId) {
                $q->where('user_id', $penyediaId);
            })
            ->with(['booking.user', 'booking.roomDetail.room'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('penyedia.fines_history', compact('fines'));
    }

    public function occupancyReport(Request $request)
    {
        $penyediaId = Auth::id();
        $filter = $request->query('filter', '30'); // '30', '90', 'all'

        $rooms = \App\Models\Room::where('user_id', $penyediaId)->where('status', '>=', 0)->get();
        $roomIds = $rooms->pluck('room_id')->toArray();

        $startDate = null;
        $totalDays = 30;
        if ($filter === '30') {
            $startDate = now()->subDays(30);
            $totalDays = 30;
        } elseif ($filter === '90') {
            $startDate = now()->subDays(90);
            $totalDays = 90;
        } else {
            $earliestBooking = \App\Models\Booking::whereIn('status', [1, 2])
                ->whereHas('roomDetail', function($q) use ($roomIds) {
                    $q->whereIn('item_id', $roomIds);
                })
                ->orderBy('start_date', 'asc')
                ->first();
            if ($earliestBooking) {
                $earliestDate = \Carbon\Carbon::parse($earliestBooking->start_date);
                $totalDays = max(1, now()->diffInDays($earliestDate));
            } else {
                $totalDays = 30;
            }
        }

        $totalHoursPeriod = $totalDays * 24;

        $bookingsQuery = \App\Models\Booking::whereIn('status', [1, 2])
            ->whereHas('roomDetail', function($q) use ($roomIds) {
                $q->whereIn('item_id', $roomIds);
            });

        if ($startDate) {
            $bookingsQuery->where('start_date', '>=', $startDate);
        }

        $bookings = $bookingsQuery->with('roomDetail')->get();

        $roomStats = [];
        foreach ($rooms as $room) {
            $roomStats[$room->room_id] = [
                'room' => $room,
                'booking_count' => 0,
                'total_hours' => 0,
                'occupancy_rate' => 0,
            ];
        }

        foreach ($bookings as $b) {
            $roomId = $b->roomDetail->item_id ?? null;
            if ($roomId && isset($roomStats[$roomId])) {
                $start = \Carbon\Carbon::parse($b->start_date);
                $end = \Carbon\Carbon::parse($b->end_date);
                
                // Jika booking harian (dimulai 00:00:00 dan berakhir 23:59:59)
                if ($start->format('H:i:s') === '00:00:00' && $end->format('H:i:s') === '23:59:59') {
                    $days = max(1, $start->diffInDays($end) + 1);
                    $hours = $days * 24;
                } else {
                    $hours = max(1, $end->diffInHours($start));
                }

                $roomStats[$roomId]['booking_count'] += 1;
                $roomStats[$roomId]['total_hours'] += $hours;
            }
        }

        $recommendations = [];
        $chartLabels = [];
        $chartValues = [];

        foreach ($roomStats as $id => &$stats) {
            $rate = ($stats['total_hours'] / $totalHoursPeriod) * 100;
            $stats['occupancy_rate'] = round(min(100, $rate), 1);

            $chartLabels[] = $stats['room']->name;
            $chartValues[] = $stats['occupancy_rate'];

            if ($stats['occupancy_rate'] < 15) {
                $recommendations[] = [
                    'room_name' => $stats['room']->name,
                    'type' => 'promo',
                    'text' => "Ruangan '{$stats['room']->name}' memiliki tingkat okupansi yang rendah ({$stats['occupancy_rate']}%). Pertimbangkan untuk membuat diskon weekday promo atau menurunkan harga sewa dasar.",
                    'class' => 'warning'
                ];
            } elseif ($stats['occupancy_rate'] > 60) {
                $recommendations[] = [
                    'room_name' => $stats['room']->name,
                    'type' => 'price',
                    'text' => "Ruangan '{$stats['room']->name}' sangat diminati dengan okupansi tinggi ({$stats['occupancy_rate']}%). Anda dapat mencoba menaikkan harga dasar sebesar 5-10% pada weekend atau peak-hour.",
                    'class' => 'success'
                ];
            }
        }

        $totalBookingsCount = $bookings->count();
        $totalHoursAllRooms = array_sum(array_column($roomStats, 'total_hours'));
        $avgOccupancyRate = count($roomStats) > 0 ? array_sum(array_column($roomStats, 'occupancy_rate')) / count($roomStats) : 0;
        $avgOccupancyRate = round($avgOccupancyRate, 1);

        return view('penyedia.report_occupancy', compact(
            'roomStats', 'filter', 'totalBookingsCount', 'totalHoursAllRooms', 
            'avgOccupancyRate', 'recommendations', 'chartLabels', 'chartValues'
        ));
    }

    public function financeReport(Request $request)
    {
        $penyediaId = Auth::id();
        $filter = $request->query('filter', '30'); // '30', '90', 'all'

        $rooms = \App\Models\Room::where('user_id', $penyediaId)->where('status', '>=', 0)->get();
        $roomIds = $rooms->pluck('room_id')->toArray();

        $startDate = null;
        if ($filter === '30') {
            $startDate = now()->subDays(30);
        } elseif ($filter === '90') {
            $startDate = now()->subDays(90);
        }

        $bookingsQuery = \App\Models\Booking::whereIn('status', [1, 2])
            ->whereHas('roomDetail', function($q) use ($roomIds) {
                $q->whereIn('item_id', $roomIds);
            });

        if ($startDate) {
            $bookingsQuery->where('start_date', '>=', $startDate);
        }

        $bookings = $bookingsQuery->with('roomDetail')->get();

        $totalRevenue = 0;
        $roomStats = [];
        foreach ($rooms as $room) {
            $roomStats[$room->room_id] = [
                'room' => $room,
                'revenue' => 0,
                'booking_count' => 0,
                'arpb' => 0,
                'share' => 0
            ];
        }

        foreach ($bookings as $b) {
            $roomId = $b->roomDetail->item_id ?? null;
            if ($roomId && isset($roomStats[$roomId])) {
                $roomStats[$roomId]['revenue'] += $b->total;
                $roomStats[$roomId]['booking_count'] += 1;
                $totalRevenue += $b->total;
            }
        }

        $donutLabels = [];
        $donutValues = [];
        foreach ($roomStats as $id => &$stats) {
            $stats['share'] = $totalRevenue > 0 ? round(($stats['revenue'] / $totalRevenue) * 100, 1) : 0;
            $stats['arpb'] = $stats['booking_count'] > 0 ? round($stats['revenue'] / $stats['booking_count']) : 0;

            if ($stats['revenue'] > 0) {
                $donutLabels[] = $stats['room']->name;
                $donutValues[] = $stats['revenue'];
            }
        }

        $monthlyEarnings = \App\Models\Booking::whereIn('status', [1, 2])
            ->whereHas('roomDetail', function($q) use ($roomIds) {
                $q->whereIn('item_id', $roomIds);
            })
            ->select(
                DB::raw('MONTH(start_date) as month'),
                DB::raw('SUM(total) as total')
            )
            ->whereYear('start_date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $momGrowth = [];
        $growthLabels = [];
        $growthValues = [];

        $monthlyData = array_fill(1, 12, 0);
        foreach ($monthlyEarnings as $m => $tot) {
            $monthlyData[$m] = (int)$tot;
        }

        $prevTotal = null;
        for ($m = 1; $m <= 12; $m++) {
            $currentTotal = $monthlyData[$m];
            $growthLabels[] = $months[$m - 1];
            $growthValues[] = $currentTotal;

            if ($prevTotal !== null) {
                if ($prevTotal > 0) {
                    $change = (($currentTotal - $prevTotal) / $prevTotal) * 100;
                    $momGrowth[$months[$m - 1]] = round($change, 1);
                } else {
                    $momGrowth[$months[$m - 1]] = $currentTotal > 0 ? 100 : 0;
                }
            } else {
                $momGrowth[$months[$m - 1]] = 0;
            }
            $prevTotal = $currentTotal;
        }

        $currentMonthIndex = (int)date('n');
        $latestGrowth = $momGrowth[$months[$currentMonthIndex - 1]] ?? 0;

        $avgArpb = $bookings->count() > 0 ? round($totalRevenue / $bookings->count()) : 0;

        return view('penyedia.report_finance', compact(
            'roomStats', 'filter', 'totalRevenue', 'avgArpb', 'latestGrowth',
            'donutLabels', 'donutValues', 'growthLabels', 'growthValues', 'momGrowth'
        ));
    }

    public function updateInstallmentDueDate(Request $request, $id)
    {
        $booking = Booking::with('roomDetail.room')->findOrFail($id);

        // Protection: Only the provider who owns the room can access
        if ($booking->roomDetail->room->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'installment_due_date' => 'required|date|after_or_equal:today',
        ], [
            'installment_due_date.required' => 'Tanggal jatuh tempo harus diisi.',
            'installment_due_date.date' => 'Format tanggal tidak valid.',
            'installment_due_date.after_or_equal' => 'Tanggal jatuh tempo tidak boleh di masa lalu.',
        ]);

        $booking->installment_due_date = $request->installment_due_date;
        $booking->save();

        return back()->with('success', 'Tanggal jatuh tempo cicilan berhasil diperbarui!');
    }

    public function downloadBookingPDF($id)
    {
        $booking = Booking::with(['user', 'roomDetail.room', 'serviceDetails'])->findOrFail($id);
        
        // Protection: Only the provider who owns the room can access
        if ($booking->roomDetail->room->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penyedia.pdf_booking', compact('booking'))
                  ->setPaper('a4', 'portrait')
                  ->setOption([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true
                  ]);

        return $pdf->download('Laporan_Pemesanan_TRX-' . $id . '.pdf');
    }

    public function downloadListBookingPDF(Request $request)
    {
        $penyediaId = Auth::id();
        $query = Booking::with(['user', 'details.room'])
            ->whereHas('details.room', function ($q) use ($penyediaId) {
                $q->where('user_id', $penyediaId);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_id', 'like', "%$search%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('username', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penyedia.pdf_list_booking', compact('bookings'))
                  ->setPaper('a4', 'landscape')
                  ->setOption([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true
                  ]);

        return $pdf->download('Laporan_Daftar_Booking.pdf');
    }

    public function downloadOccupancyPDF(Request $request)
    {
        $penyediaId = Auth::id();
        $filter = $request->query('filter', '30');

        $rooms = \App\Models\Room::where('user_id', $penyediaId)->where('status', '>=', 0)->get();
        $roomIds = $rooms->pluck('room_id')->toArray();

        $startDate = null;
        $totalDays = 30;
        if ($filter === '30') {
            $startDate = now()->subDays(30);
            $totalDays = 30;
        } elseif ($filter === '90') {
            $startDate = now()->subDays(90);
            $totalDays = 90;
        } else {
            $earliestBooking = \App\Models\Booking::whereIn('status', [1, 2])
                ->whereHas('roomDetail', function($q) use ($roomIds) {
                    $q->whereIn('item_id', $roomIds);
                })
                ->orderBy('start_date', 'asc')
                ->first();
            if ($earliestBooking) {
                $earliestDate = \Carbon\Carbon::parse($earliestBooking->start_date);
                $totalDays = max(1, now()->diffInDays($earliestDate));
            } else {
                $totalDays = 30;
            }
        }

        $totalHoursPeriod = $totalDays * 24;

        $bookingsQuery = \App\Models\Booking::whereIn('status', [1, 2])
            ->whereHas('roomDetail', function($q) use ($roomIds) {
                $q->whereIn('item_id', $roomIds);
            });

        if ($startDate) {
            $bookingsQuery->where('start_date', '>=', $startDate);
        }

        $bookings = $bookingsQuery->with('roomDetail')->get();

        $roomStats = [];
        foreach ($rooms as $room) {
            $roomStats[$room->room_id] = [
                'room' => $room,
                'booking_count' => 0,
                'total_hours' => 0,
                'occupancy_rate' => 0,
            ];
        }

        foreach ($bookings as $b) {
            $roomId = $b->roomDetail->item_id ?? null;
            if ($roomId && isset($roomStats[$roomId])) {
                $start = \Carbon\Carbon::parse($b->start_date);
                $end = \Carbon\Carbon::parse($b->end_date);
                
                if ($start->format('H:i:s') === '00:00:00' && $end->format('H:i:s') === '23:59:59') {
                    $days = max(1, $start->diffInDays($end) + 1);
                    $hours = $days * 24;
                } else {
                    $hours = max(1, $end->diffInHours($start));
                }

                $roomStats[$roomId]['booking_count'] += 1;
                $roomStats[$roomId]['total_hours'] += $hours;
            }
        }

        $recommendations = [];
        foreach ($roomStats as $id => &$stats) {
            $rate = ($stats['total_hours'] / $totalHoursPeriod) * 100;
            $stats['occupancy_rate'] = round(min(100, $rate), 1);

            if ($stats['occupancy_rate'] < 15) {
                $recommendations[] = [
                    'room_name' => $stats['room']->name,
                    'type' => 'promo',
                    'text' => "Ruangan '{$stats['room']->name}' memiliki tingkat okupansi yang rendah ({$stats['occupancy_rate']}%). Pertimbangkan untuk membuat diskon weekday promo atau menurunkan harga sewa dasar.",
                    'class' => 'warning'
                ];
            } elseif ($stats['occupancy_rate'] > 60) {
                $recommendations[] = [
                    'room_name' => $stats['room']->name,
                    'type' => 'price',
                    'text' => "Ruangan '{$stats['room']->name}' sangat diminati dengan okupansi tinggi ({$stats['occupancy_rate']}%). Anda dapat mencoba menaikkan harga dasar sebesar 5-10% pada weekend atau peak-hour.",
                    'class' => 'success'
                ];
            }
        }

        $totalBookingsCount = $bookings->count();
        $totalHoursAllRooms = array_sum(array_column($roomStats, 'total_hours'));
        $avgOccupancyRate = count($roomStats) > 0 ? array_sum(array_column($roomStats, 'occupancy_rate')) / count($roomStats) : 0;
        $avgOccupancyRate = round($avgOccupancyRate, 1);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penyedia.pdf_occupancy', compact(
                    'roomStats', 'filter', 'totalBookingsCount', 'totalHoursAllRooms', 
                    'avgOccupancyRate', 'recommendations'
                  ))
                  ->setPaper('a4', 'portrait')
                  ->setOption([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true
                  ]);

        return $pdf->download('Laporan_Okupansi.pdf');
    }

    public function downloadFinancePDF(Request $request)
    {
        $penyediaId = Auth::id();
        $filter = $request->query('filter', '30');

        $rooms = \App\Models\Room::where('user_id', $penyediaId)->where('status', '>=', 0)->get();
        $roomIds = $rooms->pluck('room_id')->toArray();

        $startDate = null;
        if ($filter === '30') {
            $startDate = now()->subDays(30);
        } elseif ($filter === '90') {
            $startDate = now()->subDays(90);
        }

        $bookingsQuery = \App\Models\Booking::whereIn('status', [1, 2])
            ->whereHas('roomDetail', function($q) use ($roomIds) {
                $q->whereIn('item_id', $roomIds);
            });

        if ($startDate) {
            $bookingsQuery->where('start_date', '>=', $startDate);
        }

        $bookings = $bookingsQuery->with('roomDetail')->get();

        $totalRevenue = 0;
        $roomStats = [];
        foreach ($rooms as $room) {
            $roomStats[$room->room_id] = [
                'room' => $room,
                'revenue' => 0,
                'booking_count' => 0,
                'arpb' => 0,
                'share' => 0
            ];
        }

        foreach ($bookings as $b) {
            $roomId = $b->roomDetail->item_id ?? null;
            if ($roomId && isset($roomStats[$roomId])) {
                $roomStats[$roomId]['revenue'] += $b->total;
                $roomStats[$roomId]['booking_count'] += 1;
                $totalRevenue += $b->total;
            }
        }

        foreach ($roomStats as $id => &$stats) {
            $stats['share'] = $totalRevenue > 0 ? round(($stats['revenue'] / $totalRevenue) * 100, 1) : 0;
            $stats['arpb'] = $stats['booking_count'] > 0 ? round($stats['revenue'] / $stats['booking_count']) : 0;
        }

        $avgArpb = $bookings->count() > 0 ? round($totalRevenue / $bookings->count()) : 0;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penyedia.pdf_finance', compact(
                    'roomStats', 'filter', 'totalRevenue', 'avgArpb'
                  ))
                  ->setPaper('a4', 'portrait')
                  ->setOption([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true
                  ]);

        return $pdf->download('Laporan_Keuangan.pdf');
    }
}
