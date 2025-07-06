<?php

use App\Models\Queue;
use App\Models\Appointment;
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
Route::get('/sales/{id?}', [DashboardController::class, 'salesdashboard'])->name('sales.perosn');
Route::post('/sales-person', [QueuesController::class, 'dashboardstore'])->name('sales.person.store');
Route::get('/check-in-status', [QueuesController::class, 'checkInStatus']);
//Sales person status
Route::get('/status', [StatusController::class, 'showStatus']);

// Active tokens ke liye API (AJAX se call hoga, auth & role middleware lagao)
Route::get('/queue-list', [TokenController::class, 'queuelist'])->name('tokens.active');
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
   Route::get('/apppointment/view/{appointment}', [AppointmentController::class, 'view'])->name('appointment.view');

    // Only Sales Person

    Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
});
//Appointment form routes

Route::post('/appointment-sales', [AppointmentController::class, 'appointmentstore'])->name('customer.appointment.store');
//Token History
Route::get('/add/users', [TokenController::class, 'addusers'])->name('token.history.view');
//Customer Sales
Route::post('/customer-sales', [CustomerSaleController::class, 'store'])->name('customer.sales.store');
Route::get('/customers', [CustomerSaleController::class, 'index'])->name('customer.index');
//Create Sale perosn
Route::get('saleperson', [UserController::class, 'saletable'])->name('saleperson.table')->middleware('role:Admin|Sales Manager');
Route::get('edit/sales/{id}', [UserController::class, 'editsales'])->name('edit.saleperson')->middleware('role:Admin|Sales Manager');
Route::post('edit/sales/{id}', [UserController::class, 'updatesales'])->name('update.saleperon')->middleware('role:Admin|Sales Manager');
Route::get('create/saleperson', [UserController::class, 'create'])->name('create.saleperson')->middleware('role:Admin|Sales Manager');
Route::post('create/saleperson', [UserController::class, 'store'])->name('store.saleperson');
Route::delete('/salesperson/delete/{id}', [UserController::class, 'deleteSalesperson'])->name('salesperson.delete');

//Activity Records

Route::middleware(['auth'])->group(function () {
    Route::get('/activity-report', [SalesDashboardController::class, 'activityReport'])->name('activity.report');
});


Route::post('/sales-person/take-turn', [QueuesController::class, 'takeTurn'])
    ->name('sales.person.takeTurn');
Route::get('/next-turn-status', [QueuesController::class, 'nextTurnStatus']);


Route::get('/checkins', [TokenController::class, 'checkinSalespersons'])->name('salespersons.checkin');

Route::post('/stop-timer/{id}', [CustomerSaleController::class, 'stopTimer']);
// Customer Transfor
Route::middleware(['auth'])->group(function () {
    Route::post('/customers/{id}/transfer', [CustomerSaleController::class, 'transfer'])->name('customers.transfer');
});

Route::get('add/customer', [CustomerSaleController::class, 'addcustomer'])->name('add.customer');


// routes/web.php
Route::post('/forward-to-manager', [CustomerSaleController::class, 'forwardToManager'])->name('customer.forward');
Route::get('/t/o-customers/customer', [CustomerSaleController::class, 'customer'])->name('to.customers');
// web.php
Route::get('/customers/fetch', [CustomerSaleController::class, 'fetch'])->name('customers.fetch');
Route::post('/forward-customer', [CustomerSaleController::class, 'forward']);


// Time customer
Route::post('/customer/complete-form/{id}', [CustomerSaleController::class, 'completeForm'])->name('customer.completeForm');

// Customer Save to manager
Route::post('/customer-form', [CustomerSaleController::class, 'customerform'])->name('customer.form.store');

Route::post('sales/person-checkout/{id}', [CustomerSaleController::class, 'checkout'])->name('sales.person.checkout');
Route::get('/appointment/section', function () {
    $appointment = Appointment::where('status', '!=', 'completed')->latest()->first();
    return view('partials.appointment-card', compact('appointment'));
});

Route::post('/appointment/arrive', [CustomerSaleController::class, 'saveArrivalTime'])->name('appointment.arrive');

require __DIR__ . '/auth.php';
