<?php

namespace App\Http\Controllers;

use App\Models\OutsourceAssignment;
use App\Models\People;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

    public function acc_room(){
        return view('admin.acc_room');
    }

    public function assign_outsource(){
        return view('admin.assign_outsource');
    }
    
    public function outsource(){
        return view('admin.outsource');
    }

    public function create_outsource(){
        return view('admin.form_outsource');
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
}
