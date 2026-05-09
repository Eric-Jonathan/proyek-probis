<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function autocompleteLocation(Request $request)
    {
        $query = $request->query('q');
        
        if (strlen($query) < 3) return response()->json([]);

        // Server-side request (API Key aman di server)
        $response = Http::get("https://api.locationiq.com/v1/autocomplete", [
            'key'          => config('services.locationiq.key'),
            'q'            => $query,
            'limit'        => 5,
            'dedupe'       => 1,
            'format'       => 'json',
            'countrycodes' => 'id', // Mengharuskan hasil hanya dari Indonesia
            'accept-language' => 'id', // Opsional: Menampilkan hasil dalam Bahasa Indonesia
        ]);

        return $response->json();
    }
}
