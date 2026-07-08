<?php
namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister() { return view('auth.register'); }
    public function showLogin() { return view('auth.login'); }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:people,email',
            'phone' => ['required', 'string', 'regex:/^(\+?62|0)8[0-9]{8,13}$/'],
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required'
        ], [
            'phone.regex' => 'Nomor WhatsApp/HP tidak valid. Harus diawali 08, 62, atau +62 dan terdiri dari 10-15 digit angka.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.'
        ]);

        People::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil, silakan login.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->regenerate();
            
            if ($user->role == 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role == 'penyedia') {
                return redirect()->route('penyedia.dashboard');
            } elseif ($user->role == 'outsource') {
                return redirect()->route('outsource.dashboard');
            } else {
                return redirect()->route('penyewa.dashboard');
            }
        }


        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
