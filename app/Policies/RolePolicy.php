<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Determine whether the user can view any roles.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('read:role')
            ? Response::allow()
            : Response::deny('You do not have permission to view roles.');
    }

    /**
     * Determine whether the user can view a specific role.
     */
    public function view(User $user, Role $role): Response
    {
        return $user->hasPermissionTo('read:role')
            ? Response::allow()
            : Response::deny('You do not have permission to view this role.');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create:role')
            ? Response::allow()
            : Response::deny('You do not have permission to create roles.');
    }

    /**
     * Determine whether the user can update a role.
     */
    public function update(User $user, Role $role): Response
    {
        return $user->hasPermissionTo('update:role')
            ? Response::allow()
            : Response::deny('You do not have permission to update this role.');
    }

    /**
     * Determine whether the user can delete a role.
     */
    public function delete(User $user, Role $role): Response
    {
        return $user->hasPermissionTo('delete:role')
            ? Response::allow()
            : Response::deny('You do not have permission to delete this role.');
    }

    /**
     * Determine whether the user can restore a role.
     */
    public function restore(User $user, Role $role): Response
    {
        return $user->hasPermissionTo('delete:role')
            ? Response::allow()
            : Response::deny('You do not have permission to restore this role.');
    }

    /**
     * Determine whether the user can permanently delete a role.
     */
    public function forceDelete(User $user, Role $role): Response
    {
        return $user->hasPermissionTo('delete:role')
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this role.');
    }

    /**
     * Determine whether the user can assign a permission to a role.
     */
    public function assignPermission(User $user): Response
    {
        return $user->hasPermissionTo('assign:role')
            ? Response::allow()
            : Response::deny('You do not have permission to assign permissions to a role.');
    }

    /**
     * Determine whether the user can attach a permission to a role.
     */
    public function attachPermission(User $user): Response
    {
        return $user->hasPermissionTo('attach:permission')
            ? Response::allow()
            : Response::deny('You do not have permission to attach permissions to a role.');
    }

    /**
     * Determine whether the user can detach a permission from a role.
     */
    public function detachPermission(User $user): Response
    {
        return $user->hasPermissionTo('detach:permission')
            ? Response::allow()
            : Response::deny('You do not have permission to detach permissions from a role.');
    }
}
