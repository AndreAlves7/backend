<?php
use App\Http\Controllers\auth\AuthController;

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
Route::middleware('auth:api')->post(
    'logout',
    [AuthController::class, 'auth/logout']
);

// Route::apiResource('/vcard', 'App\Http\Controllers\VcardController');

Route::middleware('auth:api')->group(
    function () {
       Route::apiResource('/vcard', 'App\Http\Controllers\VcardController');

    }
);