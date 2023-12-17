<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\ViewAuthUsers;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(ViewAuthUsers $viewAuthUsers): bool
    {
        return $viewAuthUsers->user_type == "A";
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(ViewAuthUsers $viewAuthUsers, Category $category): bool
    {
        return $viewAuthUsers->user_type == "A" || $viewAuthUsers->username == $category->vcard;
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
    public function update(ViewAuthUsers $viewAuthUsers, Category $category): bool
    {
        return $viewAuthUsers->user_type == "A" || $viewAuthUsers->username == $category->vcard;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(ViewAuthUsers $viewAuthUsers, Category $category): bool
    {
        return $viewAuthUsers->username == $category->vcard;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(ViewAuthUsers $viewAuthUsers, Category $category): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(ViewAuthUsers $viewAuthUsers, Category $category): bool
    {
        //
    }

    public function viewVcardCategories(ViewAuthUsers $viewAuthUsers, string $vcard): bool
    {
        return $viewAuthUsers->user_type == "A" || $viewAuthUsers->username == $vcard;
    }
}
