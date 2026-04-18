<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenyewaController extends Controller
{
    public function index() {
        return view('penyewa.dashboard');
    }

    public function searchPage() {
        return view('rooms.search_room');
    }
}
