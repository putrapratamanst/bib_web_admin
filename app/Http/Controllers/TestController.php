<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        // load insurance with bank
        $insurance = Insurance::with('banks')->get();

        // return json
        return response()->json($insurance);
    }
}
