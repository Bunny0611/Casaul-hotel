<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HousekeepingController;

// --- Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/accommodation', [HomeController::class, 'accommodation'])->name('accommodation');
Route::get('/accommodation/{slug}', [HomeController::class, 'roomDetail'])->name('accommodation.room');
Route::post('/send-message', [HomeController::class, 'sendMessage'])->name('send.message');
Route::view('/offers', 'offers')->name('offers');
Route::view('/gallery', 'gallery')->name('gallery');
Route::view('/dining', 'dining')->name('dining');
Route::view('/events', 'events')->name('events');

// --- Admin Login (outside admin prefix so it's named 'login' not 'admin.login') ---
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// --- Protected Admin Routes (requires authentication) ---
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
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
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings/account', [AdminController::class, 'updateAccount'])->name('settings.account');
});

// --- Housekeeping Portal (requires auth + housekeeping role) ---
Route::prefix('housekeeping')->name('housekeeping.')->middleware(['auth', 'role:housekeeping'])->group(function () {
    Route::get('/', [HousekeepingController::class, 'dashboard'])->name('dashboard');
    Route::patch('/rooms/{id}/cleaning', [HousekeepingController::class, 'updateStatus'])->name('rooms.cleaning');
});
