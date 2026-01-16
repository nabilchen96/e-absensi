<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// LOGIN
Route::post('/login', 'App\Http\Controllers\Api\AuthController@login');

Route::middleware('auth:sanctum')->group(function () {
    
    //SHIFT
    Route::get('/data-shift', 'App\Http\Controllers\Api\ShiftController@index');
    
    //SCHEDULE
    Route::get('/data-schedule', 'App\Http\Controllers\Api\ScheduleController@index');

    
    Route::post('/logout', 'App\Http\Controllers\Api\AuthController@logout');
});
