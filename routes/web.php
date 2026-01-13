<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\SyncUsersJob;
use App\Jobs\SyncUnitKerjaJob;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', 'App\Http\Controllers\AuthController@login')->name('login');
Route::get('/login', 'App\Http\Controllers\AuthController@login')->name('login');
Route::post('/loginProses', 'App\Http\Controllers\AuthController@loginProses');

Route::get('/absen-tanpa-login', function(){
    return view('frontend.absensi');
});

Route::post('/store-absen-tanpa-login', 'App\Http\Controllers\AbsensiController@storeAbsenTanpaLogin');

//BACKEND
Route::group(['middleware' => 'auth'], function () {


    //DASHBOARD
    Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index');
    Route::get('/grafik-absensi', 'App\Http\Controllers\DashboardController@grafikAbsensi');
    Route::get('/grafik-izin-cuti', 'App\Http\Controllers\DashboardController@grafikIzinCuti');

    //USER
    Route::get('/user', 'App\Http\Controllers\UserController@index');
    Route::get('/data-user', 'App\Http\Controllers\UserController@data');
    Route::post('/store-user', 'App\Http\Controllers\UserController@store');
    Route::post('/update-user', 'App\Http\Controllers\UserController@update');
    Route::post('/delete-user', 'App\Http\Controllers\UserController@delete');

    //PEGAWAI
    Route::get('/daftar-pegawai', 'App\Http\Controllers\PegawaiController@index');
    Route::get('/data-pegawai', 'App\Http\Controllers\PegawaiController@data');

    //DETAIL USER
    Route::get('/detail-user', 'App\Http\Controllers\DetailUserController@index');
    Route::post('/store-detail-user', 'App\Http\Controllers\DetailUserController@store');

    // SHIFT CRUD
    Route::get('/shift', 'App\Http\Controllers\ShiftController@index');
    Route::get('/data-shift', 'App\Http\Controllers\ShiftController@data');
    Route::post('/store-shift', 'App\Http\Controllers\ShiftController@store');
    Route::post('/update-shift', 'App\Http\Controllers\ShiftController@update');
    Route::post('/delete-shift', 'App\Http\Controllers\ShiftController@delete');

    //LOKASI KERJA
    Route::get('/lokasi-kerja', 'App\Http\Controllers\LokasiKerjaController@index');
    Route::get('/data-lokasi-kerja', 'App\Http\Controllers\LokasiKerjaController@data');
    Route::post('/store-lokasi-kerja', 'App\Http\Controllers\LokasiKerjaController@store');
    Route::post('/update-lokasi-kerja', 'App\Http\Controllers\LokasiKerjaController@update');
    Route::post('/delete-lokasi-kerja', 'App\Http\Controllers\LokasiKerjaController@delete');

    // SHCEDULE
    Route::get('/schedule', 'App\Http\Controllers\ScheduleController@index');
    Route::get('/data-schedule', 'App\Http\Controllers\ScheduleController@data');
    Route::post('/store-schedule', 'App\Http\Controllers\ScheduleController@store');
    Route::post('/update-schedule', 'App\Http\Controllers\ScheduleController@update');
    Route::post('/delete-schedule', 'App\Http\Controllers\ScheduleController@delete');
    Route::get('/list-user', 'App\Http\Controllers\ScheduleController@listUser');
    Route::get('/list-shift', 'App\Http\Controllers\ScheduleController@listShift');

    // SHIFT CRUD
    Route::get('/pengumuman', 'App\Http\Controllers\PengumumanController@index');
    Route::get('/data-pengumuman', 'App\Http\Controllers\PengumumanController@data');
    Route::post('/store-pengumuman', 'App\Http\Controllers\PengumumanController@store');
    Route::post('/update-pengumuman', 'App\Http\Controllers\PengumumanController@update');
    Route::post('/delete-pengumuman', 'App\Http\Controllers\PengumumanController@delete');

    // ABSENSI
    Route::get('/list-absensi', 'App\Http\Controllers\AbsensiController@index');
    Route::get('/data-absensi', 'App\Http\Controllers\AbsensiController@getData');
    Route::post('/store-absensi', 'App\Http\Controllers\AbsensiController@store');
    Route::post('/delete-absensi', 'App\Http\Controllers\AbsensiController@delete');


    // PERIZINAN
    Route::get('/perizinan', 'App\Http\Controllers\PerizinanController@index');
    Route::get('/data-perizinan', 'App\Http\Controllers\PerizinanController@data');
    Route::post('/store-perizinan', 'App\Http\Controllers\PerizinanController@store');
    Route::post('/delete-perizinan', 'App\Http\Controllers\PerizinanController@delete');

    // CUTI
    Route::get('/cuti', 'App\Http\Controllers\CutiController@index');
    Route::get('/data-cuti', 'App\Http\Controllers\CutiController@data');
    Route::post('/store-cuti', 'App\Http\Controllers\CutiController@store');
    Route::post('/delete-cuti', 'App\Http\Controllers\CutiController@delete');

    // LAPORAN SHIFT CONTROLLER
    Route::get('/laporan-shift', 'App\Http\Controllers\LaporanShiftController@index');
    Route::get('/data-laporan-shift', 'App\Http\Controllers\LaporanShiftController@data');

    //SINKRONISASI DATA USER
    Route::get('/sync-users', function () {
        SyncUsersJob::dispatch();
        return response()->json(['message' => 'Sinkronisasi sedang berjalan di background.']);
    });

    //SINKRONISASI DATA LOKASI KERJA
    Route::get('/sync-lokasi-kerja', function () {
        SyncUnitKerjaJob::dispatch();
        return response()->json(['message' => 'Sinkronisasi sedang berjalan di background.']);
    });
});

//LOGOUT
Route::get('/logout', function () {
    Auth::logout();
    return redirect('login');
})->name('logout');







