<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HousekeepingController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/accommodation', [HomeController::class, 'accommodation'])->name('accommodation');
Route::get('/accommodation/{slug}', [HomeController::class, 'roomDetail'])->name('accommodation.room');
Route::post('/send-message', [HomeController::class, 'sendMessage'])->name('send.message');
Route::view('/offers', 'offers')->name('offers');
Route::view('/gallery', 'gallery')->name('gallery');
Route::view('/dining', 'dining')->name('dining');
Route::view('/events', 'events')->name('events');

Route::prefix('employee')->name('employee.')->group(function () {
    Route::view('/', 'employee.employee')->name('index');
    Route::view('/dashboard', 'employee.dashboard')->name('dashboard');
    Route::view('/reservation', 'employee.reservation', ['reservations' => collect([]), 'rooms' => collect([])])->name('reservation');
    Route::view('/checkin', 'employee.checkin')->name('checkin');
    Route::view('/room-status', 'employee.room-status')->name('room-status');
    Route::view('/guest-requests', 'employee.guest-requests')->name('guest-requests');
    Route::view('/messages', 'employee.messages')->name('messages');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/rooms', [AdminController::class, 'rooms'])->name('rooms');
    Route::post('/rooms', [AdminController::class, 'storeRoom'])->name('rooms.store');
    Route::put('/rooms/{id}', [AdminController::class, 'updateRoom'])->name('rooms.update');
    Route::patch('/rooms/{id}/status', [AdminController::class, 'updateRoomStatus'])->name('rooms.status');
    Route::delete('/rooms/{id}', [AdminController::class, 'destroyRoom'])->name('rooms.destroy');
    Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
    Route::post('/reservations', [AdminController::class, 'storeReservation'])->name('reservations.store');
    Route::patch('/reservations/{id}/status', [AdminController::class, 'updateReservationStatus'])->name('reservations.status');
    Route::get('/guests', [AdminController::class, 'guests'])->name('guests');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::post('/messages/{id}/reply', [AdminController::class, 'replyMessage'])->name('messages.reply');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/export-csv', [AdminController::class, 'exportReportsCsv'])->name('reports.export.csv');
    Route::get('/reports/print', [AdminController::class, 'printReports'])->name('reports.print');
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
});

Route::prefix('housekeeping')->group(function(){

    Route::get('/dashboard',
    [HousekeepingController::class,'dashboard'])
    ->name('housekeeping.dashboard');


    Route::get('/assigned-rooms',
    [HousekeepingController::class,'assignedRooms'])
    ->name('housekeeping.assigned-rooms');


    Route::get('/room-status-update',
    [HousekeepingController::class,'roomStatusUpdate'])
    ->name('housekeeping.room-status-update');


    Route::get('/guest-requests',
    [HousekeepingController::class,'guestRequests'])
    ->name('housekeeping.guest-requests');


    Route::get('/maintenance-report',
    [HousekeepingController::class,'maintenanceReport'])
    ->name('housekeeping.maintenance-report');


    Route::get('/cleaning-history',
    [HousekeepingController::class,'cleaningHistory'])
    ->name('housekeeping.cleaning-history');

});

Route::get('/logout', function () {

    Auth::logout();

    request()->session()->invalidate();

    request()->session()->regenerateToken();

    return redirect('/');

})->name('logout');