<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('floor', 'like', '%' . $request->search . '%');
            });
        }
 
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
 
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
 
        $rooms     = $query->latest()->paginate(10)->withQueryString();
        // $roomTypes = Room::distinct()->pluck('type');
 
        return view('rooms.room', [
            'rooms'            => $rooms,
            // 'roomTypes'        => $roomTypes,
            'totalRooms'       => Room::count(),
            'activeRooms'      => Room::where('status', 'active')->count(),
            'maintenanceRooms' => Room::where('status', 'maintenance')->count(),
            'inactiveRooms'    => Room::where('status', 'inactive')->count(),
        ]);
    }
 
    public function create()
    {
        return view('rooms.form');
    }
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'floor'          => 'required|integer|min:1|max:100',
            'capacity'       => 'required|integer|min:1',
            'deposit_percent'=> 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description'    => 'nullable|string',
            'status'         => 'required|in:0,1,2',
            'facilities'     => 'nullable|array',
            'location'       => 'required|string|max:255',
            'rules'          => 'required|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
 
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        // $validated['user_id'] = auth()->id();
        $validated['user_id'] = 1;
 
        $validated['facilities'] = $request->input('facilities', []);
 
        Room::create($validated);
 
        return redirect()->route('rooms.index')
                         ->with('success', 'Ruangan berhasil ditambahkan!');
    }
 
    // public function show(Room $room)
    // {
    //     return view('rooms.show', compact('room'));
    // }

    public function show()
    {
        $room = (object)[
        'name' => 'Kontena Hotel',
        'capacity' => 50,
        'price' => 100000,
        'deposit_percent' => 30,
        'location' => "KH. Agus Salim No.106, Sisir, Kec. Batu, Kota Batu, Jawa Timur 65314",
        'rules' => [
            'Dilarang merokok di dalam kamar',
            'Tidak diperbolehkan membawa hewan peliharaan',
            'Check-in mulai pukul 14:00',
            'Menunjukkan identitas saat check-in'
        ],
        'description' => "tempatnya bagus mungkin",
        'embed_url' => "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d10872.509987186995!2d112.52550598185628!3d-7.886403981190083!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78812756d6007b%3A0x5a7d48319c393cb3!2sKontena%20Hotel!5e0!3m2!1sen!2sid!4v1775574543337!5m2!1sen!2sid"
        ];
        return view('rooms/room_detail', compact('room'));
    }
 
    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'deposit_percent' => 'nullable|integer|min:0|max:100',
            'location' => 'required|string|max:255',
            'rules' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|integer|in:0,1,2',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Ruangan berhasil diupdate!');
    }
    public function destroy(Room $room)
    {
        $room->delete();

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
