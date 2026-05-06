<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class PeopleController extends Controller
{
    // Menampilkan semua data (Read)
    public function people()
    {
        $users = People::orderBy('status', 'desc')->orderBy('created_at', 'desc')->get();
        $totalUser = $users->count();
        $totalActive = People::where('status', 1)->count();
        $totalInactive = People::where('status', 0)->count();
        return view('admin.users', compact('users', 'totalUser', 'totalActive', 'totalInactive'));
    }

    // Menambahkan data baru (Create)
    public function insertPeople(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:people,username',
            'email'    => 'required|email|unique:people,email',
            'password' => 'required|min:6',
            'role'     => 'required',
        ]);

        People::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
            'phone'    => $request->phone,
            'role'     => $request->role,
            'status'   => 'Aktif',
        ]);

        return redirect()->back()->with('success', 'People berhasil ditambahkan!');
    }

    // Mengubah data (Update)
    public function updatePeople(Request $request, $id)
    {
        $user = People::findOrFail($id);

        $request->validate([
            'username' => 'required|unique:people,username,' . $id . ',user_id',
            'email'    => 'required|email|unique:people,email,' . $id . ',user_id',
        ]);

        $data = [
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'status'   => $request->status,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Data user berhasil diperbarui!');
    }

    // Menghapus data (Delete)
    public function deletePeople($id)
    {
        $user = People::findOrFail($id);
        $user->status = 0;
        $user->save();

        return redirect()->back()->with('success', 'People berhasil dihapus!');
    }
}