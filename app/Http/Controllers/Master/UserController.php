<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('master.user.index');
    }

    public function create()
    {
        return view('master.user.create');
    }

    public function edit($id)
    {
        $user = User::where('company_id', auth()->user()->company_id)->findOrFail($id);
        return view('master.user.edit', compact('user'));
    }
}
