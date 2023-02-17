<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackSizes;

class HomeController extends Controller
{
    public function home() 
    {
        $packSizes = PackSizes::all();

        return view('home', ['packSizes' => $packSizes]);
    }

    public function about() 
    {
        return view('about');
    }
}
