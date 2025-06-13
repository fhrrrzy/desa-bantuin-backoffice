<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserRequest;
use Illuminate\Auth\Access\Response;

class UserRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Both admin and warga can view the list
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserRequest $userRequest): bool
    {
        // Admin can view all requests
        if ($user->isAdmin()) {
            return true;
        }

        // Warga can only view their own requests
        return $user->id === $userRequest->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only warga can create requests, admin cannot
        return $user->isWarga();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserRequest $userRequest): bool
    {
        // Admin can update any request
        if ($user->isAdmin()) {
            return true;
        }

        // Warga can only update their own requests
        return $user->id === $userRequest->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserRequest $userRequest): bool
    {
        // Admin can delete any request
        if ($user->isAdmin()) {
            return true;
        }

        // Warga can only delete their own requests
        return $user->id === $userRequest->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserRequest $userRequest): bool
    {
        // Only admin can restore deleted requests
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserRequest $userRequest): bool
    {
        // Only admin can permanently delete requests
        return $user->isAdmin();
    }
}
