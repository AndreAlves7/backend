<?php

namespace App\Policies;

use App\Models\ViewAuthUsers;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(ViewAuthUser $user, ViewAuthUser $model) {
        return $user->type == "A" || $user->id == $model->id; 
    }

    public function update(ViewAuthUser $user, ViewAuthUser $model) {
        return $user->type == "A" || $user->id == $model->id; 
    }

    public function updatePassword(ViewAuthUser $user, ViewAuthUser $model) {
        return $user->id == $model->id; 
    }
}
