<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\ProfileController;
use App\Models\Message;
use App\Models\InventoryItem;
use App\Models\Reservation;
use App\Models\RoomReservation;
use App\Models\Room;
use App\Models\DiningTable;
use App\Models\DiningSchedule;
use App\Models\DiningMenu;
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
Route::middleware(['auth:guest', 'role:guest'])->group(function () {
    Route::get('/guest/profile', [HomeController::class, 'profile'])->name('guest.profile');
    Route::get('/guest/records', [HomeController::class, 'records'])->name('guest.records');
    Route::get('/guest/receipts', [HomeController::class, 'receipts'])->name('guest.receipts');
    Route::delete('/guest/reservations/{reservation}', [HomeController::class, 'deleteReservation'])->name('guest.reservations.delete');
    Route::patch('/guest/reservations/{reservation}/cancel', [HomeController::class, 'cancelReservation'])->name('guest.reservations.cancel');
    Route::post('/guest/requests', [HomeController::class, 'storeGuestRequest'])->name('guest.requests.store');
});

// --- Admin Logout ---
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// --- Employee Portal ---
Route::prefix('employee')->name('employee.')->middleware(['auth', 'role:employee'])->group(function () {
    Route::redirect('/', '/employee/dashboard')->name('index');
    Route::get('/dashboard', [AdminController::class, 'employeeDashboard'])->name('dashboard');
    Route::get('/reservation', [AdminController::class, 'reservations'])->name('reservation');
    Route::post('/reservations', [AdminController::class, 'storeReservation'])->name('reservations.store');
    Route::put('/reservations/{id}', [AdminController::class, 'updateReservation'])->name('reservations.update');
    Route::patch('/reservations/{id}/status', [AdminController::class, 'updateReservationStatus'])->name('reservations.status');
    Route::post('/reservations/{id}/payments', [AdminController::class, 'storePayment'])->name('reservations.payments.store');
    Route::delete('/reservations/{id}', [AdminController::class, 'destroyReservation'])->name('reservations.destroy');
    Route::get('/checkin', function () {
        $checkIns = RoomReservation::with('room')
            ->whereIn('status', ['pending', 'confirmed', 'checked-in'])
            ->whereDate('check_in', today())
            ->latest()
            ->get();

        $checkOuts = RoomReservation::with(['room', 'payments'])
            ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
            ->whereDate('check_out', today())
            ->latest()
            ->get();

        $occupiedRooms = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();

        return view('employee.checkin', compact('checkIns', 'checkOuts', 'occupiedRooms', 'availableRooms'));
    })->name('checkin');
    Route::get('/room-status', function () {
        $rooms = \App\Models\Room::orderBy('room_number')->get();
        $inventoryItems = \App\Models\InventoryItem::orderBy('name')->get();
        $diningTables = DiningTable::orderBy('table_no')->get();
        $dining = DiningMenu::orderBy('name')->get();
        $diningSchedules = DiningSchedule::orderBy('available_from')->get();

        return view('employee.room-status', compact('rooms', 'inventoryItems', 'diningTables', 'dining', 'diningSchedules'));
    })->name('room-status');
    Route::get('/guest-requests', [AdminController::class, 'employeeGuestRequests'])->name('guest-requests');
    Route::get('/guest-requests/{id}', [AdminController::class, 'employeeGuestRequest'])->name('guest-requests.show');
    Route::patch('/guest-requests/{id}', [AdminController::class, 'updateEmployeeGuestRequest'])->name('guest-requests.update');
    Route::get('/messages', function () {
        $messages = Message::latest()->get();

        return view('employee.messages', compact('messages'));
    })->name('messages');

    Route::post('/messages', [AdminController::class, 'storeEmployeeMessage'])->name('messages.store');
});

