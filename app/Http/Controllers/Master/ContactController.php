<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('master.contact.index');
    }

    public function create()
    {
        return view('master.contact.create');
    }

    public function show($id)
    {
        $contactGroups = ContactGroup::get();
        $contact = Contact::find($id);

        return view('master.contact.show', [
            'contact' => $contact,
            'contactGroups' => $contactGroups
        ]);
    }
}
