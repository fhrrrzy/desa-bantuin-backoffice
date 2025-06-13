<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LaporanType;
use Illuminate\Auth\Access\Response;

class LaporanTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admin can view laporan types
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LaporanType $laporanType): bool
    {
        // Only admin can view laporan types
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // No one can create laporan types
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LaporanType $laporanType): bool
    {
        // No one can update laporan types
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LaporanType $laporanType): bool
    {
        // No one can delete laporan types
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LaporanType $laporanType): bool
    {
        // No one can restore laporan types
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LaporanType $laporanType): bool
    {
        // No one can permanently delete laporan types
        return false;
    }
}
