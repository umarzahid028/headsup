<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\TransporterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public routes
Route::get('/track/{batchId}', [App\Http\Controllers\TransportController::class, 'trackBatch'])->name('transports.track');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Vehicle Management Routes
    Route::resource('vehicles', VehicleController::class);
    
    // Transport Management Routes
    Route::resource('transports', TransportController::class);
    Route::get('/transports/batch/{batchId}', [App\Http\Controllers\TransportController::class, 'showBatch'])->name('transports.batch');
    
    // Transporter Management Routes
    Route::resource('transporters', TransporterController::class);
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|super-admin'])->group(function () {
        // User management routes
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('roles', \App\Http\Controllers\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
    });
});

require __DIR__.'/auth.php';
