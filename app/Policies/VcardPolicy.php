<?php

namespace App\Policies;

use App\Models\Vcard;
use App\Models\ViewAuthUsers;
use Illuminate\Auth\Access\Response;

class VcardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(ViewAuthUsers $viewAuthUsers): bool
    {
        //
        // verifica se o usuário é administrador
        return $viewAuthUsers->user_type == "A";
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(ViewAuthUsers $viewAuthUsers, Vcard $vcard): bool
    {
        //

        // verifica se o usuário é administrador ou se o usuário é o dono do cartão
        return $viewAuthUsers->user_type == "A" || $viewAuthUsers->username == $vcard->phone_number;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(ViewAuthUsers $viewAuthUsers): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(ViewAuthUsers $viewAuthUsers, Vcard $vcard): bool
    {
        //

        // verifica se o usuário é administrador ou se o usuário é o dono do cartão
        return $viewAuthUsers->user_type == "A" || $viewAuthUsers->username == $vcard->phone_number;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(ViewAuthUsers $viewAuthUsers, Vcard $vcard): bool
    {
        // only admins can delete
        return $viewAuthUsers->user_type == "A" && $vcard->balance == 0;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(ViewAuthUsers $viewAuthUsers, Vcard $vcard): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(ViewAuthUsers $viewAuthUsers, Vcard $vcard): bool
    {
        // only admins can force delete
        return $viewAuthUsers->user_type == "A" && $vcard->balance == 0;
    }
}
