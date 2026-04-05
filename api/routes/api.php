<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/payments', [\App\Http\Controllers\PaymentsController::class, 'createPayment']);
});

Route::post('/login', [LoginController::class, 'login'])->middleware('web');
