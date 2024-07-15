<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyMail;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardHistoryController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\home;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\VerificationController;


use Illuminate\Http\Request;
use App\Models\LokasiMonitoring;
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

Route::get('/', [HomeController::class, 'index']);

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'index')->name('login')->middleware('guest');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'create')->name('register')->middleware('guest');
    Route::post('/register', 'store');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Tautan verifikasi telah dikirim!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Tautan verifikasi telah dikirim!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::resource('/dashboard/controls', DashboardHistoryController::class)->middleware('auth');
Route::get('/dashboard/cetak', [DashboardHistoryController::class, 'cetak'])->name('dashboard.cetak');

// Tambahkan rute untuk metode cetak
Route::get('/dashboard/filter', [DashboardHistoryController::class, 'index'])->name('dashboard.filter');
Route::get('/dashboard/print', [DashboardHistoryController::class, 'print'])->name('dashboard.print');
Route::get('/dashboard/histories', [DashboardHistoryController::class, 'index'])->name('dashboard.histories');
Route::get('/dashboard/sensordata', [DashboardHistoryController::class, 'index'])->name('dashboard.sensordata');
Route::get('/dashboard/filter', [DashboardHistoryController::class, 'index'])->name('dashboard.filter');
Route::get('/sensor-data', [SensorDataController::class, 'getData'])->name('sensor.data');
Route::get('/dashboard/data', [SensorDataController::class, 'getData'])->name('dashboard.data');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [SensorDataController::class, 'index'])->name('dashboard');
    Route::get('/bacavoltage', [SensorDataController::class, 'bacavoltage'])->name('bacavoltage');
    Route::get('/bacapower', [SensorDataController::class, 'bacapower'])->name('bacapower');
    Route::get('/bacapowerfactor', [SensorDataController::class, 'bacapowerfactor'])->name('bacapowerfactor');
    Route::get('/bacaenergy', [SensorDataController::class, 'bacaenergy'])->name('bacaenergy');
    Route::get('/bacacurrent', [SensorDataController::class, 'bacacurrent'])->name('bacacurrent');
    Route::get('/bacabiaya', [SensorDataController::class, 'bacabiaya'])->name('bacabiaya');
    Route::get('/get-blynk-token', [SensorDataController::class, 'getBlynkToken'])->name('getBlynkToken');    
    Route::post('/updateSwitch1', [SensorDataController::class, 'updateSwitch1'])->name('updateSwitch1');
    Route::post('/updateSwitch2', [SensorDataController::class, 'updateSwitch2'])->name('updateSwitch2');
    Route::post('/update-blynk-data', [SensorDataController::class, 'updateData'])->name('updateBlynkData');
    Route::post('/update-blynk-token', [SensorDataController::class, 'updateBlynkToken'])->name('updateBlynkToken');
    Route::post('/get-lokasi-info', [SensorDataController::class, 'getLokasiInfo'])->name('getLokasiInfo');
    Route::get('/get-data', [SensorDataController::class, 'getData'])->name('sensor.data');
    //Route::get('/calculateDailyCost', [SensorDataController::class, 'calculateDailyCost']);
    Route::post('/calculate-daily-cost', [SensorDataController::class, 'calculateDailyCost'])->name('calculate.daily.cost');

});

Route::middleware(['auth'])->group(function () {
    Route::resource('lokasi', LokasiController::class);
    Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index'); // Menampilkan daftar lokasi
    Route::get('/lokasi/create', [LokasiController::class, 'create'])->name('lokasi.create');
    Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
    Route::get('/lokasi/{id}', [LokasiController::class, 'show'])->name('lokasi.show'); // Menampilkan detail lokasi
    Route::get('/lokasi/{id}/edit', [LokasiController::class, 'edit'])->name('lokasi.edit'); // Menampilkan form edit lokasi
    Route::put('/lokasi/{id}', [LokasiController::class, 'update'])->name('lokasi.update'); // Menyimpan perubahan lokasi
    Route::delete('/lokasi/{id}', [LokasiController::class, 'destroy'])->name('lokasi.destroy'); // Menghapus lokasi
    Route::get('/get-lokasi-by-user', [LokasiController::class, 'getLokasiByUser'])->middleware('auth');

    Route::get('/lokasi/modal/{id}/detail', [LokasiController::class, 'show'])->name('lokasi.modal.detail');
    Route::get('/lokasi/modal/{id}/edit', [LokasiController::class, 'edit'])->name('lokasi.modal.edit');
    Route::get('/lokasi/modal/{id}/delete', [LokasiController::class, 'destroy'])->name('lokasi.modal.delete');
});

// Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/users', [AdminController::class, 'searchUsers'])->name('searchUsers');
    Route::get('/dashboard/users/{userId}/lokasiMonitoring', [AdminController::class, 'listLokasi'])->name('listLokasi');
    Route::post('/dashboard/lokasiMonitoring/{lokasiId}/update-token', [AdminController::class, 'updateBlynkToken'])->name('updateBlynkToken');
    Route::get('/admin/users/{user}/lokasiMonitoring/search', [AdminController::class, 'searchLokasi'])->name('searchLokasi');
    Route::resource('users', AdminController::class);
    Route::post('users/{user}/toggle-status', [AdminController::class, 'toggleStatus'])->name('users.toggleStatus');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login'); // Form login
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store'); // Proses login
});

Route::get('/dashboard/sensor_data', [SensorDataController::class, 'getData'])->name('sensor.data');
// Route untuk menyimpan nilai sensor ke database
Route::get('/simpan/{voltage}/{power}/{power_factor}/{energy}/{current}/{biaya}', [SensorDataController::class, 'simpan']);

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

