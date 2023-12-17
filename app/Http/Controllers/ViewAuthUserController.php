<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVcardRequest;
use App\Http\Resources\VcardResource;
use App\Models\Vcard;
use App\Models\Category;
use App\Models\DefaultCategory;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Base64Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Support\Facades\File;
class ViewAuthUserController extends Controller
{
    private function storeBase64AsFile(Vcard $user, String $base64String, ?String $photoToDelete)
    {
        $targetDir = storage_path('app/public/fotos');
        $newfilename = $user->id . "_" . rand(1000,9999);
        $base64Service = new Base64Services();
        return $base64Service->saveFile($base64String, $targetDir, $newfilename, $photoToDelete);
    }

    public function show_me(Request $request)
    {
        return response()->json($request->user());
    }

    public function register(StoreVcardRequest $request)
    {
        $allDefaultcategories = DefaultCategory::all();
        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("photo_url", $dataToSave) ?
            $dataToSave["photo_url"] : ($dataToSave["photo_url"] ?? null);
        unset($dataToSave["photo_url"]);

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
            $vcard->photo_url = $this->storeBase64AsFile($vcard, $base64ImagePhoto, null);
        }

        try {
            $vcard->save();

              // Create entries in the categories table for each default category
            foreach ($allDefaultcategories as $defaultCategory) {
                $category = new Category();
                $category->vcard = $vcard->phone_number;
                $category->type = $defaultCategory->type;
                $category->name = $defaultCategory->name;
                $category->save();
            }
            return new VcardResource($vcard);

        } catch (\Exception $e) {
            Log::info($e);
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
        'password' => 'sometimes|string|min:3|nullable',
        'profilePhoto' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
    ]);

    // Find the user by phone_number

    if($request->user()->user_type === 'A'){
        $user = User::where('id', $id)->firstOrFail();
    }else{
        $user = Vcard::where('phone_number', $id)->firstOrFail();
    }

    //Verify Password if necessary
    if ($request->password != null || $request->confirmation_code != null) {
        $this->authorize('confirmPassword', [$user, $request]);
    }


    // Get the non-null fields from the request
    $updateData = array_filter($request->all(), function ($value) {
        return $value !== null;
    });

    // Update user fields based on non-null request data
    if ($request->has('password') && $request->password !== null) {
        $user->password = bcrypt($request->password);
    }

    // Bcrypt for confirmation_code (if it exists and is not null in the request)
    if ($request->has('confirmation_code') && $request->confirmation_code !== null) {
        $user->confirmation_code = bcrypt($request->confirmation_code);
    }

     // Create a new photo file from base64 content
     if ($request->user()->user_type !== 'A' && $request->photo_url) {
        $user->photo_url = $this->storeBase64AsFile($user, $request->photo_url, $user->photo_url);    
        }
    // Save the changes
    $user->save();

    return new VcardResource($user);
    }

public function destroy(Request $request)
    {
        $payload = $request->input();
        $id = $payload['phone_number'];
        $userType = $payload['user_type'];


        if($userType === 'A'){
            $user = User::where('id', $id)->firstOrFail();
        }else{
            $user = Vcard::where('phone_number', $id)->firstOrFail();
        }

        $this->authorize('deleteSelf', [$user, $request]);
        
        if ($user->balance > 0) {
            return response()->json(['error' => 'Vcard has balance greater than 0'], 403);
        }

        if ($user->transactions()->count() > 0) {
            // soft delete all vcard transactions
            $user->softDeleteTransactions();
            // soft delete vcard
            $user->deleted_at = now();
            $user->save();

            Log::info("Soft delete");
        } else {
            // hard delete
            $user->delete();
            Log::info("Hard delete");
        }

        return new VcardResource($user);
    }
}
