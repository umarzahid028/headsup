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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\RepairImageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

// Register Broadcasting Routes
Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::get('/', function () {
    return redirect()->route('login');
});

// Public routes
Route::get('/track/{batchId}', [TransportController::class, 'trackBatch'])->name('transports.track');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', App\Http\Controllers\DashboardController::class)->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Vehicle Management Routes
    Route::resource('vehicles', VehicleController::class);
    
    // Vehicle Image Management Routes
    Route::post('/vehicles/{id}/images', [VehicleController::class, 'uploadImages'])->name('vehicles.images.upload');
    Route::delete('/vehicles/{vehicleId}/images/{imageId}', [VehicleController::class, 'deleteImage'])->name('vehicles.images.delete');
    Route::patch('/vehicles/{vehicleId}/images/order', [VehicleController::class, 'updateImageOrder'])->name('vehicles.images.order');
    Route::patch('/vehicles/{vehicleId}/images/{imageId}/feature', [VehicleController::class, 'setFeaturedImage'])->name('vehicles.images.feature');
    
    // Transport Management Routes
    Route::resource('transports', TransportController::class);
    Route::get('/transports/batch/create', [TransportController::class, 'createBatch'])->name('transports.batch.create')
        ->middleware(['auth', 'verified']);
    Route::get('/transports/batch/{batchId}', [TransportController::class, 'showBatch'])->name('transports.batch')
        ->middleware(['auth', 'verified']);
    Route::post('/transports/batch/{batchId}/acknowledge', [TransportController::class, 'acknowledgeBatch'])
        ->name('transports.batch.acknowledge')
        ->middleware(['auth']);
    
    // Transport status update routes
    Route::post('/transports/{transport}/update-status', [TransportController::class, 'updateTransportStatus'])
        ->name('transports.update-status');
    Route::post('/transports/batch/{batchId}/update-status', [TransportController::class, 'updateBatchStatus'])
        ->name('transports.batch.update-status');
    
    // Transporter Management Routes
    Route::resource('transporters', TransporterController::class);
    
    // Vendor Management Routes
    Route::resource('vendors', VendorController::class);
    Route::patch('vendors/{vendor}/toggle-active', [VendorController::class, 'toggleActive'])->name('vendors.toggle-active');
    Route::resource('vendor-types', VendorTypeController::class)->except(['show', 'destroy']);
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
    Route::prefix('inspection')->name('inspection.')->middleware(['auth'])->group(function () {
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
    Route::prefix('sales')->middleware(['auth'])->group(function () {
        // Sales Team Dashboard
        Route::get('/dashboard', [App\Http\Controllers\SalesTeam\DashboardController::class, 'index'])
            ->name('sales-team.dashboard');

        // Sales Team Routes
        Route::resource('sales-team', App\Http\Controllers\SalesTeamController::class);
        Route::patch('sales-team/{salesTeam}/toggle-active', [App\Http\Controllers\SalesTeamController::class, 'toggleActive'])->name('sales-team.toggle-active');
        Route::get('sales-team-test-flow', [App\Http\Controllers\SalesTeamController::class, 'testCreateFlow'])->name('sales-team.test-flow');

        // Sales Issues Routes
        Route::resource('issues', SalesIssueController::class)->names([
            'index' => 'sales.issues.index',
            'create' => 'sales.issues.create',
            'store' => 'sales.issues.store',
            'show' => 'sales.issues.show',
            'edit' => 'sales.issues.edit',
            'update' => 'sales.issues.update',
            'destroy' => 'sales.issues.destroy'
        ]);
        Route::patch('issues/{issue}/status', [SalesIssueController::class, 'updateStatus'])->name('sales.issues.update-status');
        Route::patch('issues/{issue}/priority', [SalesIssueController::class, 'updatePriority'])->name('sales.issues.update-priority');

        // Goodwill Claims Routes
        Route::resource('goodwill-claims', GoodwillClaimController::class)->names([
            'index' => 'sales.goodwill-claims.index',
            'create' => 'sales.goodwill-claims.create',
            'store' => 'sales.goodwill-claims.store',
            'show' => 'sales.goodwill-claims.show',
            'edit' => 'sales.goodwill-claims.edit',
            'update' => 'sales.goodwill-claims.update',
            'destroy' => 'sales.goodwill-claims.destroy'
        ]);
        Route::patch('goodwill-claims/{claim}/status', [GoodwillClaimController::class, 'updateStatus'])->name('sales.goodwill-claims.update-status');
        Route::patch('goodwill-claims/{claim}/consent', [GoodwillClaimController::class, 'updateConsent'])->name('sales.goodwill-claims.update-consent');
        
        // Signature capture routes
        Route::get('goodwill-claims/{claim}/signature', [GoodwillClaimController::class, 'showSignatureForm'])
            ->name('sales.goodwill-claims.signature.show');
        Route::post('goodwill-claims/{claim}/signature', [GoodwillClaimController::class, 'storeSignature'])
            ->name('sales.goodwill-claims.signature.store');
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
    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read');
    })->name('notifications.mark-all-read');

    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/manager', [DashboardController::class, 'manager'])
        ->name('dashboard.manager')
        ->middleware('role:manager');
    Route::get('/dashboard/transporter', [DashboardController::class, 'transporter'])
        ->middleware('role:Admin,Manager,Transporter')
        ->name('dashboard.transporter');
    Route::get('/dashboard/vendor', [DashboardController::class, 'vendor'])
        ->middleware('role:Admin,Manager,Vendor')
        ->name('dashboard.vendor');
});

// Vendor Routes
Route::middleware(['auth'])->prefix('vendor')->name('vendor.')->group(function () {
    // Redirect base vendor URL to vendor dashboard
    Route::get('/', function() {
        return redirect()->route('vendor.dashboard');
    });
    
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/inspection-history', [VendorDashboardController::class, 'inspectionHistory'])->name('inspection-history');
    Route::get('/inspections/{inspection}', [VendorDashboardController::class, 'showInspection'])->name('inspections.show');
    Route::post('/inspections/{inspection}/submit-estimate', [VendorDashboardController::class, 'submitEstimate'])->name('inspections.submit-estimate');
    Route::post('/inspections/{inspection}/update-status', [VendorDashboardController::class, 'updateServiceStatus'])->name('inspections.update-status');

    // New Vendor Inspection Routes
    Route::get('/assigned-inspections', [\App\Http\Controllers\Vendor\InspectionController::class, 'index'])->name('inspections.index');
    Route::get('/assigned-inspections/{inspection}', [\App\Http\Controllers\Vendor\InspectionController::class, 'show'])->name('inspections.show');
    Route::patch('/inspection-items/{item}/update-status', [\App\Http\Controllers\Vendor\InspectionController::class, 'updateItemStatus'])->name('inspections.update-item');
    Route::post('/inspection-items/{item}/upload-images', [\App\Http\Controllers\Vendor\InspectionController::class, 'uploadImages'])->name('inspections.upload-images');
});

Route::delete('/repair-images/{repairImage}', [RepairImageController::class, 'destroy'])->name('repair-images.destroy');

// Example Routes
Route::get('/examples/icon-inputs', function () {
    return view('examples.icon-inputs');
})->middleware(['auth', 'verified'])->name('examples.icon-inputs');

require __DIR__.'/auth.php';
