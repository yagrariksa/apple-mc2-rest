<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


Route::middleware('auth:api')->group(function () {
    Route::post('user', [AuthController::class, 'edit'])->name('api.user.edit');
    Route::prefix('review')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('api.review.index');
        Route::post('/', [ReviewController::class, 'store'])->name('api.review.index');
        Route::get('/my', [ReviewController::class, 'my'])->name('api.review.my');
        Route::get('/{review}', [ReviewController::class, 'show'])->name('api.review.show');
    });

    Route::prefix('restaurant')->group(function () {
        Route::get('/', [RestaurantController::class, 'index'])->name('api.restaurant.index');
        Route::get('/{restaurant}', [RestaurantController::class, 'show'])->name('api.restaurant.show');
    });
    Route::get('test', [ReviewController::class, 'test']);
});
