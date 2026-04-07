<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenyediaController extends Controller
{
    public function index() {
        return view('penyedia.dashboard');
    }
}
