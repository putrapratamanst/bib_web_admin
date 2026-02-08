<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillingAddressController extends Controller
{
    public function index($contactId)
    {
        $billingAddresses = BillingAddress::where('contact_id', $contactId)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'data' => $billingAddresses
        ]);
    }

    public function select2(Request $request)
    {
        $search = $request->get('search', $request->get('q', ''));
        $contactId = $request->get('contact_id', '');
        $page = $request->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $query = BillingAddress::query();

        if ($contactId) {
            $query->where('contact_id', $contactId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $addresses = $query->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $data = $addresses->map(function ($address) {
            $text = $address->name;
            if ($address->address) {
                $text .= ' - ' . substr($address->address, 0, 40);
            }

            return [
                'id' => $address->id,
                'text' => $text,
                'is_primary' => $address->is_primary
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'more' => ($offset + $limit) < $total
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
            'name' => 'required|max:300',
            'address' => 'required',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|max:20',
            'is_primary' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $data = $validator->validated();
            $data['created_by'] = auth()->id() ?? 1;
            $data['updated_by'] = auth()->id() ?? 1;

            // If is_primary is true, unset other primary addresses
            if ($data['is_primary'] ?? false) {
                BillingAddress::where('contact_id', $data['contact_id'])
                    ->update(['is_primary' => false]);
            }

            $billingAddress = BillingAddress::create($data);

            return response()->json([
                'message' => 'Billing address has been created',
                'data' => $billingAddress
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $billingAddress = BillingAddress::findOrFail($id);

            return response()->json([
                'data' => $billingAddress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'address' => 'required',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|max:20',
            'is_primary' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $billingAddress = BillingAddress::findOrFail($id);
            
            $data = $validator->validated();
            $data['updated_by'] = auth()->id() ?? 1;

            // If is_primary is true, unset other primary addresses
            if ($data['is_primary'] ?? false) {
                BillingAddress::where('contact_id', $billingAddress->contact_id)
                    ->where('id', '!=', $id)
                    ->update(['is_primary' => false]);
            }

            $billingAddress->update($data);

            return response()->json([
                'message' => 'Billing address has been updated',
                'data' => $billingAddress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $billingAddress = BillingAddress::findOrFail($id);
            $billingAddress->delete();

            return response()->json([
                'message' => 'Billing address has been deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function setPrimary($id)
    {
        try {
            $billingAddress = BillingAddress::findOrFail($id);
            
            // Unset all primary for this contact
            BillingAddress::where('contact_id', $billingAddress->contact_id)
                ->update(['is_primary' => false]);
            
            // Set this as primary
            $billingAddress->update([
                'is_primary' => true,
                'updated_by' => auth()->id() ?? 1
            ]);

            return response()->json([
                'message' => 'Primary billing address has been set',
                'data' => $billingAddress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
