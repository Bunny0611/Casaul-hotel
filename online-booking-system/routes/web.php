<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HousekeepingController;

// --- Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/accommodation', [HomeController::class, 'accommodation'])->name('accommodation');
Route::get('/accommodation/{slug}', [HomeController::class, 'roomDetail'])->name('accommodation.room');
Route::get('/reservation', [HomeController::class, 'reservation'])->name('reservation');
Route::post('/reservation', [HomeController::class, 'storeReservation'])->name('reservation.store');
Route::post('/send-message', [HomeController::class, 'sendMessage'])->name('send.message');
Route::view('/offers', 'offers')->name('offers');
Route::view('/gallery', 'gallery')->name('gallery');
Route::view('/dining', 'dining')->name('dining');
Route::view('/events', 'events')->name('events');
Route::view('/aboutus', 'aboutus')->name('aboutus');

// --- Staff Login ---
Route::get('/staff/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/staff/login', [AuthController::class, 'login'])->name('login.submit');

// --- Guest Login ---
Route::get('/guest/login', [AuthController::class, 'showGuestLoginForm'])->name('guest.login');
Route::post('/guest/login', [AuthController::class, 'guestLogin'])->name('guest.login.submit');
Route::post('/guest/register', [AuthController::class, 'guestRegister'])->name('guest.register.submit');

// --- Admin Logout ---
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// --- Employee Portal ---
Route::prefix('employee')->name('employee.')->middleware('auth')->group(function () {
    Route::view('/', 'employee.employee')->name('index');
    Route::view('/dashboard', 'employee.dashboard')->name('dashboard');
    Route::view('/reservation', 'employee.reservation', [
        'reservations' => collect([]),
        'rooms' => collect([])
    ])->name('reservation');
    Route::view('/checkin', 'employee.checkin')->name('checkin');
    Route::view('/room-status', 'employee.room-status')->name('room-status');
    Route::get('/guest-requests', function () {
        $requests = session('employee_guest_requests', [
            [
                'id' => 1,
                'title' => 'Room 305 - Extra Towels',
                'requested_at' => '10:15 AM',
                'status' => 'Pending',
            ],
            [
                'id' => 2,
                'title' => 'Room 208 - Late Checkout',
                'requested_at' => '9:40 AM',
                'status' => 'Approved',
            ],
            [
                'id' => 3,
                'title' => 'Room 101 - Wake-up Call',
                'requested_at' => '7:30 AM',
                'status' => 'Done',
            ],
        ]);

        return view('employee.guest-requests', compact('requests'));
    })->name('guest-requests');
    Route::post('/guest-requests/{id}/resolve', [
        \App\Http\Controllers\AdminController::class,
        'resolveGuestRequest'
    ])->name('guest-requests.resolve');
    Route::get('/messages', function () {
        $messages = \App\Models\Message::latest()->get();
        return view('employee.messages', compact('messages'));
    })->name('messages');
    Route::post('/messages', [\App\Http\Controllers\AdminController::class, 'storeEmployeeMessage'])->name('messages.store');
});

// --- Admin Portal ---
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
    Route::get('/reports/export-csv', [AdminController::class, 'exportReportsCsv'])->name('reports.export.csv');
    Route::get('/reports/print', [AdminController::class, 'printReports'])->name('reports.print');

    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');

    Route::get('/manage-account', [AdminController::class, 'manageAccount'])->name('manage-account');

    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings/account', [AdminController::class, 'updateAccount'])->name('settings.account');
});

// --- Housekeeping Portal ---
Route::prefix('housekeeping')->name('housekeeping.')->middleware('auth')->group(function () {

    Route::get('/dashboard', [HousekeepingController::class, 'dashboard'])->name('dashboard');

    Route::patch('/rooms/{id}/cleaning', [HousekeepingController::class, 'updateStatus'])
        ->name('rooms.cleaning');

    Route::get('/assigned-rooms', [HousekeepingController::class, 'assignedRooms'])->name('assigned-rooms');
    Route::get('/room-status-update', [HousekeepingController::class, 'roomStatusUpdate'])->name('room-status-update');
    Route::get('/guest-requests', [HousekeepingController::class, 'guestRequests'])->name('guest-requests');
    Route::get('/maintenance-report', [HousekeepingController::class, 'maintenanceReport'])->name('maintenance-report');
    Route::get('/cleaning-history', [HousekeepingController::class, 'cleaningHistory'])->name('cleaning-history');
});

// --- Logout ---
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');