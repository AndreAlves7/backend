<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vcard;
use App\Models\Transaction;
use App\Http\Resources\VcardResource;

use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreVcardRequest;
use App\Http\Requests\UpdateVcardRequest;

class VcardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Vcard::class);
        return VcardResource::collection(Vcard::all()->where('deleted_at', NULL));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreVcardRequest $request)
    {
        //
        // $vcard = new Vcard();
        // $vcard->phone_number = $request->phone_number;
        // $vcard->name = $request->name;
        // $vcard->photo_url = $request->photo_url;
        // $vcard->balance = $request->balance;
        // $vcard->max_debit = $request->max_debit;
        error_log($request);
        // ($request);

        //validar com FormRequest
        $vcard = new Vcard();
        $vcard->fill($request->validated());
        $vcard->password = bcrypt($request->password);
        $vcard->blocked = FALSE;
        $vcard->balance = 0;
        $vcard->save();

        return new VcardResource($vcard);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vcard $vcard)
    {
        //show a single vcard
        $this->authorize('view', $vcard);
        if ($vcard->deleted_at == NULL) {
            return new VcardResource($vcard);
        } else {
            return response()->json(['error' => 'Vcard not found'], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVcardRequest $request, Vcard $vcard)
    {
        $this->authorize('update', $vcard);
        // $vcard->update($request->validated()); -> não funciona
        $vcard->fill($request->validated());
        $vcard->max_debit = $request->max_debit;
        $vcard->blocked = $request->blocked;
        $vcard->save();
        return new VcardResource($vcard);
    }

       /**
     * Display a listing of the resources transactions.
     */
    public function getTransactionsByVcard($userId)
    {
        \Log::info($userId);
        // Example: Assuming Transaction is the model for your transactions
        $transactions = Transaction::where('vcard', $userId)->get();
        
        // Convert the data to an array
        $responseData = ['transactions' => $transactions->toArray()];
    
        // Return a JSON response
        return response()->json($responseData);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vcard $vcard)
    {
        //only admins can delete
        $this->authorize('delete', $vcard);

        if ($vcard->balance > 0) {
            return response()->json(['error' => 'Vcard has balance greater than 0'], 403);
        }

        if ($vcard->transactions()->count() > 0) {
            // soft delete all vcard transactions
            $vcard->softDeleteTransactions();
            // soft delete vcard
            $vcard->deleted_at = now();
            $vcard->save();
        } else {
            // hard delete
            $vcard->delete();
        }

        return new VcardResource($vcard);
    }
}
