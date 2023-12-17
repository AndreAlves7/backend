<?php

namespace App\Policies;

use App\Models\Vcard;
use App\Models\ViewAuthUsers;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;


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

    public function deleteSelf(ViewAuthUsers $viewAuthUsers, Vcard $model, Request $request): bool
    {
        $confirmation_password = $request['confirmation_password'];
        $confirmation_pin = $request['confirmation_pin'];

        if ($viewAuthUsers->user_type === "A") {
            throw new AuthorizationException('Admins cannot delete themselves');
        }

        if (intval($model->balance) != 0) {
            throw new AuthorizationException('Balance must be 0 to delete');
        }

        if (!Hash::check($confirmation_pin, $viewAuthUsers->confirmation_code)) {
            throw new AuthorizationException('Pin provided does not match');
        }

        if (!Hash::check($confirmation_password, $viewAuthUsers->password)) {
            throw new AuthorizationException('Password provided does not match');
        }

        return true;
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

    public function confirmPassword(ViewAuthUsers $viewAuthUsers, Vcard $model, Request $request): bool
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

    public function viewVcardCategories(ViewAuthUsers $viewAuthUsers, Vcard $vcard): bool
    {
        return $viewAuthUsers->user_type == "A" || $viewAuthUsers->username == $vcard->phone_number;
    }
}
