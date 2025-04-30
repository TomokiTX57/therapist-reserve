<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\GoogleController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::resource('schedules', ScheduleController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
    Route::get('schedules/export', [ScheduleController::class, 'export'])->name('schedules.export');
    Route::get('schedules/calendar', [ScheduleController::class, 'showCalendar'])->name('schedules.calendar');
    Route::get('schedules/show', [ScheduleController::class, 'showCalendar'])->name('schedules.show');
    Route::get('/login/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/google/callback', [GoogleController::class, 'handleGoogleCallback']);
    Route::middleware('auth')->get('/profile', function () {
        return view('profile');
    })->name('profile');
});




require __DIR__ . '/auth.php';
