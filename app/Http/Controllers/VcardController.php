<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vcard;
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
        //
        $this->authorize('viewAny', Vcard::class);


        return VcardResource::collection(Vcard::all());
        // return Vcard::all();
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
        return new VcardResource($vcard);
    }
/**
 * Update the specified resource in storage.
 */

// public function update($id, Request $request)
// {
//     // Validate the request data
//     $request->validate([
//         'name' => 'sometimes|string|max:255|nullable',
//         'email' => 'sometimes|email|max:255|nullable',
//         'confirmation_code' => 'sometimes|string|min:3|nullable',
//         'password' => 'sometimes|string|min:6|nullable',
//         'profilePhoto' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
//     ]);

//     // Find the user by phone_number
//     $user = Vcard::where('phone_number', $id)->firstOrFail();

//     // Get the non-null fields from the request
//     $updateData = array_filter($request->all(), function ($value) {
//         return $value !== null;
//     });

//     // Update user fields based on non-null request data
//     $user->update($updateData);

//     if ($request->has('password') && $request->password !== null) {
//         $user->password = bcrypt($request->password);
//         // $vcard->update($request->validated()); -> não funciona
//         $vcard->fill($request->validated());
//         $vcard->max_debit = $request->max_debit;
//         $vcard->blocked = $request->blocked;
//         $vcard->save();
//         return new VcardResource($vcard);
//     }

//     // Bcrypt for confirmation_code (if it exists and is not null in the request)
//     if ($request->has('confirmation_code') && $request->confirmation_code !== null) {
//         $user->confirmation_code = bcrypt($request->confirmation_code);
//     }

//     // Save the changes
//     $user->save();

//     return new VcardResource($user);
// }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vcard $vcard)
    {
        //
    }

}
