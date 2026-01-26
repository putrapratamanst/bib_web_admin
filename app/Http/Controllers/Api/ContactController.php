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
        $type = request('type');
        $query = Contact::query()
            ->with(['contactGroup', 'contactTypes'])
            ->orderBy('display_name', 'asc');
        if ($type) {
            $query->whereHas('contactTypes', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        $results = $query->get()->map(function($contact) {
            $typeValue = $contact->contactTypes->isNotEmpty() ? $contact->contactTypes->first()->type : '';
            
            return [
                'id' => $contact->id,
                'display_name' => $contact->display_name,
                'contact_group_id' => $contact->contact_group_id,
                'contact_group' => $contact->contactGroup ? $contact->contactGroup->name : '',
                'type' => $typeValue,
                'debug_contact_types_count' => $contact->contactTypes->count()
            ];
        });

        return DataTables::of($results)->make(true);
    }

    public function select2(Request $request)
    {
        $search = $request->get('search', '');
        $type = $request->get('type', '');
        $page = $request->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = Contact::query()
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('display_name', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                }
            })
            ->when($type, function ($query, $type) {
                return $query->whereHas('contactTypes', function ($query) use ($type) {
                    $query->where('type', $type);
                });
            })
            ->orderBy('display_name');

        $total = $query->count();
        $contacts = $query->offset($offset)->limit($limit)->get();

        $data = $contacts->map(function($contact) {
            return [
                'id' => $contact->id,
                'text' => $contact->display_name
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'more' => ($offset + $limit) < $total
            ]
        ]);
    }

    public function store(ContactStoreRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Extract type field (saved in separate table)
            $type = $data['type'];
            unset($data['type']);
            
            // Add audit fields
            $data['created_by'] = auth()->id() ?? 1;
            $data['updated_by'] = auth()->id() ?? 1;

            $contact = Contact::create($data);

            // save contact type
            $contact->contactTypes()->create([
                'type' => $type
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
            
            // Extract type field (saved in separate table)
            $type = $data['type'];
            unset($data['type']);
            
            // Add audit field for update
            $data['updated_by'] = auth()->id() ?? 1;

            $contact = Contact::find($id);
            $contact->update($data);

            // update contact type
            $contact->contactTypes()->update([
                'type' => $type
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
