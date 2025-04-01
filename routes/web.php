<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\TransporterController;
use App\Http\Controllers\VendorController;
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
    
    // Vendor Management Routes
    Route::resource('vendors', VendorController::class);
    Route::patch('vendors/{vendor}/toggle-active', [VendorController::class, 'toggleActive'])->name('vendors.toggle-active');
    
    // Inspection & Repair Routes
    Route::prefix('inspection')->name('inspection.')->group(function () {
        // Inspection Stages
        Route::resource('stages', \App\Http\Controllers\InspectionStageController::class);
        Route::post('stages/reorder', [\App\Http\Controllers\InspectionStageController::class, 'reorder'])->name('stages.reorder');
        Route::patch('stages/{stage}/toggle-active', [\App\Http\Controllers\InspectionStageController::class, 'toggleActive'])->name('stages.toggle-active');
        
        // Inspection Items
        Route::resource('items', \App\Http\Controllers\InspectionItemController::class);
        Route::patch('items/{item}/toggle-active', [\App\Http\Controllers\InspectionItemController::class, 'toggleActive'])->name('items.toggle-active');
        
        // Vehicle Inspections
        Route::resource('inspections', \App\Http\Controllers\VehicleInspectionController::class)
            ->except(['store']);
        Route::post('inspections', [\App\Http\Controllers\VehicleInspectionController::class, 'store'])
            ->name('inspections.store');
        Route::post('inspections/{inspection}/update-items', [\App\Http\Controllers\VehicleInspectionController::class, 'updateItems'])->name('inspections.update-items');
        Route::post('inspections/items/{result}/images', [\App\Http\Controllers\VehicleInspectionController::class, 'uploadImages'])->name('inspections.upload-images');
        Route::delete('inspections/images/{image}', [\App\Http\Controllers\VehicleInspectionController::class, 'deleteImage'])->name('inspections.delete-image');
        Route::post('vehicles/{vehicle}/start-inspection', [\App\Http\Controllers\VehicleInspectionController::class, 'startInspection'])->name('vehicles.start-inspection');
        Route::patch('inspections/{inspection}/complete', [\App\Http\Controllers\VehicleInspectionController::class, 'markComplete'])->name('inspections.complete');
        
        // Comprehensive Inspection (all stages at once)
        Route::get('vehicles/{vehicle}/comprehensive', [\App\Http\Controllers\VehicleInspectionController::class, 'comprehensive'])->name('comprehensive.show');
        Route::post('vehicles/{vehicle}/comprehensive', [\App\Http\Controllers\VehicleInspectionController::class, 'comprehensiveStore'])->name('comprehensive.store');
        
        // Inspection Results
        Route::resource('results', \App\Http\Controllers\InspectionItemResultController::class)->only(['store', 'update', 'destroy']);
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|super-admin'])->group(function () {
        // User management routes
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('roles', \App\Http\Controllers\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
    });
});

require __DIR__.'/auth.php';
