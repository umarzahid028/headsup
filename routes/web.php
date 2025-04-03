<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\TransporterController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\SalesIssueController;
use App\Http\Controllers\GoodwillClaimController;
use App\Http\Controllers\VendorTypeController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public routes
Route::get('/track/{batchId}', [TransportController::class, 'trackBatch'])->name('transports.track');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Vehicle Management Routes
    Route::resource('vehicles', VehicleController::class);
    
    // Transport Management Routes
    Route::resource('transports', TransportController::class);
    Route::get('/transports/batch/{batchId}', [TransportController::class, 'showBatch'])->name('transports.batch');
    Route::post('/transports/{transport}/acknowledge', [TransportController::class, 'acknowledge'])->name('transports.acknowledge');
    
    // Transporter Management Routes
    Route::resource('transporters', TransporterController::class);
    
    // Vendor Management Routes
    Route::resource('vendors', VendorController::class);
    Route::patch('vendors/{vendor}/toggle-active', [VendorController::class, 'toggleActive'])->name('vendors.toggle-active');
    
    // Vendor Type Management Routes
    Route::resource('vendor-types', \App\Http\Controllers\VendorTypeController::class)->except(['show', 'destroy']);
    Route::patch('vendor-types/{vendorType}/toggle-active', [VendorTypeController::class, 'toggleActive'])->name('vendor-types.toggle-active');
    
    // Vendor Estimates
    Route::post('/vendor-estimates', [\App\Http\Controllers\VendorEstimateController::class, 'store'])->name('vendor-estimates.store');
    Route::patch('/vendor-estimates/{estimate}/approve', [\App\Http\Controllers\VendorEstimateController::class, 'approve'])
        ->name('vendor-estimates.approve');
        //->middleware('approve.estimates');
    Route::patch('/vendor-estimates/{estimate}/reject', [\App\Http\Controllers\VendorEstimateController::class, 'reject'])
        ->name('vendor-estimates.reject');
        //->middleware('approve.estimates');
    Route::get('/vendor-estimates/pending', [\App\Http\Controllers\VendorEstimateController::class, 'pendingEstimates'])
        ->name('vendor-estimates.pending');
        //->middleware('approve.estimates');
    
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
        Route::patch('/results/{result}/assign-vendor', [\App\Http\Controllers\InspectionItemResultController::class, 'assignVendor'])->name('results.assign-vendor');
        Route::patch('/results/{result}/mark-complete', [\App\Http\Controllers\InspectionItemResultController::class, 'markComplete'])->name('results.mark-complete');
        Route::post('/results/{result}/upload-photo', [\App\Http\Controllers\InspectionItemResultController::class, 'uploadPhoto'])->name('results.upload-photo');
    });

    // Sales Management Routes
    Route::prefix('sales')->name('sales.')->group(function () {
        // Sales Issues
        Route::resource('issues', SalesIssueController::class);
        Route::patch('issues/{issue}/status', [SalesIssueController::class, 'updateStatus'])->name('issues.update-status');
        Route::patch('issues/{issue}/priority', [SalesIssueController::class, 'updatePriority'])->name('issues.update-priority');

        // Goodwill Claims
        Route::resource('goodwill-claims', GoodwillClaimController::class);
        Route::patch('goodwill-claims/{claim}/status', [GoodwillClaimController::class, 'updateStatus'])->name('goodwill-claims.update-status');
        Route::patch('goodwill-claims/{claim}/consent', [GoodwillClaimController::class, 'updateConsent'])->name('goodwill-claims.update-consent');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings/csv', [App\Http\Controllers\Admin\SettingsController::class, 'updateCsvSettings'])->name('settings.update-csv-settings');
        // User management routes
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('roles', \App\Http\Controllers\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
        
        Route::patch('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    
        // Activity Log
        Route::get('activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');

        
        Route::get('system-settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('system-settings.index');
        Route::patch('system-settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'update'])->name('system-settings.update');
   
    });

    // Notification Routes
    Route::get('/notifications', function () {
        return view('notifications.index', [
            'notifications' => auth()->user()->notifications()->paginate(20)
        ]);
    })->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

require __DIR__.'/auth.php';
