<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Outsource;
use Illuminate\Support\Facades\Hash;

class PeopleController extends Controller
{
    // Menampilkan semua data (Read)
    public function people()
    {
        // Hanya tampilkan user yang aktif (status = 1) agar fungsi delete terasa bekerja
        $users = People::where('status', '>=', 0)
                    ->orderBy('status', 'desc')
                    ->orderBy('username', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $totalUser = People::where('status', 1)->count();
        $totalActive = People::where('status', 1)->count();
        $totalInactive = People::where('status', 0)->count(); // Penghitungan history sampah

        return view('admin.users', compact('users', 'totalUser', 'totalActive', 'totalInactive'));
    }

    // Halaman Tunggal Form: Mendukung Insert & Edit Sekaligus
    public function formPeople(Request $request)
    {
        $user = null;
        
        // Jika ada parameter query ?edit={id}, maka mode berubah menjadi Edit
        if ($request->has('edit')) {
            $user = People::findOrFail($request->query('edit'));
        }

        // Ambil daftar perusahaan dari tabel outsources untuk isi select surveyor
        $companies = Outsource::where('status', 1)->get();

        return view('admin.form_users', compact('user', 'companies'));
    }

    // Menambahkan data baru atau Memperbarui data lama (Create / Update handler)
    public function insertPeople(Request $request)
    {
        $id = $request->input('user_id');

        if ($id) {
            // =========================================================================
            // PROSES UPDATE DATA LAMA
            // =========================================================================
            $user = People::findOrFail($id);

            $request->validate([
                'username' => 'required|unique:people,username,' . $id . ',user_id',
                'email'    => 'required|email|unique:people,email,' . $id . ',user_id',
                'role'     => 'required',
            ]);

            $data = [
                'username' => $request->username,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'role'     => $request->role,
                // Jika role diganti dari outsource ke yang lain, bersihkan kolom outsource_id
                'outsource_id' => $request->role === 'outsource' ? $request->company : null,
            ];

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:6']);
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            $msg = 'Data pengguna berhasil diperbarui!';

        } else {
            // =========================================================================
            // PROSES INSERT DATA BARU
            // =========================================================================
            $request->validate([
                'username' => 'required|unique:people,username',
                'email'    => 'required|email|unique:people,email',
                'password' => 'required|min:6',
                'role'     => 'required',
            ]);

            People::create([
                'username'     => $request->username,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
                'phone'        => $request->phone,
                'role'         => $request->role,
                'outsource_id' => $request->role === 'outsource' ? $request->company : null,
                'status'       => 1, // Wajib angka integer 1 (Aktif), bukan string 'Aktif'
            ]);
            $msg = 'Pengguna baru berhasil didaftarkan!';
        }

        return redirect()->route('admin.users')->with('success', $msg);
    }

    // Menghapus data (Soft Delete dengan mengubah status menjadi 0)
    public function deletePeople($id)
    {
        $user = People::findOrFail($id);
        $user->update(['status' => 0]); // Ubah status jadi 0 agar hilang dari list utama

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil dihapus dari sistem.');
    }
}