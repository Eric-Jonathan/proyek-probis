<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OutsourceController extends Controller
{
    public function index() {
        // Ambil ID outsource yang sedang login
        $outsourceId = \Illuminate\Support\Facades\Auth::user()->outsource_id;
        $userId = \Illuminate\Support\Facades\Auth::user()->user_id;

        // 1. Ambil data penugasan aktif (on_the_way atau checking)
        $activeAssignments = \App\Models\OutsourceAssignment::with('room')
            ->where('outsource_id', $outsourceId)
            ->whereIn('assignment_status', ['on_the_way', 'checking'])
            ->where(function($query) use ($userId) {
                $query->whereNull('surveyor_id')
                      ->orWhere('surveyor_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Ambil data penugasan yang sudah diselesaikan (completed) dan memiliki laporan oleh user ini
        $completedAssignments = \App\Models\OutsourceAssignment::with(['room', 'report'])
            ->where('surveyor_id', $userId)
            ->where('assignment_status', 'completed')
            ->whereHas('report')
            ->get();

        // Hitung total honor diterima (Rp 200.000 flat per project selesai)
        $completedCount = $completedAssignments->count();
        $totalHonor = $completedCount * 200000;

        // Hitung akurasi laporan (jumlah disetujui dibanding jumlah yang sudah diputuskan)
        $processedCount = $completedAssignments->filter(function($item) {
            return in_array($item->room->status ?? 0, [2, 3]);
        })->count();
        
        $approvedCount = $completedAssignments->filter(function($item) {
            return ($item->room->status ?? 0) == 2;
        })->count();

        $accuracy = $processedCount > 0 ? round(($approvedCount / $processedCount) * 100) : 100;

        // 3. Ambil 5 riwayat tugas selesai terbaru untuk sidebar
        $recentHistory = $completedAssignments->sortByDesc('updated_at')->take(5);

        // Map data penugasan aktif ke format objek untuk tabel dashboard
        $activeJobs = $activeAssignments->map(function($item) {
            $rawLoc = $item->room->location ?? '';
            $city = \Illuminate\Support\Str::limit(implode(', ', array_slice(explode(',', $rawLoc), 0, 1)), 25);
            if (empty($city)) {
                $city = 'Malang'; // Default fallback
            }

            // Dapatkan alamat detail
            $addressParts = explode(',', $rawLoc);
            $detailAddress = count($addressParts) > 1 ? trim(implode(', ', array_slice($addressParts, 1))) : $rawLoc;

            return (object)[
                'assignment_id' => $item->assignment_id,
                'room' => $item->room->name ?? 'N/A',
                'city' => $city,
                'address' => $detailAddress,
                'fee' => 200000,
            ];
        });

        return view('outsource.dashboard', compact(
            'activeJobs',
            'completedCount',
            'totalHonor',
            'accuracy',
            'recentHistory'
        ));
    }

    public function history(Request $request){
        // Ambil ID outsource yang sedang login
        $outsourceId = \Illuminate\Support\Facades\Auth::user()->outsource_id;
        $userId = \Illuminate\Support\Facades\Auth::user()->user_id;

        // Query dasar untuk tugas yang sudah diselesaikan (completed) dan memiliki laporan oleh user ini
        $baseQuery = \App\Models\OutsourceAssignment::with(['room', 'report'])
            ->where('surveyor_id', $userId)
            ->where('assignment_status', 'completed')
            ->whereHas('report');

        // Hitung statistik keseluruhan (sebelum difilter pencarian/status)
        $totalTerkirim = (clone $baseQuery)->count();

        $disetujui = (clone $baseQuery)->whereHas('room', function($q) {
            $q->where('status', 2); // Approved
        })->count();

        $ditolak = (clone $baseQuery)->whereHas('room', function($q) {
            $q->where('status', 3); // Rejected
        })->count();

        $pending = (clone $baseQuery)->whereHas('room', function($q) {
            $q->whereNotIn('status', [2, 3]); // Pending/Checking
        })->count();

        $stats = [
            ['label' => 'Total Terkirim', 'val' => $totalTerkirim, 'color' => 'primary', 'icon' => 'bi-send-check'],
            ['label' => 'Disetujui', 'val' => $disetujui, 'color' => 'success', 'icon' => 'bi-patch-check'],
            ['label' => 'Perlu Revisi', 'val' => $ditolak, 'color' => 'danger', 'icon' => 'bi-exclamation-octagon'],
            ['label' => 'Menunggu', 'val' => $pending, 'color' => 'warning', 'icon' => 'bi-hourglass-split'],
        ];

        // Terapkan filter pencarian
        $query = (clone $baseQuery);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('room', function($rq) use ($search) {
                    $rq->where('name', 'like', '%' . $search . '%');
                });
                
                // Cari juga berdasarkan ID Tugas jika input adalah angka
                $cleanId = preg_replace('/[^0-9]/', '', $search);
                if (!empty($cleanId)) {
                    $q->orWhere('assignment_id', $cleanId);
                }
            });
        }

        // Terapkan filter status
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $status = $request->status;
            if ($status === 'Diterima') {
                $query->whereHas('room', function($q) {
                    $q->where('status', 2);
                });
            } elseif ($status === 'Ditolak') {
                $query->whereHas('room', function($q) {
                    $q->where('status', 3);
                });
            } elseif ($status === 'Pending') {
                $query->whereHas('room', function($q) {
                    $q->whereNotIn('status', [2, 3]);
                });
            }
        }

        // Ambil data terfilter diurutkan berdasarkan tanggal selesai terbaru
        $assignments = $query->orderBy('updated_at', 'desc')->get();

        // Konversi data ke format objek yang digunakan view
        $allJobs = $assignments->map(function($item) {
            return (object)[
                'id' => $item->assignment_id,
                'room' => $item->room->name ?? 'N/A',
                'tgl_kirim' => $item->report->created_at ? $item->report->created_at->format('Y-m-d') : date('Y-m-d'),
                'fee' => 200000, // Honor flat Rp 200.000
                'status' => $item->room->status == 2 ? 'Diterima' : ($item->room->status == 3 ? 'Ditolak' : 'Pending')
            ];
        });

        return view('outsource.history', compact('allJobs', 'stats'));
    }

    public function form($assignment_id) {
        $assignment = \App\Models\OutsourceAssignment::with('room')->findOrFail($assignment_id);
        
        // Pengecekan otorisasi: jika sudah diambil oleh orang lain
        if ($assignment->surveyor_id !== null && $assignment->surveyor_id !== \Illuminate\Support\Facades\Auth::user()->user_id) {
            return redirect()->route('outsource.job')->with('error', 'Akses ditolak! Tugas ini sudah dikerjakan oleh surveyor lain.');
        }

        return view('outsource.form', compact('assignment'));
    }

    public function jobList() {
        // Ambil ID outsource yang sedang login
        $outsourceId = \Illuminate\Support\Facades\Auth::user()->outsource_id;
        $userId = \Illuminate\Support\Facades\Auth::user()->user_id;

        // Ambil data penugasan aktif (on_the_way atau checking)
        $assignments = \App\Models\OutsourceAssignment::with('room')
            ->where('outsource_id', $outsourceId)
            ->whereIn('assignment_status', ['on_the_way', 'checking'])
            ->where(function($query) use ($userId) {
                $query->whereNull('surveyor_id')
                      ->orWhere('surveyor_id', $userId);
            })
            ->get();

        // Konversi ke format objek yang digunakan oleh list_job.blade.php
        $allJobs = $assignments->map(function($item) {
            $rawLoc = $item->room->location ?? '';
            $city = \Illuminate\Support\Str::limit(implode(', ', array_slice(explode(',', $rawLoc), 0, 1)), 25);
            if (empty($city)) {
                $city = 'Malang'; // Default fallback
            }
            
            return (object)[
                'assignment_id' => $item->assignment_id,
                'id' => $item->assignment_id,
                'room' => $item->room->name ?? 'N/A',
                'city' => $city,
                'fee' => 200000, // Honor flat
                'deadline' => $item->created_at ? $item->created_at->addDays(3)->format('Y-m-d') : date('Y-m-d', strtotime('+3 days')),
                'is_taken' => $item->assignment_status == 'checking'
            ];
        });

        // Hitung data statistik box atas secara dinamis
        $tugasTersedia = $assignments->where('assignment_status', 'on_the_way')->count();
        $sedangBerjalan = $assignments->where('assignment_status', 'checking')->count();
        
        $stats = [
            ['label' => 'Tugas Tersedia', 'val' => $tugasTersedia, 'color' => 'primary', 'icon' => 'bi-briefcase'],
            ['label' => 'Sedang Berjalan', 'val' => $sedangBerjalan, 'color' => 'warning', 'icon' => 'bi-clock-history'],
            ['label' => 'Total Honor', 'val' => 'Rp ' . number_format($allJobs->sum('fee'), 0, ',', '.'), 'color' => 'success', 'icon' => 'bi-wallet2'],
            ['label' => 'Perlu Tindakan', 'val' => $tugasTersedia, 'color' => 'danger', 'icon' => 'bi-bell'],
        ];

        return view('outsource.list_job', compact('allJobs', 'stats'));
    }

    public function takeJob($assignment_id)
    {
        $assignment = \App\Models\OutsourceAssignment::findOrFail($assignment_id);
        
        // Pengecekan konkurensi: jika tugas sudah diambil oleh orang lain
        if ($assignment->surveyor_id !== null) {
            return redirect()->route('outsource.job')->with('error', 'Gagal! Tugas ini sudah diambil oleh surveyor lain.');
        }

        // Update status ke checking (dalam pemeriksaan), progress ke 50%, dan isi surveyor_id
        $assignment->update([
            'assignment_status' => 'checking',
            'surveyor_id' => \Illuminate\Support\Facades\Auth::user()->user_id,
            'progress' => 50
        ]);

        return redirect()->route('outsource.job')->with('success', 'Tugas berhasil diambil! Silakan mulai pengisian laporan.');
    }

    public function submitReport(Request $request, $assignment_id)
    {
        $request->validate([
            'kondisi' => 'required',
            'kebersihan' => 'required',
            'catatan' => 'nullable|string',
            'rekomendasi' => 'required|in:layak,tidak',
            'facilities' => 'nullable|array',
            'fotos.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'video' => 'nullable|file|mimes:mp4,webm,ogg,mov|max:10240'
        ]);

        $assignment = \App\Models\OutsourceAssignment::with('room')->findOrFail($assignment_id);

        // Pengecekan otorisasi: jika sudah diambil oleh orang lain
        if ($assignment->surveyor_id !== null && $assignment->surveyor_id !== \Illuminate\Support\Facades\Auth::user()->user_id) {
            return redirect()->route('outsource.job')->with('error', 'Akses ditolak! Tugas ini sudah dikerjakan oleh surveyor lain.');
        }

        // 1. Proses upload banyak foto
        $photosList = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $index => $file) {
                $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload_report/images'), $fileName);
                $photosList[] = 'upload_report/images/' . $fileName;
            }
        }

        // 2. Proses upload video (jika ada)
        $videoPath = null;
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('upload_report/videos'), $fileName);
            $videoPath = 'upload_report/videos/' . $fileName;
        }

        // 3. Simpan laporan kelayakan ke database
        $report = \App\Models\OutsourceReport::create([
            'assignment_id' => $assignment_id,
            'kondisi' => $request->kondisi,
            'kebersihan' => $request->kebersihan,
            'catatan' => $request->catatan,
            'rekomendasi' => $request->rekomendasi == 'layak' ? 'Layak' : 'Tidak Layak',
            'photos' => $photosList,
            'video' => $videoPath,
            'facilities' => $request->input('facilities', [])
        ]);

        // 4. Update status penugasan ke completed dan progress ke 100%
        $assignment->update([
            'assignment_status' => 'completed',
            'progress' => 100
        ]);

        // 5. Sinkronisasi fasilitas hasil verifikasi lapangan ke tabel fasilitas ruangan asli
        if ($request->has('facilities') && $assignment->room) {
            $assignment->room->facilities()->delete();
            foreach ($request->facilities as $facilityName) {
                $assignment->room->facilities()->create([
                    'name' => $facilityName,
                    'status' => 1
                ]);
            }
        }

        return redirect()->route('outsource.job')->with('success', 'Laporan kelayakan berhasil dikirimkan ke Admin untuk diverifikasi!');
    }

    public function historyDetail($id)
    {
        // 1. Ambil data asli dari database beserta relasi detail laporan dan ruangan
        $assignment = \App\Models\OutsourceAssignment::with(['room.images', 'room.facilities', 'company', 'report'])->find($id);

        if ($assignment) {
            // Pengecekan otorisasi untuk role outsource
            if (\Illuminate\Support\Facades\Auth::user()->role === 'outsource' && 
                $assignment->surveyor_id !== \Illuminate\Support\Facades\Auth::user()->user_id) {
                abort(403, 'Anda tidak memiliki akses ke laporan ini.');
            }
        }

        if ($assignment && $assignment->report) {
            $report = $assignment->report;
            
            // Konversi media lampiran foto ke format array detail_history (hanya gambar)
            $mediaList = [];
            if (is_array($report->photos)) {
                foreach ($report->photos as $path) {
                    $mediaList[] = ['type' => 'image', 'url' => asset($path)];
                }
            }

            // Siapkan media pengaju dari foto ruangan terdaftar
            $pengajuMedia = [];
            if ($assignment->room && $assignment->room->images) {
                foreach ($assignment->room->images as $img) {
                    $pengajuMedia[] = ['type' => 'image', 'url' => asset($img->path)];
                }
            }

            // Daftar fasilitas untuk komparasi
            $masterFacilities = [
                'AC',
                'Free Wi-Fi',
                'Sound System',
                'Wireless Mic',
                'Proyektor',
                'Snack',
                'Galon',
                'Area Parkir',
                'Musholla',
                'Stage',
                'Keamanan CCTV'
            ];
            $roomFacilities = $assignment->room ? $assignment->room->facilities->pluck('name')->toArray() : [];
            $surveyorFacilities = is_array($report->facilities) ? $report->facilities : [];
            $allFacilitiesList = array_values(array_unique(array_merge($masterFacilities, $roomFacilities, $surveyorFacilities)));

            // Map data DB ke struktur visual detail_history
            $job = (object)[
                'id' => $assignment->assignment_id,
                'room_id' => $assignment->room_id,
                'room' => $assignment->room->name ?? 'N/A',
                'city' => \Illuminate\Support\Str::limit(implode(', ', array_slice(explode(',', $assignment->room->location ?? ''), 0, 1)), 25),
                'address' => $assignment->room->location ?? 'N/A',
                'fee' => 200000,
                'tgl_kirim' => $report->created_at ? $report->created_at->format('d M Y') : date('d M Y'),
                'status' => $assignment->room->status == 2 ? 'Diterima' : ($assignment->room->status == 3 ? 'Ditolak' : 'Pending'),
                'surveyor' => (object)[
                    'kondisi' => $report->kondisi . ' (Berdasarkan Cek Lapangan)',
                    'kebersihan' => $report->kebersihan,
                    'catatan' => $report->catatan,
                    'facilities' => $surveyorFacilities,
                    'video' => $report->video ? asset($report->video) : null,
                    'media' => $mediaList
                ],
                'pengaju' => (object)[
                    'kondisi' => 'Sangat Baik',
                    'kebersihan' => 'Sangat Bersih',
                    'catatan' => $assignment->room->description ?? 'Deskripsi pengaju unit.',
                    'facilities' => $roomFacilities,
                    'media' => $pengajuMedia
                ]
            ];

            return view('outsource.detail_history', compact('job', 'id', 'allFacilitiesList'));
        }

        abort(404, 'Detail laporan tidak ditemukan.');
    }

    public function downloadPDF($id)
    {
        // 1. Ambil data asli dari database beserta relasi detail laporan dan ruangan
        $assignment = \App\Models\OutsourceAssignment::with(['room.images', 'room.facilities', 'company', 'report'])->findOrFail($id);
        
        // Pengecekan otorisasi untuk role outsource
        if (\Illuminate\Support\Facades\Auth::user()->role === 'outsource' && 
            $assignment->surveyor_id !== \Illuminate\Support\Facades\Auth::user()->user_id) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        $report = $assignment->report;

        if (!$report) {
            return back()->with('error', 'Laporan belum diisi untuk penugasan ini.');
        }

        // Konversi media lampiran foto ke format array (hanya gambar, dengan local path untuk Dompdf)
        $mediaList = [];
        if (is_array($report->photos)) {
            foreach ($report->photos as $path) {
                // Gunakan path lokal agar Dompdf bisa meload gambar offline
                $mediaList[] = [
                    'type' => 'image',
                    'url' => asset($path),
                    'url_local' => public_path($path)
                ];
            }
        }

        // Siapkan media pengaju dari foto ruangan terdaftar
        $pengajuMedia = [];
        if ($assignment->room && $assignment->room->images) {
            foreach ($assignment->room->images as $img) {
                $pengajuMedia[] = [
                    'type' => 'image',
                    'url' => asset($img->path),
                    'url_local' => public_path($img->path)
                ];
            }
        }

        // Daftar fasilitas untuk komparasi
        $masterFacilities = [
            'AC',
            'Free Wi-Fi',
            'Sound System',
            'Wireless Mic',
            'Proyektor',
            'Snack',
            'Galon',
            'Area Parkir',
            'Musholla',
            'Stage',
            'Keamanan CCTV'
        ];
        $roomFacilities = $assignment->room ? $assignment->room->facilities->pluck('name')->toArray() : [];
        $surveyorFacilities = is_array($report->facilities) ? $report->facilities : [];
        $allFacilitiesList = array_values(array_unique(array_merge($masterFacilities, $roomFacilities, $surveyorFacilities)));

        // Map data DB ke struktur visual detail_history
        $job = (object)[
            'id' => $assignment->assignment_id,
            'room' => $assignment->room->name ?? 'N/A',
            'address' => $assignment->room->location ?? 'N/A',
            'tgl_kirim' => $report->created_at ? $report->created_at->format('d M Y') : date('d M Y'),
            'status' => $assignment->room->status == 2 ? 'Diterima' : ($assignment->room->status == 3 ? 'Ditolak' : 'Pending'),
            'surveyor_name' => $assignment->company->company_name ?? 'Outsource Partner',
            'surveyor' => (object)[
                'kondisi' => $report->kondisi,
                'kebersihan' => $report->kebersihan,
                'catatan' => $report->catatan,
                'rekomendasi' => $report->rekomendasi,
                'facilities' => $surveyorFacilities,
                'media' => $mediaList
            ],
            'pengaju' => (object)[
                'kondisi' => 'Sangat Baik',
                'kebersihan' => 'Sangat Bersih',
                'catatan' => $assignment->room->description ?? 'Deskripsi pengaju unit.',
                'facilities' => $roomFacilities,
                'media' => $pengajuMedia
            ]
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('outsource.pdf_report', compact('job', 'allFacilitiesList'))
                  ->setPaper('a4', 'portrait')
                  ->setOption([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true
                  ]);

        return $pdf->stream('Laporan_Survei_SRV-' . $id . '.pdf');
    }
}
