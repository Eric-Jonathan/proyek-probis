<?php

namespace App\Http\Controllers;

use App\Models\Outsource;
use App\Models\OutsourceAssignment;
use App\Models\People;
use App\Models\Room;
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
        $monitoring = OutsourceAssignment::with(['room', 'surveyor'])
                    ->whereIn('assignment_status', ['on_the_way', 'checking'])
                    ->get();

        // 3. Ambil data list pegawai surveyor kustom outsource untuk isi dropdown select
        $mitra = People::where('role', 'outsource')->get();

        // 4. Laporan Survei yang sudah diselesaikan Outsource dan menunggu Keputusan Admin
        $realPendingReports = OutsourceAssignment::with(['room', 'surveyor'])
                    ->where('assignment_status', 'completed')
                    ->whereHas('room', function($q) {
                        $q->where('status', 1);
                    })
                    ->get();

        $realProcessedReports = OutsourceAssignment::with(['room', 'surveyor'])
                    ->where('assignment_status', 'completed')
                    ->whereHas('room', function($q) {
                        $q->whereIn('status', [2, 3]);
                    })
                    ->get();

        // Data dummy sinkron dengan acc_room.blade.php
        $dummyReports = collect([
            (object)[
                'id' => 101, 
                'room' => 'Cozy Meeting Room', 
                'floor' => 'Lantai 1', 
                'price' => '500.000', 
                'status' => 'Diterima', 
                'outsource' => 'Budi Santoso', 
                'rek' => 'Layak',
                'is_dummy' => true
            ],
            (object)[
                'id' => 102, 
                'room' => 'Grand Ballroom Kencana', 
                'floor' => 'Lantai 3', 
                'price' => '5.500.000', 
                'status' => 'Pending', 
                'outsource' => 'Siti Aminah', 
                'rek' => 'Layak',
                'is_dummy' => true
            ],
            (object)[
                'id' => 103, 
                'room' => 'Diponegoro Suite', 
                'floor' => 'Lantai 2', 
                'price' => '750.000', 
                'status' => 'Diterima', 
                'outsource' => 'Budi Santoso', 
                'rek' => 'Layak',
                'is_dummy' => true
            ],
            (object)[
                'id' => 104, 
                'room' => 'Studio Foto Malang', 
                'floor' => 'Lantai 1', 
                'price' => '300.000', 
                'status' => 'Ditolak', 
                'outsource' => 'Siti Aminah', 
                'rek' => 'Tidak Layak',
                'is_dummy' => true
            ],
        ]);

        // Transformasikan data dari database ke format yang sama dengan dummy
        $dbPendingReports = $realPendingReports->map(function($item) {
            return (object)[
                'id' => $item->room->room_id,
                'room' => $item->room->name,
                'floor' => 'Lantai ' . ($item->room->floor ?? '1'),
                'price' => number_format($item->room->price, 0, ',', '.'),
                'status' => 'Pending',
                'outsource' => $item->surveyor->username ?? 'Outsource Partner',
                'rek' => 'Layak',
                'is_dummy' => false
            ];
        });

        $dbProcessedReports = $realProcessedReports->map(function($item) {
            $statusText = $item->room->status == 2 ? 'Diterima' : 'Ditolak';
            return (object)[
                'id' => $item->room->room_id,
                'room' => $item->room->name,
                'floor' => 'Lantai ' . ($item->room->floor ?? '1'),
                'price' => number_format($item->room->price, 0, ',', '.'),
                'status' => $statusText,
                'outsource' => $item->surveyor->username ?? 'Outsource Partner',
                'rek' => $item->room->status == 2 ? 'Layak' : 'Tidak Layak',
                'is_dummy' => false
            ];
        });

        // Gabungkan data DB dengan dummy agar dashboard tidak kosong saat demo
        $pendingReports = $dbPendingReports->concat($dummyReports->where('status', 'Pending'))->unique('room');
        $processedReports = $dbProcessedReports->concat($dummyReports->whereIn('status', ['Diterima', 'Ditolak']))->unique('room');

        // Menghitung data statistik box atas secara real-time
        $countWaiting = Room::where('status', 1)
                    ->whereDoesntHave('outsourceAssignments', function($query) {
                        $query->whereIn('assignment_status', ['on_the_way', 'checking']);
                    })->count();
        $countActive = OutsourceAssignment::whereIn('assignment_status', ['on_the_way', 'checking'])->count();
        $countSurveyor = People::where('role', 'outsource')->count();
        
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
        return view('admin.acc_room');
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
        $monitoring = OutsourceAssignment::with(['room', 'surveyor'])
                    ->whereIn('assignment_status', ['on_the_way', 'checking'])
                    ->get();

        // 3. Ambil data list pegawai surveyor kustom outsource untuk isi dropdown select
        // (Misal memfilter user yang memiliki role 'surveyor' atau 'outsource')
        $mitra = People::where('role', 'outsource')->get(); 

        // 4. Hitung data statistik box atas secara dinamis dari database
        $countWaiting = Room::where('status', 1)
                    ->whereDoesntHave('outsourceAssignments', function($query) {
                        $query->whereIn('assignment_status', ['on_the_way', 'checking']);
                    })->count();
        $countActive = OutsourceAssignment::whereIn('assignment_status', ['on_the_way', 'checking'])->count();
        $countSurveyor = People::where('role', 'outsource')->count();

        return view('admin.assign_outsource', compact('incoming', 'monitoring', 'mitra', 'countWaiting', 'countActive', 'countSurveyor'));
    }

    // Fungsi eksekusi tombol "Tugaskan" saat admin memilih surveyor
    public function assignSurveyor(Request $request, $room_id)
    {
        $request->validate([
            'surveyor_id' => 'required'
        ], [
            'surveyor_id.required' => 'Wajib memilih salah satu surveyor lapangan.'
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
            'surveyor_id'       => $request->surveyor_id,
            'assignment_status' => 'on_the_way', // Langsung aktif menuju lokasi
            'progress'          => 15            // Set awal progres ke 15% sesuai visual template
        ]);

        // (Opsional) Jika diperlukan, kamu bisa mengubah status ketersediaan awal 
        // di tabel rooms agar tidak muncul lagi di antrean penugasan baru:
        // Room::where('room_id', $room_id)->update(['status' => 2]); 

        return back()->with('success', 'Tugas baru berhasil dibuat dan surveyor lapangan telah ditugaskan!');
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

        // Fallback simulasi data dummy
        return redirect()->route('admin.dashboard')->with('success', 'Simulasi: Ruangan #' . $room_id . ' berhasil disetujui (Data Dummy)!');
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

        // Fallback simulasi data dummy
        return redirect()->route('admin.dashboard')->with('success', 'Simulasi: Pengajuan ruangan #' . $room_id . ' berhasil ditolak (Data Dummy)!');
    }
}
