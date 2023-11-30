<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\VcardController;
use App\Http\Controllers\ViewAuthUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('auth/login', [AuthController::class, 'login']);
Route::post('/signup', [ViewAuthUserController::class, 'register']);

Route::middleware('auth:api')->group(
    function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', ['App\Http\Controllers\ViewAuthUserController', 'show_me']);

        //policies in the controller
        Route::apiResource('/vcard', 'App\Http\Controllers\VcardController');
    }
);


// Route::middleware('auth:api')->post( 
//     'auth/logout',
//     [AuthController::class, 'logout']
// );

// Route::apiResource('/vcard', 'App\Http\Controllers\VcardController')