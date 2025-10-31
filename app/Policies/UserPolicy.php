<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('read:user')
            ? Response::allow()
            : Response::deny('You do not have permission to view users.');
    }

    public function viewPulse(User $user): Response
    {
        return $user->hasPermissionTo('view_pulse')
            ? Response::allow()
            : Response::deny('You do not have permission to view the Pulse dashboard.');
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): Response
    {
        return $user->hasPermissionTo('read:user')
            ? Response::allow()
            : Response::deny('You do not have permission to view this user.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo('create:user')
            ? Response::allow()
            : Response::deny('You do not have permission to create a user.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        return $user->hasPermissionTo('update:user')
            ? Response::allow()
            : Response::deny('You do not have permission to update this user.');
    }

    public function activeInactive(User $user, User $model): Response
    {
        return $user->hasPermissionTo('active-inactive:user')
            ? Response::allow()
            : Response::deny('You do not have permission to update this user.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        return $user->hasPermissionTo('delete:user')
            ? Response::allow()
            : Response::deny('You do not have permission to delete this user.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): Response
    {
        return $user->hasPermissionTo('delete:user')
            ? Response::allow()
            : Response::deny('You do not have permission to restore this user.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response
    {
        return $user->hasPermissionTo('delete:user')
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this user.');
    }
}