// --- Protected Admin Routes (requires authentication) ---
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/rooms', [AdminController::class, 'rooms'])->name('rooms');
    Route::get('/dining', [AdminController::class, 'diningOverview'])->name('dining');
    Route::get('/dining/tables', [AdminController::class, 'diningTables'])->name('dining.tables');
    Route::get('/dining/menu', [AdminController::class, 'diningMenu'])->name('dining.menu');
    Route::get('/dining/schedule', [AdminController::class, 'diningSchedule'])->name('dining.schedule');
    Route::post('/dining', [AdminController::class, 'storeDiningItem'])->name('dining.store');
    Route::post('/rooms', [AdminController::class, 'storeRoom'])->name('rooms.store');
    Route::post('/inventory', [AdminController::class, 'storeInventoryItem'])->name('inventory.store');
    Route::put('/inventory/{id}', [AdminController::class, 'updateInventoryItem'])->name('inventory.update');
    Route::patch('/inventory/{id}/status', [AdminController::class, 'updateInventoryStatus'])->name('inventory.status');
    Route::match(['post', 'delete'], '/inventory/bulk-delete', [AdminController::class, 'bulkDestroyInventoryItems'])->name('inventory.bulkDestroy');
    Route::delete('/inventory/{id}', [AdminController::class, 'destroyInventoryItem'])->name('inventory.destroy');
    Route::put('/rooms/{id}', [AdminController::class, 'updateRoom'])->name('rooms.update');
    Route::patch('/rooms/{id}/status', [AdminController::class, 'updateRoomStatus'])->name('rooms.status');
    Route::match(['post', 'delete'], '/rooms/bulk-delete', [AdminController::class, 'bulkDestroyRooms'])->name('rooms.bulkDestroy');
    Route::match(['post', 'delete'], '/dining/bulk-delete', [AdminController::class, 'bulkDestroyDining'])->name('dining.bulkDestroy');
    Route::delete('/rooms/{id}', [AdminController::class, 'destroyRoom'])->name('rooms.destroy');
    Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
    Route::post('/reservations', [AdminController::class, 'storeReservation'])->name('reservations.store');
    Route::put('/reservations/{id}', [AdminController::class, 'updateReservation'])->name('reservations.update');
    Route::patch('/reservations/{id}/status', [AdminController::class, 'updateReservationStatus'])->name('reservations.status');
    Route::delete('/reservations/{id}', [AdminController::class, 'destroyReservation'])->name('reservations.destroy');
    Route::get('/guests', [AdminController::class, 'guests'])->name('guests');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::post('/messages/{id}/reply', [AdminController::class, 'replyMessage'])->name('messages.reply');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::patch('/maintenance-reports/{maintenanceReport}/status', [AdminController::class, 'updateMaintenanceReportStatus'])->name('maintenance-reports.status');
    Route::get('/reports/export-csv', [AdminController::class, 'exportReportsCsv'])->name('reports.export.csv');
    Route::get('/reports/print', [AdminController::class, 'printReports'])->name('reports.print');
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
    Route::get('/manage-account', [AdminController::class, 'manageAccount'])->name('manage-account');
    Route::post('/manage-account', [AdminController::class, 'storeAccount'])->name('manage-account.store');
    Route::put('/manage-account/{id}', [AdminController::class, 'updateAccountUser'])->name('manage-account.update');
    Route::patch('/manage-account/{id}/status', [AdminController::class, 'updateUserStatus'])->name('manage-account.status');
    Route::delete('/manage-account/{id}', [AdminController::class, 'destroyUser'])->name('manage-account.destroy');
    Route::match(['post', 'delete'], '/manage-account/bulk-delete', [AdminController::class, 'bulkDestroyUsers'])->name('manage-account.bulkDestroy');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings/account', [AdminController::class, 'updateAccount'])->name('settings.account');
});

// --- Housekeeping Portal ---
Route::prefix('housekeeping')->name('housekeeping.')->middleware(['auth', 'role:housekeeping'])->group(function () {
    Route::get('/dashboard', [HousekeepingController::class, 'dashboard'])->name('dashboard');
    Route::patch('/rooms/{id}/cleaning', [HousekeepingController::class, 'updateStatus'])->name('rooms.cleaning');
    Route::get('/assigned-rooms', [HousekeepingController::class, 'assignedRooms'])->name('assigned-rooms');
    Route::post('/tasks', [HousekeepingController::class, 'storeTask'])->name('tasks.store');
    Route::patch('/tasks/{housekeepingTask}/start', [HousekeepingController::class, 'startTask'])->name('tasks.start');
    Route::patch('/tasks/{housekeepingTask}/complete', [HousekeepingController::class, 'completeTask'])->name('tasks.complete');
    Route::delete('/tasks/{housekeepingTask}', [HousekeepingController::class, 'destroyTask'])->name('tasks.destroy');
    Route::get('/room-status-update', [HousekeepingController::class, 'roomStatusUpdate'])->name('room-status-update');
    Route::get('/guest-requests', [HousekeepingController::class, 'guestRequests'])->name('guest-requests');
    Route::get('/guest-requests/{id}', [HousekeepingController::class, 'guestRequestDetails'])->name('guest-requests.show');
    Route::patch('/guest-requests/{id}', [HousekeepingController::class, 'updateGuestRequest'])->name('guest-requests.update');
    Route::post('/guest-requests/{id}/mark-delivered', [HousekeepingController::class, 'markGuestRequestDelivered'])->name('guest-requests.mark-delivered');
    Route::get('/messages', [HousekeepingController::class, 'messages'])->name('messages');
    Route::get('/maintenance-report', [HousekeepingController::class, 'maintenanceReport'])->name('maintenance-report');
    Route::post('/maintenance-report', [HousekeepingController::class, 'storeMaintenanceReport'])->name('maintenance-report.store');
    Route::put('/maintenance-report/{maintenanceReport}', [HousekeepingController::class, 'updateMaintenanceReport'])->name('maintenance-report.update');
    Route::delete('/maintenance-report/{maintenanceReport}', [HousekeepingController::class, 'destroyMaintenanceReport'])->name('maintenance-report.destroy');
    Route::get('/cleaning-history', [HousekeepingController::class, 'cleaningHistory'])->name('cleaning-history');
});

// --- Logout (fallback route) ---
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware('auth:guest')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});


