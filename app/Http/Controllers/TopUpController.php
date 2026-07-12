<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Midtrans\Snap;

class TopUpController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        if ($user->role === 'outsource') {
            // abort(403, 'Mitra outsource tidak memiliki saldo.');
        }
        if ($user->role === 'admin') {
            abort(403, 'Administrator tidak diperbolehkan melakukan top up saldo.');
        }
        return view('auth.topup', compact('user'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10000',
        ]);

        $user = Auth::user();
        if ($user->role === 'admin' || $user->role === 'outsource') {
            return response()->json([
                'success' => false,
                'message' => 'Top up tidak diperbolehkan untuk peran ini.'
            ], 403);
        }
        $amount = (int) $request->amount;

        // Konfigurasi Midtrans
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = config('services.midtrans.is_sanitized');
        Config::$is3ds        = config('services.midtrans.is_3ds');

        $orderId = 'TOPUP-' . $user->user_id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'item_details' => [
                [
                    'id' => 'TOPUP',
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Top Up Saldo Tempat-In',
                ]
            ]
        ];

        $snapToken = '';
        $isSimulated = false;

        try {
            $serverKey = config('services.midtrans.server_key') ?: config('midtrans.server_key');
            $isProduction = config('services.midtrans.is_production') ?: false;
            $baseUrl = $isProduction 
                ? 'https://app.midtrans.com/snap/v1/transactions' 
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            $response = Http::withBasicAuth($serverKey, '')
                ->withoutVerifying()
                ->post($baseUrl, $params);

            $result = $response->json();

            if (isset($result['token'])) {
                $snapToken = $result['token'];
            } else {
                throw new \Exception('Response does not contain snap token: ' . json_encode($result));
            }
        } catch (\Exception $e) {
            Log::error('Midtrans Top Up Error: ' . $e->getMessage());
            $snapToken = 'MOCK-TOPUP-TOKEN-' . uniqid();
            $isSimulated = true;
        }

        return response()->json([
            'token' => $snapToken,
            'isSimulated' => $isSimulated,
            'order_id' => $orderId
        ]);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        if ($user && ($user->role === 'admin' || $user->role === 'outsource')) {
            return response()->json([
                'success' => false,
                'message' => 'Top up tidak diperbolehkan untuk peran ini.'
            ], 403);
        }

        $userId = Auth::id();
        $user = People::findOrFail($userId);
        
        $user->saldo += $request->amount;
        $user->save();

        return response()->json([
            'success' => true,
            'new_balance' => $user->saldo
        ]);
    }
}
