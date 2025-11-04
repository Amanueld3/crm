<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationWithUsernameRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends AuthController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $perPage = $request->per_page ?? 10;

        return User::when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('username', 'like', '%' . $request->search . '%');
        })->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->with('roles.permissions')->paginate($perPage);
    }

    public function updateStatus(Request $request, User $user)
    {
        Gate::authorize('activeInactive', $user);
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);
        $statusMap = [
            0 => 'active',
            1 => 'inactive',
        ];

        $status = $request->status;
        $statusValue = array_search($status, $statusMap);

        if ($statusValue === 'inactive') {
            return response()->json([
                'message' => 'Invalid status value',
            ], 400);
        }

        if ($status == 'active') {
            if ($user->status == 'active') {
                return response()->json([
                    'message' => 'its already active',
                ], 400);
            }
        }

        if ($status == 'inactive') {
            if ($user->status == 'inactive') {
                return response()->json([
                    'message' => 'its already inactive',
                ], 400);
            }
        }

        $user->status = $request->status;
        $user->save();

        return $user;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRegistrationWithUsernameRequest $request)
    {
        // Gate::authorize('create', User::class);

        DB::beginTransaction();

        $user = $this->register([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $this->generateUniqueUsername($request->name),
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            foreach ($request->roles as $role) {
                $user->assignRole($role);
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'User created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);
        // $user->attachments = $user->attachments;

        return $user->load('roles.permissions');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        Gate::authorize('update', $user);

        try {

            if ($user->roles->contains('name', 'Super_Admin')) {
                abort(403, 'You can not update super admin');
            }

            $user->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
            ]);

            if ($request->has('roles')) {
                $user->roles()->detach();
                foreach ($request->roles as $role) {
                    $user->assignRole($role);
                }
            }
            return $user;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        try {
            if ($user->roles->contains('name', 'super_admin')) {
                abort(403, 'You can not delete super admin');
            }

            if ($user->id === Auth::id()) {
                abort(403, 'You can not delete yourself');
            }

            $user->delete();

            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function generateUniqueUsername($name)
    {
        $username = Str::slug($name);
        $existingUser = User::where('username', $username)->first();

        if ($existingUser) {
            $username .= rand(1000, 9999);
        }

        while (User::where('username', $username)->exists()) {
            $username = Str::slug($name) . rand(1000, 1000);
        }

        return $username;
    }
}
