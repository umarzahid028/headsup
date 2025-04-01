<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\TransporterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

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

    // Transport Management Routes
    Route::middleware(['auth'])->group(function () {
        // Batch Routes
        Route::get('/batches', [App\Http\Controllers\BatchController::class, 'index'])
            ->name('batches.index');
        Route::get('/batches/create', [App\Http\Controllers\BatchController::class, 'create'])
            ->name('batches.create');
        Route::post('/batches', [App\Http\Controllers\BatchController::class, 'store'])
            ->name('batches.store');
        Route::get('/batches/{batch}', [App\Http\Controllers\BatchController::class, 'show'])
            ->name('batches.show');
        Route::get('/batches/{batch}/edit', [App\Http\Controllers\BatchController::class, 'edit'])
            ->name('batches.edit');
        Route::put('/batches/{batch}', [App\Http\Controllers\BatchController::class, 'update'])
            ->name('batches.update');
        Route::patch('/batches/{batch}/status', [App\Http\Controllers\BatchController::class, 'updateStatus'])
            ->name('batches.update-status');
        Route::delete('/batches/{batch}', [App\Http\Controllers\BatchController::class, 'destroy'])
            ->name('batches.destroy');

        // Gate Pass Routes
        Route::get('/gate-passes', [App\Http\Controllers\GatePassController::class, 'index'])
            ->name('gate-passes.index');
        Route::get('/gate-passes/create', [App\Http\Controllers\GatePassController::class, 'create'])
            ->name('gate-passes.create');
        Route::post('/gate-passes', [App\Http\Controllers\GatePassController::class, 'store'])
            ->name('gate-passes.store');
        Route::get('/gate-passes/{gatePass}', [App\Http\Controllers\GatePassController::class, 'show'])
            ->name('gate-passes.show');
        Route::get('/gate-passes/{gatePass}/edit', [App\Http\Controllers\GatePassController::class, 'edit'])
            ->name('gate-passes.edit');
        Route::put('/gate-passes/{gatePass}', [App\Http\Controllers\GatePassController::class, 'update'])
            ->name('gate-passes.update');
        Route::patch('/gate-passes/{gatePass}/status', [App\Http\Controllers\GatePassController::class, 'updateStatus'])
            ->name('gate-passes.update-status');
        Route::get('/gate-passes/{gatePass}/download', [App\Http\Controllers\GatePassController::class, 'download'])
            ->name('gate-passes.download');
        Route::delete('/gate-passes/{gatePass}', [App\Http\Controllers\GatePassController::class, 'destroy'])
            ->name('gate-passes.destroy');
    });
});

require __DIR__.'/auth.php';
