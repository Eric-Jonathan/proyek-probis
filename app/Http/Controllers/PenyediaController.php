<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenyediaController extends Controller
{
    public function index() {
        return view('penyedia.dashboard');
    }

    public function form() {
        return view('penyedia.form');
    }
}
