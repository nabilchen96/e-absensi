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
    

    //USER
    Route::get('/data-user', 'App\Http\Controllers\Api\UserController@index');

    //PROFILE
    Route::get('/data-profile', 'App\Http\Controllers\Api\ProfileController@index');
    Route::get('/data-my-profile', 'App\Http\Controllers\Api\ProfileController@myProfile');

    //SHIFT
    Route::get('/data-shift', 'App\Http\Controllers\Api\ShiftController@index');
    
    //SCHEDULE
    Route::get('/data-schedule', 'App\Http\Controllers\Api\ScheduleController@index');
    Route::post('/store-schedule', 'App\Http\Controllers\Api\ScheduleController@store');
    Route::post('/update-schedule', 'App\Http\Controllers\Api\ScheduleController@update');
    Route::post('/delete-schedule', 'App\Http\Controllers\Api\ScheduleController@delete');

    //LKH
    Route::get('/data-lkh', 'App\Http\Controllers\Api\LkhController@index');
    Route::post('/store-lkh', 'App\Http\Controllers\Api\LkhController@store');
    Route::post('/update-lkh', 'App\Http\Controllers\Api\LkhController@update');
    Route::post('/delete-lkh', 'App\Http\Controllers\Api\LkhController@delete');

    //PERIZINAN
    Route::get('/data-perizinan', 'App\Http\Controllers\Api\PerizinanController@index');
    Route::post('/store-perizinan', 'App\Http\Controllers\Api\PerizinanController@store');
    Route::post('/delete-perizinan', 'App\Http\Controllers\Api\PerizinanController@delete');
    Route::post('/update-perizinan', 'App\Http\Controllers\Api\PerizinanController@update');
    Route::post('/update-status-perizinan', 'App\Http\Controllers\Api\PerizinanController@updateStatus');

    //CUTI
    Route::get('/data-cuti', 'App\Http\Controllers\Api\CutiController@index');
    Route::post('/store-cuti', 'App\Http\Controllers\Api\CutiController@store');
    Route::post('/delete-cuti', 'App\Http\Controllers\Api\CutiController@delete');
    Route::post('/update-cuti', 'App\Http\Controllers\Api\CutiController@update');
    Route::post('/update-status-cuti', 'App\Http\Controllers\Api\CutiController@updateStatus');

    // PENGUMUMAN
    Route::get('/pengumuman', 'App\Http\Controllers\Api\PengumumanController@index');
    Route::get('/data-pengumuman', 'App\Http\Controllers\Api\PengumumanController@data');
    Route::post('/store-pengumuman', 'App\Http\Controllers\Api\PengumumanController@store');
    Route::post('/update-pengumuman', 'App\Http\Controllers\Api\PengumumanController@update');
    Route::post('/delete-pengumuman', 'App\Http\Controllers\Api\PengumumanController@delete');

    // ABSENSI
    Route::get('/data-absensi', 'App\Http\Controllers\Api\AbsensiController@index');
    Route::post('/store-absensi', 'App\Http\Controllers\Api\AbsensiController@store');

    //LAPORAN ABSENSI
    Route::get('/data-laporan', 'App\Http\Controllers\Api\LaporanController@index');

    //LOKASI KEERJA USER
    Route::get('/data-lokasi-kerja-user', 'App\Http\Controllers\Api\LokasiKerjaUserController@index');

});

Route::post('/logout', 'App\Http\Controllers\Api\AuthController@logout');
Route::post('/logout-all', 'App\Http\Controllers\Api\AuthController@logoutAll')->middleware('auth:sanctum');
