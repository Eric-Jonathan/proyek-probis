<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;
class RoomController extends Controller
{
    public function index(Request $request)
    {
        // Ambil ID user yang sedang login
        $userId = Auth::id(); 

        // 1. Mulai query builder (Tanpa ->get() di awal agar filter di bawahnya berfungsi)
        $query = Room::where('user_id', $userId)->where('status', '>=', 0);

        // 2. Filter Pencarian
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        // 3. Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. Ambil data dengan pagination di akhir rangkaian filter
        $rooms = $query->latest()->get();

        return view('rooms.room', [
            'rooms'         => $rooms,
            'totalRooms'    => Room::where('user_id', $userId)->where('status', '>=', 0)->count(),
            'activeRooms'   => Room::where('user_id', $userId)->where('status', 2)->count(),
            'diajukan'      => Room::where('user_id', $userId)->where('status', 1)->count(),
            'inactiveRooms' => Room::where('user_id', $userId)->where('status', 0)->count(),
        ]);
    }
 
    public function create()
    {
        return view('rooms.form');
    }
 
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name'           => 'required|string|max:255',
    //         'floor'          => 'required|integer|min:1|max:100',
    //         'capacity'       => 'required|integer|min:1',
    //         'deposit_percent'=> 'required|integer|min:1',
    //         'price' => 'required|numeric|min:0',
    //         'description'    => 'nullable|string',
    //         'status'         => 'required|in:0,1,2',
    //         'facilities'     => 'nullable|array',
    //         'location'       => 'required|string|max:255',
    //         'rules'          => 'required|string',
    //         'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    //     ]);
 
    //     if ($request->hasFile('image')) {
    //         $validated['image'] = $request->file('image')->store('rooms', 'public');
    //     }

    //     // $validated['user_id'] = auth()->id();
    //     $userId = Auth::id();
    //     $validated['user_id'] = $userId;
 
    //     $validated['facilities'] = $request->input('facilities', []);
 
    //     Room::create($validated);
 
