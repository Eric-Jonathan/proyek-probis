<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OutsourceController extends Controller
{
    public function index() {
        return view('outsource.dashboard');
    }

    public function history(){
        return view('outsource.history');
    }

    public function form(){
        return view('outsource.form');
    }

    public function jobList() {
        // Data dummy semua project untuk halaman List Job
        $allJobs = [
            (object)[
                'id' => 101,
                'room' => 'Kontena Hotel - Ball Room',
                'city' => 'Batu',
                'fee' => 250000,
                'deadline' => '2026-05-15',
                'priority' => 'High'
            ],
            (object)[
                'id' => 102,
                'room' => 'Lab Komputer Bisnis',
                'city' => 'Malang',
                'fee' => 150000,
                'deadline' => '2026-05-18',
                'priority' => 'Medium'
            ],
            (object)[
                'id' => 103,
                'room' => 'Meeting Room Ijen',
                'city' => 'Malang',
                'fee' => 175000,
                'deadline' => '2026-05-20',
                'priority' => 'Low'
            ],
        ];

        return view('outsource.list_job', compact('allJobs'));
    }

public function historyDetail($id)
{
    // Struktur data sudah disamakan agar variabel seperti $job->room, $job->status, dll 
    // konsisten di seluruh aplikasi.
    $allJobs = collect([
        (object)[
            'id' => 101,
            'room' => 'Cozy Meeting Room', // Sebelumnya 'name'
            'city' => 'Batu',
            'floor' => 'Lantai 1',
            'address' => 'Jl. KH. Agus Salim No. 106, Kota Batu',
            'fee' => 500000,
            'deadline' => '2026-05-15',
            'kondisi' => 'Sangat Baik (9/10)',
            'kebersihan' => 'Sangat Bersih',
            'catatan' => 'Fasilitas lengkap, AC dingin, dan pencahayaan sangat baik.',
            'tgl_kirim' => '2026-05-10',
            'status' => 'Diterima' // Status sinkron untuk pengecekan tombol cetak
        ],
        (object)[
            'id' => 102,
            'room' => 'Grand Ballroom Kencana',
            'city' => 'Batu',
            'floor' => 'Lantai 3',
            'address' => 'Jl. Ir. Soekarno No. 15, Kota Batu',
            'fee' => 5500000,
            'deadline' => '2026-05-18',
            'kondisi' => 'Baik (8/10)',
            'kebersihan' => 'Bersih',
            'catatan' => 'Ruangan sangat luas, karpet baru saja dibersihkan.',
            'tgl_kirim' => '2026-05-11',
            'status' => 'Pending' // Admin akan melihat tombol Setuju/Tolak
        ],
        (object)[
            'id' => 103,
            'room' => 'Diponegoro Suite',
            'city' => 'Malang',
            'floor' => 'Lantai 2',
            'address' => 'Jl. Diponegoro No. 2, Kota Malang',
            'fee' => 750000,
            'deadline' => '2026-05-12',
            'kondisi' => 'Sangat Baik (9/10)',
            'kebersihan' => 'Sangat Bersih',
            'catatan' => 'Siap digunakan untuk tamu VIP.',
            'tgl_kirim' => '2026-05-09',
            'status' => 'Diterima'
        ],
        (object)[
            'id' => 104,
            'room' => 'Studio Foto Malang',
            'city' => 'Malang',
            'floor' => 'Lantai 1',
            'address' => 'Jl. Borobudur No. 12, Malang',
            'fee' => 300000,
            'deadline' => '2026-05-20',
            'kondisi' => 'Kurang (5/10)',
            'kebersihan' => 'Kotor',
            'catatan' => 'Banyak debu dan lampu beberapa mati.',
            'tgl_kirim' => '2026-05-08',
            'status' => 'Ditolak' // Tombol cetak akan disable
        ],
    ]);

    // Pencarian data berdasarkan ID
    $job = $allJobs->firstWhere('id', $id);

    // Bypass jika ID tidak ditemukan untuk kebutuhan tes visual
    if (!$job) {
        $job = $allJobs->first();
    }

    return view('outsource.detail_history', compact('job', 'id'));
}
}
