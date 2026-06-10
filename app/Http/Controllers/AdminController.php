<?php

namespace App\Http\Controllers;

use App\Models\Outsource;
use App\Models\OutsourceAssignment;
use App\Models\OutsourceReport;
use App\Models\People;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index() {
        // 1. Ambil data pengajuan BARU (yang status tugasnya masih 'waiting' alias belum punya surveyor)
        $incoming = Room::where('status', 1)
                    ->whereDoesntHave('outsourceAssignments', function($query) {
                        $query->whereIn('assignment_status', ['on_the_way', 'checking']);
                    })
                    ->get();

        // 2. Ambil data tugas AKTIF (yang sedang dikerjakan tim lapangan untuk dipantau progresnya)
        $monitoring = OutsourceAssignment::with(['room', 'company'])
                    ->whereIn('assignment_status', ['on_the_way', 'checking'])
                    ->get();

        // 3. Ambil data list pegawai surveyor kustom outsource untuk isi dropdown select
        $mitra = Outsource::where('status', 1)->get();

        // 4. Laporan Survei yang sudah diselesaikan Outsource dan menunggu Keputusan Admin
        $realPendingReports = OutsourceAssignment::with(['room', 'company', 'report'])
                    ->where('assignment_status', 'completed')
                    ->whereHas('room', function($q) {
                        $q->where('status', 1);
                    })
                    ->get();

        $realProcessedReports = OutsourceAssignment::with(['room', 'company', 'report'])
                    ->where('assignment_status', 'completed')
                    ->whereHas('room', function($q) {
                        $q->whereIn('status', [2, 3]);
                    })
                    ->get();

        // Transformasikan data dari database ke format objek
        $pendingReports = $realPendingReports->map(function($item) {
            return (object)[
                'id' => $item->assignment_id, // Menggunakan assignment_id agar detail route valid
                'room' => $item->room->name ?? 'N/A',
                'floor' => 'Lantai ' . ($item->room->floor ?? '1'),
                'price' => $item->room ? number_format($item->room->price, 0, ',', '.') : '0',
                'status' => 'Pending',
                'outsource' => $item->company->company_name ?? 'Outsource Partner',
                'rek' => $item->report->rekomendasi ?? 'Layak',
                'is_dummy' => false
            ];
        })->unique('room');

        $processedReports = $realProcessedReports->map(function($item) {
            $statusText = ($item->room->status ?? 0) == 2 ? 'Diterima' : 'Ditolak';
            return (object)[
                'id' => $item->assignment_id, // Menggunakan assignment_id agar detail route valid
                'room' => $item->room->name ?? 'N/A',
                'floor' => 'Lantai ' . ($item->room->floor ?? '1'),
                'price' => $item->room ? number_format($item->room->price, 0, ',', '.') : '0',
                'status' => $statusText,
                'outsource' => $item->company->company_name ?? 'Outsource Partner',
                'rek' => $item->report->rekomendasi ?? (($item->room->status ?? 0) == 2 ? 'Layak' : 'Tidak Layak'),
                'is_dummy' => false
            ];
        })->unique('room');

        // Menghitung data statistik box atas secara real-time
        $countWaiting = Room::where('status', 1)
                    ->whereDoesntHave('outsourceAssignments', function($query) {
                        $query->whereIn('assignment_status', ['on_the_way', 'checking']);
                    })->count();
        $countActive = OutsourceAssignment::whereIn('assignment_status', ['on_the_way', 'checking'])->count();
        $countSurveyor = Outsource::count();
        
        $countPendingReports = $pendingReports->count();
        $countTotalRooms = Room::where('status', '>=', 0)->count();

        return view('admin.dashboard', compact(
            'incoming', 
            'monitoring', 
            'mitra', 
            'pendingReports', 
            'processedReports',
            'countWaiting', 
            'countActive', 
            'countSurveyor',
            'countPendingReports',
            'countTotalRooms'
        ));
    }

    public function acc_room(){
        // 1. Ambil data asli dari database
        $realPendingReports = OutsourceAssignment::with(['room', 'company', 'report'])
                    ->where('assignment_status', 'completed')
                    ->whereHas('room', function($q) {
                        $q->where('status', 1);
                    })
                    ->get();

        $realProcessedReports = OutsourceAssignment::with(['room', 'company', 'report'])
                    ->where('assignment_status', 'completed')
                    ->whereHas('room', function($q) {
                        $q->whereIn('status', [2, 3]);
                    })
                    ->get();

        // 2. Transformasikan ke format objek untuk view
        $pendingRooms = $realPendingReports->map(function($item) {
            return (object)[
                'id' => $item->assignment_id, // Gunakan assignment_id agar detail route valid
                'room' => $item->room->name ?? 'N/A',
                'floor' => 'Lantai ' . ($item->room->floor ?? '1'),
                'price' => number_format($item->room->price ?? 0, 0, ',', '.'),
                'status' => 'Pending',
                'outsource' => $item->company->company_name ?? 'Outsource Partner',
                'rek' => $item->report->rekomendasi ?? 'Layak'
            ];
        });

        $processedRooms = $realProcessedReports->map(function($item) {
            $statusText = ($item->room->status ?? 0) == 2 ? 'Diterima' : 'Ditolak';
            return (object)[
                'id' => $item->assignment_id,
                'room' => $item->room->name ?? 'N/A',
                'floor' => 'Lantai ' . ($item->room->floor ?? '1'),
                'price' => number_format($item->room->price ?? 0, 0, ',', '.'),
                'status' => $statusText,
                'outsource' => $item->company->company_name ?? 'Outsource Partner',
                'rek' => $item->report->rekomendasi ?? 'Layak'
            ];
        });

        $pendingCount = $pendingRooms->count();
        $approvedCount = $processedRooms->where('status', 'Diterima')->count();
        $rejectedCount = $processedRooms->where('status', 'Ditolak')->count();
        $totalCount = $pendingCount + $approvedCount + $rejectedCount;

        $stats = [
            ['label' => 'Total Pengajuan', 'val' => $totalCount, 'color' => 'primary', 'icon' => 'bi-list-check'],
            ['label' => 'Menunggu di Setujui', 'val' => $pendingCount, 'color' => 'warning', 'icon' => 'bi-clock-history'],
            ['label' => 'Disetujui', 'val' => $approvedCount, 'color' => 'success', 'icon' => 'bi-check-all'],
            ['label' => 'Ditolak', 'val' => $rejectedCount, 'color' => 'danger', 'icon' => 'bi-x-circle'],
        ];

        return view('admin.acc_room', compact('pendingRooms', 'processedRooms', 'stats'));
    }

    public function assign_outsource(){
        return view('admin.assign_outsource');
    }
    
    public function outsource(Request $request)
    {
        $query = Outsource::with('account')->where('status', '>=', 0);

        // Fitur Pencarian Dinamis berdasarkan nama vendor atau layanan
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', '%' . $search . '%')
                ->orWhere('business_type', 'like', '%' . $search . '%');
            });
        }

        // Ambil data dengan pagination
        $partners = $query->paginate(10);

        // Menghitung data statistik box atas secara real-time
        $totalMitra    = Outsource::where('status', '>=', 0)->count();
        $mitraAktif    = Outsource::where('status', 1)->count();
        $mitraNonaktif = Outsource::where('status', 0)->count();

        return view('admin.outsource', compact('partners', 'totalMitra', 'mitraAktif', 'mitraNonaktif'));
    }

    // Menampilkan halaman form pendaftaran vendor
    public function create_outsource()
    {
        return view('admin.form_outsource');
    }

    // Memproses penyimpanan data pendaftaran vendor baru (Database Transaction Safe)
    public function store_outsource(Request $request)
    {
        $request->validate([
            'company_name'    => 'required|string|max:255',
            'nib'             => 'required|numeric|digits:13',
            'npwp'            => 'required|numeric',
            'business_type'   => 'required|string',
            'company_address' => 'required|string',
            'pic_name'        => 'required|string|max:255',
            'pic_position'    => 'required|string|max:255',
            'pic_phone'       => 'required|numeric',
            'pic_email'           => 'required|email',
            'bank_name'       => 'required|string',
            'bank_account'    => 'required|numeric',
        ]);

        Outsource::create([
            'company_name'    => $request->company_name,
            'nib'             => $request->nib,
            'npwp'            => $request->npwp,
            'business_type'   => $request->business_type,
            'company_address' => $request->company_address,
            'pic_name'        => $request->pic_name,
            'pic_position'    => $request->pic_position,
            'pic_email'       => $request->pic_email,
            'pic_phone'       => $request->pic_phone,
            'bank_name'       => $request->bank_name,
            'bank_account'    => $request->bank_account,
            'status'          => 1
        ]);

        return redirect()->route('admin.outsource')->with('success', 'Perusahaan Mitra Berhasil Didaftarkan!');
    }

    // Menampilkan halaman form edit dengan data lama yang sudah terisi
    public function edit_outsource($outsource_id)
    {
        // Cari data vendor berdasarkan ID, jika tidak ketemu langsung error 404
        $vendor = Outsource::findOrFail($outsource_id);

        return view('admin.form_outsource', compact('vendor'));
    }

    // Memproses perubahan data dari form edit
    public function update_outsource(Request $request, $outsource_id)
    {
        $request->validate([
            'company_name'    => 'required|string|max:255',
            'nib'             => 'required|numeric|digits:13',
            'npwp'            => 'required|numeric',
            'business_type'   => 'required|string',
            'company_address' => 'required|string',
            'pic_name'        => 'required|string|max:255',
            'pic_position'    => 'required|string|max:255',
            'pic_phone'       => 'required|numeric',
            'pic_email'       => 'required|email',
            'bank_name'       => 'required|string',
            'bank_account'    => 'required|numeric',
        ]);

        $vendor = Outsource::findOrFail($outsource_id);

        // Update data vendor di database
        $vendor->update([
            'company_name'    => $request->company_name,
            'nib'             => $request->nib,
            'npwp'            => $request->npwp,
            'business_type'   => $request->business_type,
            'company_address' => $request->company_address,
            'pic_name'        => $request->pic_name,
            'pic_position'    => $request->pic_position,
            'pic_email'       => $request->pic_email,
            'pic_phone'       => $request->pic_phone,
            'bank_name'       => $request->bank_name,
            'bank_account'    => $request->bank_account,
        ]);

        // Redirect kembali ke halaman utama list master outsource dengan alert sukses
        return redirect()->route('admin.outsource')->with('success', 'Data perusahaan mitra berhasil diperbarui!');
    }

    // Fungsi memutus kontrak / mengubah status keaktifan mitra
    public function terminate_outsource($id)
    {
        $partner = Outsource::findOrFail($id);
        // Toggle status keaktifan vendor
        $partner->update([
            'status' => $partner->status == 1 ? 0 : 1
        ]);

        return back()->with('success', 'Status keaktifan kemitraan vendor berhasil diperbarui.');
    }

    public function outsourceAssignment()
    {
        // 1. Ambil data pengajuan BARU (yang status tugasnya masih 'waiting' alias belum punya surveyor)
        $incoming = Room::where('status', 1)
                    ->whereDoesntHave('outsourceAssignments', function($query) {
                        $query->whereIn('assignment_status', ['on_the_way', 'checking']);
                    })
                    ->get();

        // 2. Ambil data tugas AKTIF (yang sedang dikerjakan tim lapangan untuk dipantau progresnya)
        $monitoring = OutsourceAssignment::with(['room', 'company'])
                    ->whereIn('assignment_status', ['on_the_way', 'checking'])
                    ->get();

        // 3. Ambil data list partner outsource untuk isi dropdown select
        $mitra = Outsource::where('status', 1)->get(); 

        // 4. Hitung data statistik box atas secara dinamis dari database
        $countWaiting = Room::where('status', 1)
                    ->whereDoesntHave('outsourceAssignments', function($query) {
                        $query->whereIn('assignment_status', ['on_the_way', 'checking']);
                    })->count();
        $countActive = OutsourceAssignment::whereIn('assignment_status', ['on_the_way', 'checking'])->count();
        $countSurveyor = Outsource::count();

        return view('admin.assign_outsource', compact('incoming', 'monitoring', 'mitra', 'countWaiting', 'countActive', 'countSurveyor'));
    }

    // Fungsi eksekusi tombol "Tugaskan" saat admin memilih mitra outsource
    public function assignSurveyor(Request $request, $room_id)
    {
        $request->validate([
            'outsource_id' => 'required'
        ], [
            'outsource_id.required' => 'Wajib memilih salah satu mitra outsource.'
        ]);
        // =========================================================================
        // LOGIKA AMAN ERP: Mencegah Duplikasi Penugasan Aktif untuk Ruangan yang Sama
        // =========================================================================
        $isAssigned = OutsourceAssignment::where('room_id', $room_id)
                        ->whereIn('assignment_status', ['on_the_way', 'checking'])
                        ->exists();

        if ($isAssigned) {
            return back()->with('error', 'Gagal! Ruangan ini sudah masuk ke dalam daftar tugas aktif outsource.');
        }

        // =========================================================================
        // TINDAKAN NYATA: Jalankan Perintah CREATE Data Penugasan Baru
        // =========================================================================
        OutsourceAssignment::create([
            'room_id'           => $room_id,
            'outsource_id'      => $request->outsource_id,
            'assignment_status' => 'on_the_way', // Langsung aktif menuju lokasi
            'progress'          => 15            // Set awal progres ke 15% sesuai visual template
        ]);

        // (Opsional) Jika diperlukan, kamu bisa mengubah status ketersediaan awal 
        // di tabel rooms agar tidak muncul lagi di antrean penugasan baru:
        // Room::where('room_id', $room_id)->update(['status' => 2]); 

        return back()->with('success', 'Tugas baru berhasil dibuat dan mitra outsource telah ditugaskan!');
    }

    // Fungsi menghapus total data penugasan dari database
    public function cancelAssignment($assignment_id)
    {
        // 1. Cari data penugasan berdasarkan ID di tabel outsource_assignments
        $assignment = OutsourceAssignment::findOrFail($assignment_id);
        
        // 2. TINDAKAN NYATA: Hapus baris data ini secara permanen dari database
        $assignment->delete();

        // 3. Kembalikan ke halaman dengan pesan sukses
        return back()->with('success', 'Penugasan berhasil dihapus total dan ruangan dikembalikan ke antrean baru.');
    }

    public function approveRoom($room_id)
    {
        $room = Room::find($room_id);
        
        if ($room) {
            $room->update(['status' => 2]); // 2 = Diterima (Approved)

            // Update the outsource assignment status to completed if any
            OutsourceAssignment::where('room_id', $room_id)
                ->whereIn('assignment_status', ['on_the_way', 'checking'])
                ->update(['assignment_status' => 'completed', 'progress' => 100]);

            return redirect()->route('admin.dashboard')->with('success', 'Ruangan ' . $room->name . ' berhasil disetujui untuk disewa!');
        }

        return redirect()->route('admin.dashboard')->with('error', 'Ruangan tidak ditemukan.');
    }

    public function rejectRoom($room_id)
    {
        $room = Room::find($room_id);
        
        if ($room) {
            $room->update(['status' => 3]); // 3 = Not Available / Ditolak (Rejected)

            // Update the outsource assignment status to completed/canceled
            OutsourceAssignment::where('room_id', $room_id)
                ->whereIn('assignment_status', ['on_the_way', 'checking'])
                ->update(['assignment_status' => 'completed', 'progress' => 100]);

            return redirect()->route('admin.dashboard')->with('success', 'Pengajuan ruangan ' . $room->name . ' berhasil ditolak.');
        }

        return redirect()->route('admin.dashboard')->with('error', 'Ruangan tidak ditemukan.');
    }

    public function getChartData(Request $request)
    {
        $period = $request->input('period', 'month');
        $labels = [];
        $newProposals = [];
        $completedSurveys = [];

        if ($period === 'day') {
            $dateVal = $request->input('date', date('Y-m-d'));
            $dateStart = Carbon::parse($dateVal)->startOfDay();
            $dateEnd = $dateStart->copy()->endOfDay();

            $rooms = Room::where('status', '>=', 1)->whereBetween('created_at', [$dateStart, $dateEnd])->get();
            $reports = OutsourceReport::whereBetween('created_at', [$dateStart, $dateEnd])->get();

            $labels = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'];
            $newProposals = [0, 0, 0, 0, 0, 0, 0];
            $completedSurveys = [0, 0, 0, 0, 0, 0, 0];

            foreach ($rooms as $r) {
                $hour = $r->created_at->hour;
                if ($hour < 10) $newProposals[0]++;
                elseif ($hour < 12) $newProposals[1]++;
                elseif ($hour < 14) $newProposals[2]++;
                elseif ($hour < 16) $newProposals[3]++;
                elseif ($hour < 18) $newProposals[4]++;
                elseif ($hour < 20) $newProposals[5]++;
                else $newProposals[6]++;
            }

            foreach ($reports as $rep) {
                $hour = $rep->created_at->hour;
                if ($hour < 10) $completedSurveys[0]++;
                elseif ($hour < 12) $completedSurveys[1]++;
                elseif ($hour < 14) $completedSurveys[2]++;
                elseif ($hour < 16) $completedSurveys[3]++;
                elseif ($hour < 18) $completedSurveys[4]++;
                elseif ($hour < 20) $completedSurveys[5]++;
                else $completedSurveys[6]++;
            }

        } elseif ($period === 'week') {
            $weekVal = $request->input('week', date('Y-\WW'));
            if (preg_match('/^(\d{4})-W(\d{2})$/', $weekVal, $matches)) {
                $year = (int)$matches[1];
                $weekNum = (int)$matches[2];
                $dateStart = Carbon::now()->setISODate($year, $weekNum)->startOfWeek();
            } else {
                $dateStart = Carbon::now()->startOfWeek();
            }
            $dateEnd = $dateStart->copy()->endOfWeek();

            $rooms = Room::where('status', '>=', 1)->whereBetween('created_at', [$dateStart, $dateEnd])->get();
            $reports = OutsourceReport::whereBetween('created_at', [$dateStart, $dateEnd])->get();

            $labels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $newProposals = [0, 0, 0, 0, 0, 0, 0];
            $completedSurveys = [0, 0, 0, 0, 0, 0, 0];

            foreach ($rooms as $r) {
                $dayIdx = $r->created_at->dayOfWeekIso - 1;
                if ($dayIdx >= 0 && $dayIdx < 7) {
                    $newProposals[$dayIdx]++;
                }
            }

            foreach ($reports as $rep) {
                $dayIdx = $rep->created_at->dayOfWeekIso - 1;
                if ($dayIdx >= 0 && $dayIdx < 7) {
                    $completedSurveys[$dayIdx]++;
                }
            }

        } elseif ($period === 'month') {
            $monthVal = (int)$request->input('month', date('m'));
            $yearVal = (int)$request->input('year', date('Y'));
            $dateStart = Carbon::createFromDate($yearVal, $monthVal, 1)->startOfMonth();
            $dateEnd = $dateStart->copy()->endOfMonth();

            $rooms = Room::where('status', '>=', 1)->whereBetween('created_at', [$dateStart, $dateEnd])->get();
            $reports = OutsourceReport::whereBetween('created_at', [$dateStart, $dateEnd])->get();

            $labels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
            $newProposals = [0, 0, 0, 0];
            $completedSurveys = [0, 0, 0, 0];

            foreach ($rooms as $r) {
                $day = $r->created_at->day;
                if ($day <= 7) $newProposals[0]++;
                elseif ($day <= 14) $newProposals[1]++;
                elseif ($day <= 21) $newProposals[2]++;
                else $newProposals[3]++;
            }

            foreach ($reports as $rep) {
                $day = $rep->created_at->day;
                if ($day <= 7) $completedSurveys[0]++;
                elseif ($day <= 14) $completedSurveys[1]++;
                elseif ($day <= 21) $completedSurveys[2]++;
                else $completedSurveys[3]++;
            }

        } elseif ($period === 'year') {
            $yearVal = (int)$request->input('year', date('Y'));
            $dateStart = Carbon::createFromDate($yearVal, 1, 1)->startOfYear();
            $dateEnd = $dateStart->copy()->endOfYear();

            $rooms = Room::where('status', '>=', 1)->whereBetween('created_at', [$dateStart, $dateEnd])->get();
            $reports = OutsourceReport::whereBetween('created_at', [$dateStart, $dateEnd])->get();

            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            $newProposals = array_fill(0, 12, 0);
            $completedSurveys = array_fill(0, 12, 0);

            foreach ($rooms as $r) {
                $monthIdx = $r->created_at->month - 1;
                if ($monthIdx >= 0 && $monthIdx < 12) {
                    $newProposals[$monthIdx]++;
                }
            }

            foreach ($reports as $rep) {
                $monthIdx = $rep->created_at->month - 1;
                if ($monthIdx >= 0 && $monthIdx < 12) {
                    $completedSurveys[$monthIdx]++;
                }
            }
        }

        return response()->json([
            'labels' => $labels,
            'newProposals' => $newProposals,
            'completedSurveys' => $completedSurveys
        ]);
    }

    public function fines()
    {
        $fines = \App\Models\Fine::with(['booking.user', 'booking.roomDetail.room'])->orderBy('created_at', 'desc')->get();
        return view('admin.fines', compact('fines'));
    }

    public function approveFine($id)
    {
        $fine = \App\Models\Fine::findOrFail($id);
        $fine->update(['status' => 1]); // Approved
        return back()->with('success', 'Denda #' . $fine->fine_id . ' berhasil disetujui. Warning akan ditampilkan kepada penyewa saat login.');
    }

    public function rejectFine($id)
    {
        $fine = \App\Models\Fine::findOrFail($id);
        $fine->update(['status' => 2]); // Rejected
        return back()->with('success', 'Denda #' . $fine->fine_id . ' telah ditolak.');
    }
}
