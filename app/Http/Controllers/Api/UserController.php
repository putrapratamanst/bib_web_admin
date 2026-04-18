<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();
        return response()->json(['data' => $users]);
    }

    public function datatables()
    {
        $users = User::orderBy('name', 'asc')->get();

        return DataTables::of($users)
            ->addColumn('role_badge', function($user) {
                $badgeClass = match($user->role) {
                    'admin' => 'bg-primary',
                    'approver' => 'bg-success',
                    'user' => 'bg-secondary',
                    default => 'bg-info'
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($user->role) . '</span>';
            })
            ->addColumn('action', function($user) {
                return '
                    <a href="' . route('master.users.edit', $user->id) . '" class="btn btn-sm btn-warning">Edit</a>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="' . $user->id . '">Delete</button>
                ';
            })
            ->rawColumns(['role_badge', 'action'])
            ->make(true);
    }

    public function store(UserStoreRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Hash password
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            return response()->json([
                'message' => 'User has been created successfully',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(UserStoreRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->validated();

            // Only update password if provided
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            return response()->json([
                'message' => 'User has been updated successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting own account
            if ($user->id === Auth::id()) {
                return response()->json([
                    'message' => 'You cannot delete your own account'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'message' => 'User has been deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
}
