<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Vcard;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $passportData = [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PASSWORD_GRANT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_GRANT_SECRET'),
            'username' => $request->username,
            'password' => $request->password,
            'scope'         => '',
        ];

        // check if vcard is soft delete or blocked
        //log
        Log::info($request->username);
        if ($request->username) {
            $vcard = Vcard::where('phone_number', $request->username)->first();

            //log vcard
            Log::info($vcard);

            if ($vcard == NULL) {
                return response()->json(['error' => 'User not found'], 404);
            }else{
                if ($vcard->blocked == 1) {
                    return response()->json(['error' => 'User blocked'], 403);
                }
            }
        }

        request()->request->add($passportData);

        $request = Request::create(env('PASSPORT_URL') . '/oauth/token', 'POST');
        $response = Route::dispatch($request);
        $errorCode = $response->getStatusCode();

        if ($errorCode == '200') {
            return json_decode((string) $response->content(), true);
        } else {
            return response()->json(
                ['msg' => 'User credentials are invalid'],
                $errorCode
            );
        }
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();
        $token->delete();
        return response(['msg' => 'Token revoked'], 200);
    }
}
