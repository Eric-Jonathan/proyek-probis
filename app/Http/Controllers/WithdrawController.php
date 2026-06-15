<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'penyedia') {
            abort(403, 'Anda tidak memiliki akses untuk melakukan penarikan saldo.');
        }
        return view('auth.withdraw', compact('user'));
    }

    public function process(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'penyedia') {
            abort(403, 'Anda tidak memiliki akses untuk melakukan penarikan saldo.');
        }

        $request->validate([
            'amount' => 'required|integer|min:10000|max:' . $user->saldo,
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
        ], [
            'amount.max' => 'Saldo Anda tidak mencukupi untuk melakukan penarikan sebesar ini.',
            'amount.min' => 'Jumlah penarikan minimal adalah Rp 10.000.',
        ]);

        $amount = (int) $request->amount;

        // Deduct balance from the database
        $dbUser = People::findOrFail($user->user_id);
        $dbUser->saldo -= $amount;
        $dbUser->save();

        return redirect()->route('profile.show')->with('success', 'Penarikan saldo sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil diproses ke rekening ' . $request->bank_name . ' (' . $request->account_number . ') atas nama ' . $request->account_name . '.');
    }
}
