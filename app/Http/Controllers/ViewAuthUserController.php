<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVcardRequest;
use App\Http\Resources\VcardResource;
use App\Models\Vcard;
use Illuminate\Http\Request;

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
}
