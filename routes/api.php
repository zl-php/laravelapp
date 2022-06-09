<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
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


Route::get('wx', [TestController::class, 'test']);
Route::get('test', [UserController::class, 'test']);
Route::get('encrypt', [UserController::class, 'encrypt']);
Route::get('decrypt', [UserController::class, 'decrypt']);
Route::get('genSign', [UserController::class, 'genSign']);
Route::get('verifySign', [UserController::class, 'verifySign']);


// 登录中间件
Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('test1', [UserController::class, 'test1']);


});