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
Route::get('/sales-person', [DashboardController::class, 'salesdashboard'])->name('sales.perosn');
Route::post('/sales-person', [QueuesController::class, 'dashboardstore'])->name('sales.person.store');

//Sales person status
Route::get('/status', [StatusController::class, 'showStatus']);

//Tokens Routes
Route::get('/tokens', [TokenController::class, 'showTokensPage'])->name('tokens.page');

Route::post('/tokens/generate', [TokenController::class, 'generateToken'])->name('tokens.generate');

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
    Route::middleware('role:Admin|Sales Manager')->group(function () {
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointment.create');
        Route::post('/appointments', [AppointmentController::class, 'store']);
    });

    // Only Sales Person
    Route::middleware('role:Admin|Sales person')->group(function () {
        Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
    });
});
//Appointment form routes
Route::get('appointment/form/{id}', [AppointmentController::class, 'appointmentform'])->name('appointment.form');
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


Route::middleware('auth')->get('/next-turn-status', function () {
    $user = Auth::user();

    $myQueue = Queue::where('user_id', $user->id)
        ->where('is_checked_in', true)
        ->whereNull('took_turn_at')
        ->orderBy('id')
        ->first();

    $othersBefore = 0;

    if ($myQueue) {
        $othersBefore = Queue::where('is_checked_in', true)
            ->whereNull('took_turn_at')
            ->where('id', '<', $myQueue->id)
            ->count();
    }

    return response()->json([
        'is_your_turn'   => $myQueue !== null && $othersBefore === 0,
        'others_pending' => $othersBefore,
    ]);
});

Route::post('/stop-timer/{id}', [CustomerSaleController::class, 'stopTimer']);
// Customer Transfor
Route::post('/customers/{id}/transfer', [CustomerSaleController::class, 'transfer']);


require __DIR__ . '/auth.php';
