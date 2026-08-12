<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\ProfileController;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

// --- Admin Login (outside admin prefix so it's named 'login' not 'admin.login') ---
Route::get('/staff/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/staff/login', [AuthController::class, 'login'])->name('login.submit');

// --- Guest Login ---
Route::get('/guest/login', [AuthController::class, 'showGuestLoginForm'])->name('guest.login');
Route::post('/guest/login', [AuthController::class, 'guestLogin'])->name('guest.login.submit');
Route::post('/guest/register', [AuthController::class, 'guestRegister'])->name('guest.register.submit');

// --- Guest Profile ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::get('/profile/records', [HomeController::class, 'records'])->name('profile.records');
});

// --- Admin Logout ---
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// --- Employee Portal ---
Route::prefix('employee')->name('employee.')->group(function () {
    Route::redirect('/', '/employee/dashboard')->name('index');
    Route::view('/dashboard', 'employee.dashboard')->name('dashboard');
    Route::view('/reservation', 'employee.reservation', [
        'reservations' => collect([]),
        'rooms' => collect([]),
    ])->name('reservation');
    Route::view('/checkin', 'employee.checkin')->name('checkin');
    Route::view('/room-status', 'employee.room-status')->name('room-status');
    Route::get('/guest-requests', function () {
        if (!session()->has('employee_guest_requests')) {
            session()->put('employee_guest_requests', [
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
        }

        $requests = session('employee_guest_requests');

        return view('employee.guest-requests', compact('requests'));
    })->name('guest-requests');
    Route::post('/guest-requests/{id}/resolve', [
        AdminController::class,
        'resolveGuestRequest',
    ])->name('guest-requests.resolve');
    Route::get('/messages', function () {
        $messages = Message::latest()->get();

        return view('employee.messages', compact('messages'));
    })->name('messages');

    Route::post('/messages', [AdminController::class, 'storeEmployeeMessage'])->name('messages.store');
});

// --- Protected Admin Routes (requires authentication) ---
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/rooms', [AdminController::class, 'rooms'])->name('rooms');
    Route::post('/rooms', [AdminController::class, 'storeRoom'])->name('rooms.store');
    Route::put('/rooms/{id}', [AdminController::class, 'updateRoom'])->name('rooms.update');
    Route::patch('/rooms/{id}/status', [AdminController::class, 'updateRoomStatus'])->name('rooms.status');
    Route::match(['post', 'delete'], '/rooms/bulk-delete', [AdminController::class, 'bulkDestroyRooms'])->name('rooms.bulkDestroy');
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
    Route::post('/manage-account', [AdminController::class, 'storeAccount'])->name('manage-account.store');
    Route::put('/manage-account/{id}', [AdminController::class, 'updateAccountUser'])->name('manage-account.update');
    Route::patch('/manage-account/{id}/status', [AdminController::class, 'updateUserStatus'])->name('manage-account.status');
    Route::delete('/manage-account/{id}', [AdminController::class, 'destroyUser'])->name('manage-account.destroy');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings/account', [AdminController::class, 'updateAccount'])->name('settings.account');
});

// --- Housekeeping Portal ---
Route::prefix('housekeeping')->name('housekeeping.')->group(function () {
    Route::get('/dashboard', [HousekeepingController::class, 'dashboard'])->name('dashboard');
    Route::patch('/rooms/{id}/cleaning', [HousekeepingController::class, 'updateStatus'])->name('rooms.cleaning');
    Route::get('/assigned-rooms', [HousekeepingController::class, 'assignedRooms'])->name('assigned-rooms');
    Route::get('/room-status-update', [HousekeepingController::class, 'roomStatusUpdate'])->name('room-status-update');
    Route::get('/guest-requests', [HousekeepingController::class, 'guestRequests'])->name('guest-requests');
    Route::get('/maintenance-report', [HousekeepingController::class, 'maintenanceReport'])->name('maintenance-report');
    Route::get('/cleaning-history', [HousekeepingController::class, 'cleaningHistory'])->name('cleaning-history');
});

// --- Logout (fallback route) ---
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});


