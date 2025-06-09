<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\QueuesController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\SalesIssueController;
use App\Http\Controllers\VendorTypeController;
use App\Http\Controllers\RepairImageController;
use App\Http\Controllers\TransporterController;
use App\Http\Controllers\CustomerSaleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GoodwillClaimController;
use App\Http\Controllers\VehicleStatusController;
use App\Http\Controllers\VendorDashboardController;

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
Route::post('/sales-person', [QueuesController::class, 'dashboardstore'])->name('sales.perosn.store');

//Sales person status
Route::get('/status', [StatusController::class, 'showStatus']);

//Tokens Routes
Route::get('/tokens', [TokenController::class, 'showTokensPage'])->name('tokens.page');
// Ye blade page dikhaega (salesperson ya customer dono ke liye public ya middleware lagao apni marzi se)

// Token generate karne ke liye (POST request, public ya auth middleware laga sakte ho)
Route::post('/tokens/generate', [TokenController::class, 'generateToken'])->name('tokens.generate');

// Active tokens ke liye API (AJAX se call hoga, auth & role middleware lagao)
Route::middleware(['auth', 'role:Sales person'])->group(function () {
    Route::get('/tokens/active', [TokenController::class, 'activeTokens'])->name('tokens.active');
    Route::post('/tokens/{token}/complete', [TokenController::class, 'completeToken'])->name('tokens.complete');
    // routes/web.php
    Route::post('/tokens/{token}/skip', [TokenController::class, 'skip'])->name('tokens.skip');
});
Route::post('/check-in', [TokenController::class, 'checkIn'])->middleware('auth');

//Current Token
Route::middleware('auth')->get('/token/current', [TokenController::class, 'currentAssignedToken'])->name('token.current');
//Active Token auto refresh
Route::get('/tokens/current-assigned', function () {
    $user = Auth::user();  // logged-in user ko lelo

    if (!$user) {
        return response('Unauthorized', 401);
    }

    $token = \App\Models\Token::with('salesperson')
        ->where('user_id', $user->id)  // sirf current user ke tokens
        ->where('status', 'assigned')
        ->latest('created_at')
        ->first();

    return view('partials.current-token', compact('token'));
})->middleware(['auth', 'web']);  // ensure auth middleware laga ho


//Token History
Route::get('token/history', [TokenController::class, 'tokenhistory'])->name('token.history.view');
//Customer Sales
Route::post('/customer-sales', [CustomerSaleController::class, 'store'])->name('customer.sales.store');
require __DIR__ . '/auth.php';
