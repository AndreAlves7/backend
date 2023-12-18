<?php

namespace App\Policies;

use App\Models\ViewAuthUsers;

use Illuminate\Support\Facades\Log;

class DefaultCategoryPolicy
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

    public function create(ViewAuthUsers $viewAuthUsers): bool
    {
        //
        // verifica se o usuário é administrador
        return $viewAuthUsers->user_type == "A";
    } 
    
    public function delete(ViewAuthUsers $viewAuthUsers): bool
    {
        //
        // verifica se o usuário é administrador
        return $viewAuthUsers->user_type == "A";
    }
}
