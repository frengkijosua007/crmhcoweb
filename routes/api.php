<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::middleware('api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');
