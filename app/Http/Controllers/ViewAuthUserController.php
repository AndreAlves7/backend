<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVcardRequest;
use App\Http\Resources\VcardResource;
use App\Models\Vcard;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ViewAuthUserController extends Controller
{
    public function show_me(Request $request)
    {
        return response()->json($request->user());
    }

    public function register(StoreVcardRequest $request)
    {
        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        unset($dataToSave["base64ImagePhoto"]);

        $vcard = new Vcard();
        $vcard->phone_number = $dataToSave['phone_number'];
        $vcard->name = $dataToSave['name'];
        $vcard->email = $dataToSave['email'];
        $vcard->password = bcrypt($dataToSave['password']);
        $vcard->confirmation_code = bcrypt($dataToSave['confirmation_code']);
        $vcard->photo_url = $base64ImagePhoto;
        $vcard->blocked = 0;

        // Create a new photo file from base64 content
        if ($base64ImagePhoto) {
            $vcard->photo_url = $this->storeBase64AsFile($vcard, $base64ImagePhoto);
        }

        try {
            $vcard->save();
            return new VcardResource($vcard);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error creating user'], 500);
        }
    }

    public function update(Request $request)
{
    $id = $request->user()->id;

    // Validate the request data
    $request->validate([
        'name' => 'sometimes|string|max:255|nullable',
        'email' => 'sometimes|email|max:255|nullable',
        'confirmation_code' => 'sometimes|string|min:3|nullable',
        'password' => 'sometimes|string|min:6|nullable',
        'profilePhoto' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
    ]);

    // Find the user by phone_number

    if($request->user()->user_type === 'A'){
        $user = User::where('id', $id)->firstOrFail();
    }else{
        $user = Vcard::where('phone_number', $id)->firstOrFail();
    }



    // Get the non-null fields from the request
    $updateData = array_filter($request->all(), function ($value) {
        return $value !== null;
    });

    // Update user fields based on non-null request data
    $user->update($updateData);

    if ($request->has('password') && $request->password !== null) {
        $user->password = bcrypt($request->password);
    }

    // Bcrypt for confirmation_code (if it exists and is not null in the request)
    if ($request->has('confirmation_code') && $request->confirmation_code !== null) {
        $user->confirmation_code = bcrypt($request->confirmation_code);
    }

    // Save the changes
    $user->save();

    return new VcardResource($user);
    }

    public function confirmPassword(Request $request)
    {
        $id = $request->user()->id;

        if($request->user()->user_type === 'A'){
            $user = User::where('id', $id)->firstOrFail();
        }else{
            $user = Vcard::where('phone_number', $id)->firstOrFail();
        }

        Log::info('Confirm Password Request', ['user_password' => $user->name]);

        Log::info('Confirm Password Request', ['ddsasdadsa' => $request]);

        if (!$user) {
            return response()->json(['msg' => 'User not authenticated'], 401);
        }

        // Validate the request data (you might want to add more validation rules)
        $request->validate([
            'password' => 'required|string',
        ]);

        // Check if the provided password matches the user's actual password
        if (Hash::check($request->password, $user->password)) {
            return response()->json(['msg' => 'Password confirmed'], 200);
        } else {
            return response()->json(['msg' => 'Incorrect password'], 401);
        }
    }
}
