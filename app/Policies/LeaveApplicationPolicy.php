<?php

namespace App\Policies;

use App\Models\LeaveApplication;
use App\Models\User;
//use Illuminate\Auth\Access\Response;

class LeaveApplicationPolicy
{

    public function before(User $user)
    {
        if ($user->role_id === 1) {
            return true;
        }
    }
    /**
     * // Allow staff and admins within the same tenant to view lists
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveApplication $leaveApplication): bool
    {
        // Must belong to the same tenant, and either be their own leave or an admin/manager
        return $user->tenant_id === $leaveApplication->tenant_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user within a tenant can apply for leave
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveApplication $leaveApplication): bool
    {
        // Must belong to the same tenant
        if ($user->tenant_id !== $leaveApplication->tenant_id) {
            return false;
        }

        // Admins (role_id === 2) can update anytime
        if ($user->role_id === 2) {
            return true;
        }

        // Regular staff can only update if it belongs to them AND status is pending
        return $leaveApplication->user_id === $user->id && $leaveApplication->status === 'pending';
    }

    /**
     * Determine whether the user can delete/withdraw the model.
     */
    public function delete(User $user, LeaveApplication $leaveApplication): bool
    {
       // Must belong to the same tenant
        if ($user->tenant_id !== $leaveApplication->tenant_id) {
            return false;
        }

        // Admins can delete anytime
        if ($user->role_id === 2) {
            return true;
        }

        // Regular staff can only withdraw if it belongs to them AND status is pending
        return $leaveApplication->user_id === $user->id && $leaveApplication->status === 'pending';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LeaveApplication $leaveApplication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LeaveApplication $leaveApplication): bool
    {
        return false;
    }
}
