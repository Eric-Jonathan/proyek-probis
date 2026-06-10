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
        
        $totalEarnings = \App\Models\Booking::whereIn('status', [1, 2])
            ->whereHas('details.room', function ($q) use ($penyediaId) {
                $q->where('user_id', $penyediaId);
            })->sum('total');

        // 4. Data untuk grafik pendapatan bulanan tahun ini (Jan-Des)
        $monthlyEarnings = \App\Models\Booking::whereIn('status', [1, 2])
            ->whereHas('details.room', function($q) use ($roomIds) {
                $q->where('item_type', 1)->whereIn('item_id', $roomIds);
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

        $chartData = array_fill(1, 12, 0);
        foreach ($monthlyEarnings as $month => $total) {
            $chartData[$month] = (int)$total;
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
                ->whereHas('details.room', function($q) use ($roomIds) {
                    $q->where('item_type', 1)->whereIn('item_id', $roomIds);
                })
                ->whereYear('start_date', '>=', $currentYear - 4)
                ->get();

            foreach ($bookings as $b) {
                $bYear = (int)date('Y', strtotime($b->start_date));
                if (isset($yearsData[$bYear])) {
                    $yearsData[$bYear] += $b->total;
                }
            }
            $values = array_values($yearsData);

        } elseif ($filter === 'week') {
            $bookings = \App\Models\Booking::whereIn('status', [1, 2])
                ->whereHas('details.room', function($q) use ($roomIds) {
                    $q->where('item_type', 1)->whereIn('item_id', $roomIds);
                })
                ->whereYear('start_date', date('Y'))
                ->whereMonth('start_date', date('m'))
                ->get();

            $weeks = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
            foreach ($bookings as $b) {
                $day = (int)date('d', strtotime($b->start_date));
                if ($day <= 7) $weeks[1] += $b->total;
                elseif ($day <= 14) $weeks[2] += $b->total;
                elseif ($day <= 21) $weeks[3] += $b->total;
                elseif ($day <= 28) $weeks[4] += $b->total;
                else $weeks[5] += $b->total;
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
                ->whereHas('details.room', function($q) use ($roomIds) {
                    $q->where('item_type', 1)->whereIn('item_id', $roomIds);
                })
                ->where('start_date', '>=', date('Y-m-d 00:00:00', strtotime("-6 days")))
                ->get();

            foreach ($bookings as $b) {
                $bDate = date('Y-m-d', strtotime($b->start_date));
                if (isset($daysData[$bDate])) {
                    $daysData[$bDate] += $b->total;
                }
            }
            $values = array_values($daysData);

        } else {
            $monthlyEarnings = \App\Models\Booking::whereIn('status', [1, 2])
                ->whereHas('details.room', function($q) use ($roomIds) {
                    $q->where('item_type', 1)->whereIn('item_id', $roomIds);
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

            $chartData = array_fill(1, 12, 0);
            foreach ($monthlyEarnings as $month => $total) {
                $chartData[$month] = (int)$total;
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
        $unpaidOrder  = (clone $statsQuery)->where('status', 3)->count();
        $cancelOrder  = (clone $statsQuery)->where('status', 0)->count();

        $bookings = $query->latest()->paginate(10)->withQueryString();

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
}