    //     return redirect()->route('rooms.index')
    //                      ->with('success', 'Ruangan berhasil ditambahkan!');
    // }
 
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'capacity'        => 'required|integer|min:1',
            'deposit_percent' => 'required|integer|min:0|max:100',
            'jenis_deposit'   => 'required',
            'price'           => 'required|numeric|min:1000',
            'jenis_harga'     => 'required',
            'min_order'       => 'required|numeric|min:1',
            'day'             => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'status'          => 'required|in:1,2,3',
            'location'        => 'required',
            'latitude'        => 'required|numeric',
            'longitude'       => 'required|numeric',
            'rules'           => 'nullable|string',
            'images.*'        => 'image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'latitude.required' => 'Lokasi harus dipilih dari saran autocomplete.',
            'images.*.image'    => 'Berkas yang diunggah harus berupa gambar.',
            'images.*.max'      => 'Ukuran setiap foto tidak boleh melebihi 2MB.',
        ]);

        // 2. HITUNG & VALIDASI JUMLAH FOTO DI AWAL (Sebelum Sentuh Database)
        $newPhotosCount = $request->hasFile('images') ? count($request->file('images')) : 0;

        if ($newPhotosCount < 5) {
            return back()->withErrors(['images' => 'Total keseluruhan foto ruangan kurang dari 5.'])->withInput();
        }

        // 3. Masukkan User ID dari Session Login setelah dipastikan foto aman
        $validated['user_id'] = Auth::id();

        // 4. EKSEKUSI INSERT DATA RUANGAN (Aman dari data sampah)
        $room = Room::create($validated);

        // 5. Handle Upload Gambar (Karena Room sudah pasti punya ID)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Buat nama file unik
                $fileName = time() . '_' . $image->getClientOriginalName();
                
                // Pindahkan file ke folder public/upload_room
                $image->move(public_path('upload_room'), $fileName);
                
                // Simpan path hubungannya ke table room_images
                $room->images()->create([
                    'path' => 'upload_room/' . $fileName
                ]);
            }
        }

        // 6. Handle Insert Fasilitas Ruangan
        if ($request->has('facilities')) {
            foreach ($request->facilities as $facilityName) {
                $room->facilities()->create([
                    'name'   => $facilityName,
                    'status' => 1
                ]);
            }
        }

        // Redirect dengan Flash Message Sukses
        return redirect()->route('rooms.index')
                        ->with('success', 'Ruangan berhasil dipublikasikan!');
    }

    public function show($id)
    {
        // Ambil data ruangan beserta gambar, fasilitas aktif, dan rating dengan user pemberinya
        $room = Room::with([
            'images', 
            'facilities' => function($query) {
                $query->where('status', 1); // Hanya ambil fasilitas yang tersedia/aktif
            },
            'ratings.booking.user'
        ])->findOrFail($id);



        $ratings = $room->ratings;
        $totalReview = $ratings->count();
        $averageRating = 0.0;
        
        if ($totalReview > 0) {
            $sum = 0;
            foreach ($ratings as $r) {
                $sum += ($r->kebersihan + $r->pelayanan + $r->kenyamanan) / 3;
            }
            $averageRating = round($sum / $totalReview, 1);
        }

        // Dapatkan seluruh tanggal yang sudah terbooking (Full Book) untuk ruangan ini
        $bookedDates = [];
        $bookings = \App\Models\Booking::whereIn('status', [1, 2])
            ->whereHas('details', function($q) use ($id) {
                $q->where('item_type', 1)->where('item_id', $id);
            })
            ->get(['start_date', 'end_date']);

        foreach ($bookings as $b) {
            $start = new \DateTime($b->start_date);
            $end = new \DateTime($b->end_date);
            $interval = new \DateInterval('P1D');
            $start->setTime(0, 0, 0);
            $end->setTime(0, 0, 0);
            
            $current = clone $start;
            while ($current <= $end) {
                $bookedDates[] = $current->format('Y-m-d');
                $current->add($interval);
            }
        }
        $bookedDates = array_values(array_unique($bookedDates));
        
        return view('rooms.room_detail', compact('room', 'averageRating', 'totalReview', 'bookedDates'));
    }
 
    public function edit(Room $room)
    {
        // Load relasi facilities dan images bawaan room
        $room->load(['facilities', 'images']);
        
        return view('rooms.form', compact('room'));
    }
    
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'capacity'        => 'required|integer|min:1',
            'deposit_percent' => 'required|integer|min:0|max:100',
            'jenis_deposit'   => 'required',
            'price'           => 'required|numeric|min:1000',
            'jenis_harga'     => 'required',
            'min_order'       => 'required|numeric|min:1',
            'day'             => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'status'          => 'required|in:1,2,3',
            'location'        => 'required',
            'latitude'        => 'required|numeric',
            'longitude'       => 'required|numeric',
            'rules'           => 'nullable|string',
            'images.*'        => 'image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'latitude.required' => 'Lokasi harus dipilih dari saran autocomplete.',
            'images.*.image'    => 'Berkas yang diunggah harus berupa gambar.',
            'images.*.max'      => 'Ukuran setiap foto tidak boleh melebihi 2MB.',
        ]);

        $currentOldPhotosCount = 0;
        $totalDeleted = $request->has('deleted_images') ? count($request->deleted_images) : 0;
        $currentOldPhotosCount = $room->images()->count() - $totalDeleted;

        // 3. Hitung jumlah file baru yang diunggah
        $newPhotosCount = $request->hasFile('images') ? count($request->file('images')) : 0;

        // 4. Hitung Total Gabungan Akhir di sisi Server
        if (($currentOldPhotosCount + $newPhotosCount) < 5) {
            return back()->withErrors(['images' => 'Total keseluruhan foto ruangan kurang dari 5. Mohon unggah foto tambahan.'])->withInput();
        }

        // 1. Jalankan update data utama ruangan
        $room->update($validated);

        // 2. PROSES PENGHAPUSAN FOTO YANG DISILANG
        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $imgId) {
                // Cari model gambar terkait
                $image = $room->images()->where('image_id', $imgId)->first();
                
                if ($image) {
                    // Hapus file fisik dari folder public/rooms jika file eksis
                    $filePath = public_path($image->path);
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                    
                    // Hapus baris dari database
                    $image->delete();
                }
            }
        }

        // 3. PROSES UPLOAD FOTO BARU (Jika ada)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $fileName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('rooms'), $fileName);
                
                $room->images()->create([
                    'path' => 'rooms/' . $fileName
                ]);
            }
        }

        // 4. Sinkronisasi fasilitas (Gunakan logika sinkronisasi sebelumnya)
        if ($request->has('facilities')) {
            $room->facilities()->whereNotIn('name', $request->facilities)->delete();
            foreach ($request->facilities as $facilityName) {
                $room->facilities()->firstOrCreate([
                    'name' => $facilityName,
                    'status' => 1
                ]);
            }
        } else {
            $room->facilities()->delete();
        }

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil diperbarui!');
    }
    
    public function destroy(Room $room)
    {
        $room->update(['status' => -1]);

        return redirect()->route('rooms.index')
            ->with('success', 'Ruangan berhasil dihapus!');
    }

    public function transaction($id)
    {
        /*
        =====================================
        MIDTRANS CONFIG
        =====================================
        */
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        /*
        =====================================
        DUMMY DATA
        =====================================
        */
        $transaction = (object)[
            'id'         => $id,
            'name'       => 'Kontena Hotel',
            'date'       => now(),
            'place'      => 'Batu, Malang',
            'price'      => 100000,
            'start_date' => now(),
            'end_date'   => now()->addDay(),
        ];

        /*
        =====================================
        PARAM MIDTRANS
        =====================================
        */
        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . time() . rand(100,999),
                'gross_amount' => (int) $transaction->price,
            ],

            'customer_details' => [
                'first_name' => 'Guest',
                'email' => 'guest@mail.com',
                'phone' => '08123456789',
            ],

            'item_details' => [
                [
                    'id' => $transaction->id,
                    'price' => (int) $transaction->price,
                    'quantity' => 1,
                    'name' => $transaction->name,
                ]
            ]
        ];

        /*
        =====================================
        REQUEST TOKEN KE MIDTRANS
        TANPA PACKAGE SNAP::getSnapToken()
        =====================================
        */
        $response = Http::withBasicAuth(
            config('midtrans.server_key'),
            ''
        )
        ->withoutVerifying() // untuk localhost windows
        ->post(
            'https://app.sandbox.midtrans.com/snap/v1/transactions',
            $params
        );

        $result = $response->json();

        /*
        =====================================
        JIKA GAGAL
        =====================================
        */
        if (!isset($result['token'])) {
            dd($result);
        }

        /*
        =====================================
        TOKEN BERHASIL
        =====================================
        */
        $snapToken = $result['token'];

        return view('rooms.transaction', compact('transaction', 'snapToken'));
    }
}
