<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

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
 
    public function show(Room $room)
    {
        return view('rooms.show', compact('room'));
    }
 
    public function edit(Room $room)
    {
        return view('rooms.form', compact('room'));
    }
}
