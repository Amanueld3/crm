<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('read:permission')
            ? Response::allow()
            : Response::deny('You do not have permission to read permissions');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): Response
    {
        return $user->hasPermissionTo('read:permission')
            ? Response::allow()
            : Response::deny('You do not have permission to read this permission');
    }

    

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create:permission')
            ? Response::allow()
            : Response::deny('You do not have permission to create a permission');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): Response
    {
        return $user->hasPermissionTo('update:permission')
            ? Response::allow()
            : Response::deny('You do not have permission to update this permission');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): Response
    {
        return $user->hasPermissionTo('delete:permission')
            ? Response::allow()
            : Response::deny('You do not have permission to delete this permission');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): Response
    {
        return $user->hasPermissionTo('delete:permission')
            ? Response::allow()
            : Response::deny('You do not have permission to restore this permission');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): Response
    {
        return $user->hasPermissionTo('delete:permission')
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this permission');
    }
}
