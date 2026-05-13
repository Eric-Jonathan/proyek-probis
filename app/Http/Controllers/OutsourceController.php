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
    // Koleksi Data Dummy Lengkap dengan Struktur Komparasi
    $allJobs = collect([
        // ID 101: SUDAH DITERIMA
        (object)[
            'id' => 101,
            'room' => 'Cozy Meeting Room',
            'city' => 'Batu',
            'address' => 'Jl. KH. Agus Salim No. 106, Kota Batu',
            'fee' => 500000,
            'tgl_kirim' => '10 May 2026',
            'status' => 'Diterima',
            'surveyor' => (object)[
                'kondisi' => 'Sangat Baik (9/10)',
                'kebersihan' => 'Sangat Bersih',
                'catatan' => 'Fasilitas lengkap, AC dingin, dan pencahayaan sangat baik.',
                'media' => [['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1000&q=80']]
            ],
            'pengaju' => (object)[
                'kondisi' => 'Sangat Baik (9/10)',
                'kebersihan' => 'Bersih',
                'catatan' => 'Ruang rapat minimalis yang nyaman untuk tim kecil.',
                'media' => [['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=1000&q=80']]
            ]
        ],

        // ID 102: SEDANG DICEK (PENDING)
        (object)[
            'id' => 102,
            'room' => 'Grand Ballroom Kencana',
            'city' => 'Batu',
            'address' => 'Jl. Ir. Soekarno No. 15, Kota Batu',
            'fee' => 5500000,
            'tgl_kirim' => '11 May 2026',
            'status' => 'Pending',
            'surveyor' => (object)[
                'kondisi' => 'Baik (8/10)',
                'kebersihan' => 'Bersih',
                'catatan' => 'Ruangan sangat luas, karpet baru saja dibersihkan. Namun ada sedikit lecet di dinding pojok.',
                'media' => [
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1000&q=80'],
                    ['type' => 'video', 'url' => 'https://www.w3schools.com/html/mov_bbb.mp4']
                ]
            ],
            'pengaju' => (object)[
                'kondisi' => 'Sangat Baik (10/10)',
                'kebersihan' => 'Sangat Bersih',
                'catatan' => 'Ballroom mewah dalam kondisi prima, fasilitas premium, siap pakai tanpa kendala.',
                'media' => [['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1431540015161-0bf868a2d407?auto=format&fit=crop&w=1000&q=80']]
            ]
        ],

        // ID 103: SUDAH DITERIMA
        (object)[
            'id' => 103,
            'room' => 'Diponegoro Suite',
            'city' => 'Malang',
            'address' => 'Jl. Diponegoro No. 2, Kota Malang',
            'fee' => 750000,
            'tgl_kirim' => '09 May 2026',
            'status' => 'Diterima',
            'surveyor' => (object)[
                'kondisi' => 'Sangat Baik (9/10)',
                'kebersihan' => 'Sangat Bersih',
                'catatan' => 'Kondisi interior sangat mewah, semua elektronik berfungsi normal.',
                'media' => [['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=80']]
            ],
            'pengaju' => (object)[
                'kondisi' => 'Sangat Baik (10/10)',
                'kebersihan' => 'Sangat Bersih',
                'catatan' => 'Kamar tipe suite terbaik dengan pemandangan kota.',
                'media' => [['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80']]
            ]
        ],

        // ID 104: SUDAH DITOLAK
        (object)[
            'id' => 104,
            'room' => 'Studio Foto Malang',
            'city' => 'Malang',
            'address' => 'Jl. Borobudur No. 12, Malang',
            'fee' => 300000,
            'tgl_kirim' => '08 May 2026',
            'status' => 'Ditolak',
            'surveyor' => (object)[
                'kondisi' => 'Kurang (5/10)',
                'kebersihan' => 'Kotor',
                'catatan' => 'Banyak debu di area studio dan lampu beberapa ada yang mati, tidak sesuai deskripsi pengaju.',
                'media' => [['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1000&q=80']]
            ],
            'pengaju' => (object)[
                'kondisi' => 'Baik (8/10)',
                'kebersihan' => 'Bersih',
                'catatan' => 'Studio foto lengkap dengan berbagai background.',
                'media' => [['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1598425237654-4fc758e50a93?auto=format&fit=crop&w=1000&q=80']]
            ]
        ],
    ]);

    // Cari data berdasarkan ID, jika tidak ketemu ambil data pertama
    $job = $allJobs->firstWhere('id', (int)$id) ?? $allJobs->first();

    return view('outsource.detail_history', compact('job', 'id'));
}
}
