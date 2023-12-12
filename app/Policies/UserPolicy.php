<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ViewAuthUsers;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;

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

    public function confirmPassword(ViewAuthUsers $viewAuthUsers, User $model, Request $request): bool
    {
        $confirmation_password = $request['confirmation_password'];
        $confirmation_pin = $request['confirmation_pin'];
    

        if (!Hash::check($confirmation_pin, $viewAuthUsers->confirmation_code)) {
            throw new AuthorizationException('Pin provided does not match');
        }

        if (!Hash::check($confirmation_password, $viewAuthUsers->password)) {
            throw new AuthorizationException('Password provided does not match');
        }

        return true;
    }

}
