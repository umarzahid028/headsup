<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\QueuesController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CreatePersonController;
use App\Http\Controllers\CustomerSaleController;
use App\Http\Controllers\SalesDashboardController;
use App\Models\Queue;
// Register Broadcasting Routes
Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::get('/', function () {
    return redirect()->route('login');
});

// Public routes

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/csv', [App\Http\Controllers\Admin\SettingsController::class, 'updateCsvSettings'])->name('settings.update-csv-settings');
    // User management routes
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::post('users/{user}/verify', [\App\Http\Controllers\UserController::class, 'verify'])->name('users.verify');
    Route::get('roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
    Route::put('roles/update-permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
    Route::resource('permissions', \App\Http\Controllers\PermissionController::class);

    Route::patch('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
});

//Queues Routes
Route::get('/sales/{id}', [DashboardController::class, 'salesdashboard'])->name('sales.perosn');
Route::post('/sales-person', [QueuesController::class, 'dashboardstore'])->name('sales.person.store');

//Sales person status
Route::get('/status', [StatusController::class, 'showStatus']);

// Active tokens ke liye API (AJAX se call hoga, auth & role middleware lagao)
Route::get('/queue-list', [TokenController::class, 'activeTokens'])->name('tokens.active');
Route::middleware(['auth', 'role:Sales person'])->group(function () {
    Route::post('/tokens/{token}/complete', [TokenController::class, 'completeToken'])->name('tokens.complete');
    // routes/web.php
    Route::post('/tokens/{token}/skip', [TokenController::class, 'skip'])->name('tokens.skip');
});
// routes/web.php
Route::post('/token/{id}/resume', [TokenController::class, 'resume'])->name('token.resume');

Route::post('/token/{id}/hold', [TokenController::class, 'hold'])->name('token.hold');
Route::post('/tokens/next/{token}', [TokenController::class, 'assignNextToken'])->middleware('auth');

Route::post('/check-in', [TokenController::class, 'checkIn'])->middleware('auth');

//Current Token
Route::middleware('auth')->get('/token/current', [TokenController::class, 'currentAssignedToken'])->name('token.current');
//Active Token auto refresh
Route::get('/tokens/current-assigned', function () {
    $user = Auth::user();  // logged-in user ko lelo

    if (!$user) {
        return response('Unauthorized', 401);
    }
    // Check if user is checked in
    $isCheckedIn = \App\Models\Queue::where('user_id', $user->id)
                    ->where('is_checked_in', true)
                    ->exists();

    if (!$isCheckedIn) {
        $token = null;
        return view('partials.current-token', compact('token'));

    }

    $token = \App\Models\Token::with('salesperson')
        ->where('user_id', $user->id)  
        ->where('status', 'assigned')
        ->latest('created_at')
        ->first();

    return view('partials.current-token', compact('token'));
})->middleware(['auth', 'web']);

//Appoinments
Route::middleware(['auth'])->group(function () {

    // Both Sales Manager and Sales Person
    Route::middleware('role:Admin|Sales Manager|Sales person')->group(function () {
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointment.records');
    });

    // Only Sales Manager
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointment.create');
        Route::post('/appointments', [AppointmentController::class, 'store']);
   
Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
Route::get('/appointments/{appointment}/form', [AppointmentController::class, 'form'])->name('appointments.form');
Route::post('/appointments/form', [AppointmentController::class, 'formstore'])->name('appointments.form.store');


    // Only Sales Person
   
        Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
  
});
//Appointment form routes

Route::post('/appointment-sales', [AppointmentController::class, 'appointmentstore'])->name('customer.appointment.store');
//Token History
Route::get('token/history', [TokenController::class, 'tokenhistory'])->name('token.history.view');
//Customer Sales
Route::post('/customer-sales', [CustomerSaleController::class, 'store'])->name('customer.sales.store');
Route::get('/customers', [CustomerSaleController::class, 'index'])->name('customer.index');
//Create Sale perosn
Route::get('saleperson', [UserController::class, 'saletable'])->name('saleperson.table')->middleware('role:Admin|Sales Manager');
Route::get('edit/sales/{id}', [UserController::class,'editsales'])->name('edit.saleperson')->middleware('role:Admin|Sales Manager');
Route::post('edit/sales/{id}', [UserController::class,'updatesales'])->name('update.saleperon')->middleware('role:Admin|Sales Manager');
Route::get('create/saleperson', [UserController::class, 'create'])->name('create.saleperson')->middleware('role:Admin|Sales Manager');
Route::post('create/saleperson', [UserController::class, 'store'])->name('store.saleperson');
Route::delete('/salesperson/delete/{id}', [UserController::class, 'deleteSalesperson'])->name('salesperson.delete');

//Activity Records
Route::middleware(['auth'])->get('/sales/activity-report', [SalesDashboardController::class, 'activityReport'])->name('sales.activity.report');

Route::post('/sales-person/take-turn', [QueuesController::class, 'takeTurn'])
    ->name('sales.person.takeTurn');
Route::get('/next-turn-status', [QueuesController::class, 'nextTurnStatus']);


Route::get('/checkins', [TokenController::class, 'checkinSalespersons'])->name('salespersons.checkin');

Route::post('/stop-timer/{id}', [CustomerSaleController::class, 'stopTimer']);
// Customer Transfor
Route::post('/customers/{id}/transfer', [CustomerSaleController::class, 'transfer']);
Route::get('add/customer', [CustomerSaleController::class, 'addcustomer'])->name('add.customer');

// T/o customer
Route::middleware(['auth'])->group(function () {
    Route::get('/t/o-customers/customer', [CustomerSaleController::class, 'customer'])->name('to.customers');
    Route::post('/customers/transfer', [CustomerSaleController::class, 'transferToManager'])->name('customers.transfer');
});

// Time customer
Route::post('/customer/complete-form/{id}', [CustomerSaleController::class, 'completeForm'])->name('customer.completeForm');

    

    require __DIR__.'/auth.php';

