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

        request()->request->add($passportData);

        $request = Request::create(env('PASSPORT_URL') . '/oauth/token', 'POST');
        $response = Route::dispatch($request);
        $errorCode = $response->getStatusCode();

        if (
            $errorCode == '200'
        ) {
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

    public function confirmPassword(Request $request)
    {

        $user = Vcard::where('phone_number', $request->phone_number)->firstOrFail();
        Log::info('Confirm Password Request', ['user_password' => $user->name]);

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