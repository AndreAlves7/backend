<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ViewAuthUsers;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(ViewAuthUsers $viewAuthUsers): bool
    {
        //
        return $viewAuthUsers->user_type == "A";

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(ViewAuthUsers $viewAuthUsers, User $model): bool
    {
        //
        return $viewAuthUsers->user_type == "A";
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(ViewAuthUsers $viewAuthUsers): bool
    {
        //
        return $viewAuthUsers->user_type == "A";
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(ViewAuthUsers $viewAuthUsers, User $model): bool
    {
        //
        return $viewAuthUsers->user_type == "A";
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(ViewAuthUsers $viewAuthUsers, User $model): bool
    {
        //
        return $viewAuthUsers->user_type == "A";
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(ViewAuthUsers $viewAuthUsers, User $model): bool
    {
        //
        return $viewAuthUsers->user_type == "A";
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(ViewAuthUsers $viewAuthUsers, User $model): bool
    {
        //
        return $viewAuthUsers->user_type == "A";
    }
}
