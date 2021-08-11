<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\API\AuthController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::group(['middleware'=>'auth:sanctum'], function(){
    /* User End Point */
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);

    /* Toko End Point */
    Route::get('toko', [TokoController::class, 'index']);
    Route::get('toko/{id}', [TokoController::class, 'show']);
    Route::post('toko', [TokoController::class, 'store']);
    Route::put('toko/{id}', [TokoController::class, 'update']);
    
    /* Produk End Point */
    Route::get('produk', [ProdukController::class, 'index']);
    Route::get('produk/{id}', [ProdukController::class, 'show']);
    Route::post('produk', [ProdukController::class, 'store']);
    Route::put('produk/{id}', [ProdukController::class, 'update']);

    /* Get produk suatu toko */
    Route::get('produk/toko/{id}', [ProdukController::class, 'show_produk_toko']);

});