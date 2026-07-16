<?php

namespace App\Policies;

use App\Models\User;
//use Illuminate\Auth\Access\Response;
// handles authorizing actions (like viewing, deleting, or updating) at the application level.
class UserPolicy
{
    // The 'before' method runs automatically before any other method.
    // It give SuperAdmins (Role 1) full access.
    public function before(User $user)
    {
        if ($user->role_id === 1) {
            return true;
        }
    }
    public function viewAny(User $user): bool
    {
        // Allow Admins/Staff to see the list of users in their own company
        return $user->role_id === 2;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        //return false;
        return $user->tenant_id === $model->tenant_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only allow if they are an admin of their company
        return $user->role_id === 2;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // A user can ONLY update another user if they belong to the same tenant
        return $user->tenant_id === $model->tenant_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
       // Prevent accidental self-deletion or cross-tenant deletion
        return $user->tenant_id === $model->tenant_id && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
