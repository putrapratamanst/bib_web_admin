<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactGroupStoreRequest;
use App\Http\Resources\ContactGroupResource;
use App\Models\ContactGroup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContactGroupController extends Controller
{
    public function index()
    {
        $contactGroups = ContactGroup::orderBy('name', 'asc')->get();

        return ContactGroupResource::collection($contactGroups);
    }

    public function datatables()
    {
        $contactGroups = ContactGroup::orderBy('name', 'asc')->get();

        return DataTables::of($contactGroups)
                ->make(true);
    }

    public function select2(Request $request)
    {
        $search = $request->q;

        $contactGroups = ContactGroup::where('name', 'like', "%$search%")
            ->orderBy('name', 'asc')
            ->get();

        $formattedUsers = $contactGroups->map(function ($d) {
            return ['id' => $d->id, 'text' => $d->name];
        });

        return response()->json([
            'items' => $formattedUsers
        ]);
    }

    public function store(ContactGroupStoreRequest $request)
    {
        try {
            $data = $request->validated();

            $contactGroup = ContactGroup::create($data);

            return response()->json([
                'message' => 'Data has been created',
                'data' => new ContactGroupResource($contactGroup)
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
