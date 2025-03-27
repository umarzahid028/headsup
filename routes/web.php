<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Vehicle Intake & Dispatch
    Route::get('/vehicles/intake', [\App\Http\Controllers\VehicleIntakeController::class, 'index'])->name('vehicles.intake');
    Route::post('/vehicles/intake/import-ftp', [\App\Http\Controllers\VehicleIntakeController::class, 'importFtp'])->name('vehicles.intake.import-ftp');
    Route::post('/vehicles/intake/assign-transporter/{vehicle}', [\App\Http\Controllers\VehicleIntakeController::class, 'assignTransporter'])->name('vehicles.intake.assign-transporter');
    Route::post('/vehicles/intake/upload-document/{vehicle}', [\App\Http\Controllers\VehicleIntakeController::class, 'uploadDocument'])->name('vehicles.intake.upload-document');
    Route::get('/vehicles/intake/scan/{vin?}', [\App\Http\Controllers\VehicleIntakeController::class, 'scan'])->name('vehicles.intake.scan');
    
    // Vehicle routes
    Route::resource('vehicles', \App\Http\Controllers\VehicleController::class);
    Route::patch('/vehicles/{vehicle}/stage', [\App\Http\Controllers\VehicleController::class, 'updateStage'])->name('vehicles.update.stage');
    
    // Vendor & Transporter routes
    Route::resource('vendors', \App\Http\Controllers\VendorController::class);
    Route::get('/vendors/transporters', [\App\Http\Controllers\VendorController::class, 'transporters'])->name('transporters.index');
    
    // FTP Import settings
    Route::get('/ftp-import', [\App\Http\Controllers\FtpImportController::class, 'index'])->name('ftp-import.index');
    Route::post('/ftp-import/settings', [\App\Http\Controllers\FtpImportController::class, 'updateSettings'])->name('ftp-import.settings');
    Route::post('/ftp-import/run', [\App\Http\Controllers\FtpImportController::class, 'runImport'])->name('ftp-import.run');
    
    // Task routes
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);
    Route::patch('/tasks/{task}/complete', [\App\Http\Controllers\TaskController::class, 'complete'])->name('tasks.complete');
    
    // Document routes
    Route::post('/documents', [\App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [\App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [\App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.destroy');
    
    // We-Owe & Goodwill routes
    Route::get('/we-owe', [\App\Http\Controllers\WeOweController::class, 'index'])->name('we-owe.index');
    
    // Reports routes
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    
    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    
    // Reconditioning Workflow routes
    Route::prefix('recon')->name('recon.')->group(function () {
        Route::resource('workflows', \App\Http\Controllers\ReconWorkflowController::class);
        Route::post('workflows/{workflow}/diagram', [\App\Http\Controllers\ReconWorkflowController::class, 'uploadDiagram'])->name('workflows.diagram');
        
        Route::resource('items', \App\Http\Controllers\InspectionItemController::class);
        Route::post('items/{item}/complete', [\App\Http\Controllers\InspectionItemController::class, 'complete'])->name('items.complete');
        Route::post('items/{item}/assign-vendor', [\App\Http\Controllers\InspectionItemController::class, 'assignVendor'])->name('items.assign-vendor');
        Route::post('items/{item}/photos', [\App\Http\Controllers\InspectionItemController::class, 'uploadPhotos'])->name('items.upload-photos');
        Route::delete('photos/{photo}', [\App\Http\Controllers\InspectionItemController::class, 'deletePhoto'])->name('photos.delete');
    });
    
    // Test routes page (for debugging)
    Route::get('/test-routes', function() {
        return view('test-routes');
    })->name('test-routes');
    
    // Test route specifically for vehicles.intake
    Route::get('/test-vehicles-intake', function() {
        return "This is a test for vehicles.intake route";
    })->name('test.vehicles.intake');
    
    // Route diagnostics page
    Route::get('/route-diagnostics', function() {
        return view('route-diagnostics');
    })->name('route.diagnostics');

    // Post-repair frontline ready routes
    Route::prefix('vehicles/frontline')->name('vehicles.frontline.')->group(function () {
        Route::get('/', [App\Http\Controllers\FrontlineReadyController::class, 'index'])->name('index');
        Route::get('/{workflow}/confirm', [App\Http\Controllers\FrontlineReadyController::class, 'confirm'])->name('confirm');
        Route::post('/{workflow}/mark-ready', [App\Http\Controllers\FrontlineReadyController::class, 'markAsFrontlineReady'])->name('mark');
    });
    
    // Post-sale management routes
    Route::prefix('post-sale')->name('post-sale.')->group(function () {
        Route::get('/', [App\Http\Controllers\PostSaleController::class, 'index'])->name('index');
        Route::get('/vehicle/{vehicle}', [App\Http\Controllers\PostSaleController::class, 'show'])->name('show');
        Route::post('/vehicle/{vehicle}/archive', [App\Http\Controllers\PostSaleController::class, 'archive'])->name('archive');
        Route::post('/vehicle/{vehicle}/unarchive', [App\Http\Controllers\PostSaleController::class, 'unarchive'])->name('unarchive');
        Route::post('/vehicle/{vehicle}/reopen', [App\Http\Controllers\PostSaleController::class, 'reopenVehicle'])->name('reopen');
        Route::get('/we-owe-items', [App\Http\Controllers\PostSaleController::class, 'weOweItems'])->name('we-owe-items');
        Route::get('/goodwill-repairs', [App\Http\Controllers\PostSaleController::class, 'goodwillRepairs'])->name('goodwill-repairs');
    });
    
    // Goodwill repairs routes
    Route::resource('goodwill-repairs', App\Http\Controllers\GoodwillRepairController::class);
    Route::get('/goodwill-repairs/{goodwillRepair}/waiver', [App\Http\Controllers\GoodwillRepairController::class, 'showWaiverForm'])->name('goodwill-repairs.waiver');
    Route::post('/goodwill-repairs/{goodwillRepair}/waiver', [App\Http\Controllers\GoodwillRepairController::class, 'processWaiver'])->name('goodwill-repairs.process-waiver');
    Route::get('/goodwill-repairs/{goodwillRepair}/waiver-complete', [App\Http\Controllers\GoodwillRepairController::class, 'waiverComplete'])->name('goodwill-repairs.waiver-complete');
    Route::get('/goodwill-repairs/{goodwillRepair}/download-waiver', [App\Http\Controllers\GoodwillRepairController::class, 'downloadWaiver'])->name('goodwill-repairs.download-waiver');

    // Tag routes
    Route::resource('tags', \App\Http\Controllers\TagController::class);
    Route::post('/tags/attach', [\App\Http\Controllers\TagController::class, 'attachToVehicle'])->name('tags.attach');
    Route::post('/tags/detach', [\App\Http\Controllers\TagController::class, 'detachFromVehicle'])->name('tags.detach');

    // Vehicle checklist routes
    Route::resource('checklists', \App\Http\Controllers\VehicleChecklistController::class);
    Route::patch('/checklist-items/{item}/status', [\App\Http\Controllers\VehicleChecklistController::class, 'updateItemStatus'])->name('checklist-items.status');
    Route::post('/checklists/{checklist}/complete', [\App\Http\Controllers\VehicleChecklistController::class, 'markAsComplete'])->name('checklists.complete');

    // Vehicle photo routes
    Route::resource('photos', \App\Http\Controllers\VehiclePhotoController::class);
    Route::post('/photos/reorder', [\App\Http\Controllers\VehiclePhotoController::class, 'reorder'])->name('photos.reorder');
    Route::post('/photos/{photo}/set-primary', [\App\Http\Controllers\VehiclePhotoController::class, 'setPrimary'])->name('photos.set-primary');

    // Tag seeder route
    Route::get('/tags/seed-system-tags', [\App\Http\Controllers\TagSeederController::class, 'seedTags'])->name('tags.seed');
    
    // Time-based alerts routes
    Route::resource('alerts', \App\Http\Controllers\TimeBasedAlertController::class);
    Route::post('/alerts/{alert}/resolve', [\App\Http\Controllers\TimeBasedAlertController::class, 'resolve'])->name('alerts.resolve');
    Route::post('/vehicles/{vehicle}/create-default-alerts', [\App\Http\Controllers\TimeBasedAlertController::class, 'createDefaultVehicleAlerts'])->name('vehicles.create-default-alerts');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|super-admin'])->group(function () {
        // User management routes
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('roles', \App\Http\Controllers\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
    });
});

require __DIR__.'/auth.php';
