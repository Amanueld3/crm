<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Gate;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Permission::class);

        $search = $request->input('search');

        $permissions = Permission::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        $sortedPermissions = $permissions->sortBy(function ($permission) {
            return explode(':', $permission->name)[1] ?? '';
        });

        return $sortedPermissions->values();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Permission::class);

        abort(403, 'Unauthorized action.');
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return $permission;
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        Gate::authorize('view', $permission);
        return $permission;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        Gate::authorize('update', $permission);
        abort(403, 'Unauthorized action.');

        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        $permission->update([
            'name' => $request->name,
            'description' => $request->description ?? $permission->description,
        ]);

        return $permission;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        Gate::authorize('delete', $permission);
        if ($permission->roles()->count() > 0) {
            return response()->json(['message' => 'Permission is in use'], 423);
        }
        $permission->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
