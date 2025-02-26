<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactStoreRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::where('display_name', 'like', "%".request('q')."%")
            ->orWhere('name', 'like', "%".request('q')."%")
            ->orderBy('display_name', 'asc')
            ->paginate(10);

        return ContactResource::collection($contacts);
    }

    public function datatables()
    {
        $contacts = Contact::orderBy('display_name', 'asc')->get();

        $contacts->makeHidden('contact_types');

        return DataTables::of($contacts)
            ->addColumn('contact_group', function(Contact $c) {
                return $c->contactGroup ? $c->contactGroup->name : '';
            })
            ->make(true);
    }

    public function select2(Request $request)
    {
        $search = $request->q;
        $type = $request->type;

        $contacts = Contact::where('display_name', 'like', "%$search%")
            ->when($type, function ($query, $type) {
                return $query->whereHas('contactTypes', function ($query) use ($type) {
                    $query->where('type', $type);
                });
            })
            ->orderBy('display_name', 'asc')
            ->get();

        $formattedContacts = $contacts->map(function ($d) {
            return ['id' => $d->id, 'text' => $d->display_name];
        });

        return response()->json([
            'items' => $formattedContacts
        ]);
    }

    public function store(ContactStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $contact = Contact::create($data);

            // save contact type
            $contact->contactTypes()->create([
                'type' => $data['type']
            ]);

            return response()->json([
                'message' => 'Data has been created',
                'data' => new ContactResource($contact)
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(ContactStoreRequest $request, $id)
    {
        try {
            $data = $request->validated();

            $contact = Contact::find($id);
            $contact->update($data);

            // update contact type
            $contact->contactTypes()->update([
                'type' => $data['type']
            ]);

            return response()->json([
                'message' => 'Data has been updated',
                'data' => new ContactResource($contact)
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
