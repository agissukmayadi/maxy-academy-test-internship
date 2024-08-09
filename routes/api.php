<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::group(['middleware' => 'jwt'], function () {
    Route::post('/topup', [TopUpController::class, 'store']);
    Route::post('/pay', [PaymentController::class, 'store']);
    Route::post('/transfer', [TransferController::class, 'store']);
    Route::get('/transactions', [TransactionController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::delete('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/user', function (Request $request) {
        $user = Auth::guard('api')->user();
        return response()->json([
            "user" => $user
        ]);
    })->middleware('jwt');

});