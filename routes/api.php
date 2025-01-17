<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\CategoryController;
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
        Route::patch('/me', ['App\Http\Controllers\ViewAuthUserController', 'update']);
        Route::delete('/me', ['App\Http\Controllers\ViewAuthUserController', 'destroy']);

        // Route::post('me/confirmPassword', [ViewAuthUserController::class, 'confirmPassword']);

        //policies in the controller
        Route::apiResource('/vcard', 'App\Http\Controllers\VcardController');
        Route::get('/vcard/transaction/{userId}', ['App\Http\Controllers\VcardController', 'getTransactionsByVcard']);
        Route::get('/vcard/{vcard}/category', ['App\Http\Controllers\VcardController', 'getCategoriesByVcard']);

        Route::get('/vcard/statistics/{userId}/{userType}', ['App\Http\Controllers\VcardController', 'getDataForStatistics']);
        Route::get('/vcard/statistics/payment_types/{userId}/{userType}', ['App\Http\Controllers\VcardController', 'getTotalUsageOfPaymentMethod']);
        Route::get('/vcard/statistics/totals/{userId}/{userType}', ['App\Http\Controllers\VcardController', 'getTotalUsageAndMaxValue']);

        Route::apiResource('/user', 'App\Http\Controllers\UserController');
        Route::apiResource('/transaction', 'App\Http\Controllers\TransactionController');
        Route::apiResource('/category', 'App\Http\Controllers\CategoryController');
        Route::apiResource('/defaultcategory', 'App\Http\Controllers\DefaultCategoryController');
    }
);


// Route::middleware('auth:api')->post( 
//     'auth/logout',
//     [AuthController::class, 'logout']
// );

// Route::apiResource('/vcard', 'App\Http\Controllers\VcardController')