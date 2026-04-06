<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/payments', [\App\Http\Controllers\PaymentsController::class, 'getPayments']);
    Route::post('/payments', [\App\Http\Controllers\PaymentsController::class, 'createPayment']);
    Route::delete('/payments/{id}', [\App\Http\Controllers\PaymentsController::class, 'deletePayment'])
        ->whereNumber('id');
});

Route::post('/logout', [\App\Http\Controllers\LogoutController::class, 'logout'])->middleware('auth:web');
Route::post('/login', [LoginController::class, 'login'])->middleware('web');
