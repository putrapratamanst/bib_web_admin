<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactGroupController extends Controller
{
    public function index()
    {
        return view('master.contact-group.index');
    }

    public function create()
    {
        return view('master.contact-group.create');
    }
}
