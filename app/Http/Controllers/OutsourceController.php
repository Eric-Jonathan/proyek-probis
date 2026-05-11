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
    // 1. Kumpulan Data Dummy Lengkap
    // Saya tambahkan ID 105 agar sesuai dengan yang ada di view history kamu
    $allJobs = collect([
        (object)[
            'id' => 101,
            'room' => 'Kontena Hotel - Ball Room',
            'city' => 'Batu',
            'address' => 'Jl. KH. Agus Salim No. 106, Kota Batu',
            'fee' => 250000,
            'deadline' => '2026-05-15',
            'priority' => 'High',
            'kondisi' => 'Sangat Baik (9/10)',
            'kebersihan' => 'Sangat Bersih',
            'catatan' => 'Fasilitas lengkap, AC dingin, dan pencahayaan sangat baik.',
            'tgl_kirim' => '2026-05-10',
            'status' => 'Diterima'
        ],
        (object)[
            'id' => 102,
            'room' => 'Lab Komputer Bisnis',
            'city' => 'Malang',
            'address' => 'Jl. Soekarno Hatta No. 9, Malang',
            'fee' => 150000,
            'deadline' => '2026-05-18',
            'priority' => 'Medium',
            'kondisi' => 'Baik (7/10)',
            'kebersihan' => 'Cukup',
            'catatan' => 'Beberapa kursi perlu dirapikan, namun secara fungsi siap digunakan.',
            'tgl_kirim' => '2026-05-11',
            'status' => 'Diterima'
        ],
        (object)[
            'id' => 105, // ID ini yang tadi menyebabkan 404
            'room' => 'Studio Foto Malang',
            'city' => 'Malang',
            'address' => 'Jl. Borobudur No. 12, Malang',
            'fee' => 175000,
            'deadline' => '2026-05-20',
            'priority' => 'Low',
            'kondisi' => 'Cukup (5/10)',
            'kebersihan' => 'Kotor',
            'catatan' => 'Banyak debu di area properti foto, perlu pembersihan menyeluruh sebelum disewakan.',
            'tgl_kirim' => '2026-05-08',
            'status' => 'Revisi'
        ],
    ]);

    // 2. Pencarian data berdasarkan ID dari URL
    $job = $allJobs->firstWhere('id', $id);

    // 3. Logic Bypass untuk Tes Visual
    // Jika kamu memasukkan ID yang tidak ada di list (misal 999)
    // sistem akan tetap menampilkan data agar tidak 404
    if (!$job) {
        $job = $allJobs->first(); // Default ambil data Kontena Hotel
    }

    // 4. Kirim ke view
    return view('outsource.detail_history', compact('job', 'id'));
}
}
