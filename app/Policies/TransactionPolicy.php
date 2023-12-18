<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\ViewAuthUsers;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(ViewAuthUsers $viewAuthUsers): bool
    {
        $viewAuthUsers->user_type == "A";
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(ViewAuthUsers $viewAuthUsers, Transaction $transaction): bool
    {
        $viewAuthUsers->user_type == "A" || $viewAuthUsers->username == $transaction->vcard;
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
    public function update(ViewAuthUsers $viewAuthUsers, Transaction $transaction): bool
    {
        $viewAuthUsers->user_type == "A" || $viewAuthUsers->username == $transaction->vcard;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(ViewAuthUsers $viewAuthUsers, Transaction $transaction): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(ViewAuthUsers $viewAuthUsers, Transaction $transaction): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(ViewAuthUsers $viewAuthUsers, Transaction $transaction): bool
    {
        //
    }
}
