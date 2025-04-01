<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\NewVehicleImported;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class VehicleImportService
{
    /**
     * Debug mode
     * 
     * @var bool
     */
    protected $debug = false;
    
    /**
     * Dry run mode
     * 
     * @var bool
     */
    protected $dryRun = false;
    
    /**
     * Custom logger
     * 
     * @var callable|null
     */
    protected $logger = null;
    
    /**
     * Set debug mode
     * 
     * @param bool $debug
     * @return self
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }
    
    /**
     * Set dry run mode
     * 
     * @param bool $dryRun
     * @return self
     */
    public function setDryRun(bool $dryRun): self
    {
        $this->dryRun = $dryRun;
        return $this;
    }
    
    /**
     * Set custom logger
     * 
     * @param callable $logger
     * @return self
     */
    public function setLogger(callable $logger): self
    {
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * Log a message
     * 
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        // Use custom logger if set
        if ($this->logger !== null) {
            call_user_func($this->logger, $level, $message, $context);
        }
        
        // Always log to default logger too
        Log::{$level}($message, $context);
    }
    
    /**
     * Process a CSV file with vehicle data
     *
     * @param string $filePath The full path to the CSV file
     * @param bool $shouldSendNotifications Whether to send notifications or not
     * @return array Summary of import results
     */
    public function processCsvFile(string $filePath, bool $shouldSendNotifications = true): array
    {
        // Get the filename for tracking
        $fileName = basename($filePath);
        
        // Check if file exists and is readable
        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->log('error', "CSV file not found or not readable: {$filePath}");
            return [
                'success' => false,
                'message' => "File not found or not readable: {$fileName}",
                'imported' => 0,
                'skipped' => 0,
                'errors' => 1,
            ];
        }
        
        // Stats tracking
        $imported = 0;
        $skipped = 0;
        $errors = 0;
        
        // Open the CSV file
        $handle = fopen($filePath, 'r');
        
        // Get headers from first row of CSV
        $headers = fgetcsv($handle);
        
        if ($this->debug) {
            $this->log('debug', "CSV Headers: ", ['headers' => $headers]);
        }
        
        // Clean and normalize the headers
        $headers = array_map(fn($header) => $this->normalizeHeader($header), $headers);
        
        if ($this->debug) {
            $this->log('debug', "Normalized Headers: ", ['headers' => $headers]);
        }
        
        // Process each row
        $rowCount = 0;
        while (($data = fgetcsv($handle)) !== false) {
            $rowCount++;
            
            // Skip empty rows
            if (count(array_filter($data)) === 0) {
                $this->log('debug', "Skipping empty row {$rowCount}");
                continue;
            }
            
            // Skip rows with wrong column count
            if (count($data) !== count($headers)) {
                $this->log('warning', "Row {$rowCount} has different column count than headers. Skipping.", [
                    'headers_count' => count($headers),
                    'data_count' => count($data),
                    'file' => $fileName,
                ]);
                $errors++;
                continue;
            }
            
            // Combine headers with data to create associative array
            $vehicleData = array_combine($headers, $data);
            
            if ($this->debug) {
                $this->log('debug', "Processing row {$rowCount} data: ", ['data' => $vehicleData]);
            }
            
            // Process the vehicle data
            try {
                $result = $this->processVehicleData($vehicleData, $fileName);
                
                if ($result['status'] === 'imported') {
                    $imported++;
                    $this->log('info', "Imported vehicle: {$result['message']}");
                    
                    // Send notifications for new vehicles
                    if ($shouldSendNotifications && !$this->dryRun) {
                        $this->sendNotifications($result['vehicle'], $fileName);
                    }
                } elseif ($result['status'] === 'skipped') {
                    $skipped++;
                    $this->log('info', "Skipped vehicle: {$result['message']}");
                }
            } catch (\Exception $e) {
                $this->log('error', "Error processing row {$rowCount}: " . $e->getMessage(), [
                    'file' => $fileName,
                    'data' => $vehicleData,
                    'exception' => $e->getMessage(),
                ]);
                $errors++;
            }
        }
        
        fclose($handle);
        
        return [
            'success' => true,
            'message' => "Processed file: {$fileName}",
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }
    
    /**
     * Process individual vehicle data from CSV
     *
     * @param array $data The associative array of vehicle data
     * @param string $fileName The name of the import file
     * @return array Status and vehicle instance
     */
    protected function processVehicleData(array $data, string $fileName): array
    {
        // Map CSV fields to database fields
        $vehicleData = $this->mapFieldsToModel($data);
        
        if ($this->debug) {
            $this->log('debug', "Mapped vehicle data:", ['data' => $vehicleData]);
        }
        
        // Basic validation
        if (empty($vehicleData['stock_number'])) {
            throw new \Exception("Stock number is required");
        }
        
        if (empty($vehicleData['vin'])) {
            throw new \Exception("VIN is required");
        }
        
        // Add import file info
        $vehicleData['import_file'] = $fileName;
        $vehicleData['processed_at'] = now();
        
        // In dry run mode, just validate but don't save
        if ($this->dryRun) {
            return [
                'status' => 'imported',
                'vehicle' => new Vehicle($vehicleData),
                'message' => "Would import vehicle: {$vehicleData['stock_number']} (dry run)",
            ];
        }
        
        // Check if vehicle already exists by stock_number or VIN
        $existingVehicle = Vehicle::where('stock_number', $vehicleData['stock_number'])
            ->orWhere('vin', $vehicleData['vin'])
            ->first();
            
        if ($existingVehicle) {
            // Update existing vehicle
            $existingVehicle->update($vehicleData);
            return [
                'status' => 'skipped',
                'vehicle' => $existingVehicle,
                'message' => "Updated existing vehicle: {$vehicleData['stock_number']}",
            ];
        }
        
        // Create new vehicle
        $vehicle = Vehicle::create($vehicleData);
        
        return [
            'status' => 'imported',
            'vehicle' => $vehicle,
            'message' => "Imported new vehicle: {$vehicleData['stock_number']}",
        ];
    }
    
    /**
     * Map CSV fields to database fields
     *
     * @param array $data The raw CSV data
     * @return array Formatted data for model
     */
    protected function mapFieldsToModel(array $data): array
    {
        $mapped = [];
        
        // Map fields with transformations if needed
        $mappings = [
            'stocknumber' => 'stock_number',
            'vin' => 'vin',
            'year' => 'year',
            'make' => 'make',
            'model' => 'model',
            'trim' => 'trim',
            'dateinstock' => 'date_in_stock',
            'odometer' => 'odometer',
            'exteriorcolor' => 'exterior_color',
            'interiorcolor' => 'interior_color',
            'leads' => 'number_of_leads',
            'status' => 'status',
            'bodytype' => 'body_type',
            'drivetrain' => 'drive_train',
            'engine' => 'engine',
            'fueltype' => 'fuel_type',
            'isfeatured' => 'is_featured',
            'hasvideo' => 'has_video',
            'numberofpics' => 'number_of_pics',
            'purchasedfrom' => 'purchased_from',
            'purchasedate' => 'purchase_date',
            'transmission' => 'transmission',
            'transmissiontype' => 'transmission_type',
            'vehiclepurchasesource' => 'vehicle_purchase_source',
            'advertisingprice' => 'advertising_price',
            'dealstatus' => 'deal_status',
            'solddate' => 'sold_date',
            'buyername' => 'buyer_name',
        ];
        
        foreach ($mappings as $csvField => $dbField) {
            if (isset($data[$csvField])) {
                $mapped[$dbField] = $this->formatFieldValue($dbField, $data[$csvField]);
            }
        }
        
        // Handle boolean fields
        if (isset($data['isfeatured'])) {
            $mapped['is_featured'] = $this->parseBoolean($data['isfeatured']);
        }
        
        if (isset($data['hasvideo'])) {
            $mapped['has_video'] = $this->parseBoolean($data['hasvideo']);
        }
        
        return $mapped;
    }
    
    /**
     * Format field values based on their type
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @return mixed Formatted value
     */
    protected function formatFieldValue(string $field, $value)
    {
        // Handle date fields
        if (in_array($field, ['date_in_stock', 'purchase_date', 'sold_date'])) {
            if (empty(trim($value))) {
                return null; // Return null for empty dates
            }
            return date('Y-m-d', strtotime($value));
        }
        
        // Handle numeric fields
        if (in_array($field, ['year', 'odometer', 'number_of_leads', 'number_of_pics'])) {
            return !empty($value) ? (int) $value : null;
        }
        
        // Handle price field
        if ($field === 'advertising_price') {
            // Remove currency symbols and commas
            $value = preg_replace('/[^0-9.]/', '', $value);
            return !empty($value) ? (float) $value : null;
        }
        
        // For text fields, trim and set to null if empty
        $value = trim($value);
        return $value === '' ? null : $value;
    }
    
    /**
     * Parse boolean values from various formats
     * 
     * @param mixed $value The value to parse
     * @return bool The boolean value
     */
    protected function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        $trueValues = ['1', 'yes', 'true', 'y', 'on'];
        
        return in_array(strtolower((string) $value), $trueValues);
    }
    
    /**
     * Normalize CSV header names
     *
     * @param string $header Original header name
     * @return string Normalized header name
     */
    protected function normalizeHeader(string $header): string
    {
        // Remove special characters, convert to lowercase
        $normalized = preg_replace('/[^a-zA-Z0-9]/', '', $header);
        return strtolower($normalized);
    }
    
    /**
     * Send notifications to roles
     *
     * @param Vehicle $vehicle The imported vehicle
     * @param string $fileName The import file name
     * @return void
     */
    protected function sendNotifications(Vehicle $vehicle, string $fileName): void
    {
        // Get users with Sales Manager role
        $salesManagers = User::role('manager')
            ->where('email', 'like', '%sales-manager%')
            ->get();
            
        // Get users with Recon Manager role
        $reconManagers = User::role('manager')
            ->where('email', 'like', '%recon-manager%')
            ->get();
            
        // Combine the users
        $usersToNotify = $salesManagers->merge($reconManagers);
        
        if ($usersToNotify->isEmpty()) {
            $this->log('warning', "No Sales or Recon Managers found to notify about imported vehicle");
            return;
        }
        
        // Create notification
        $notification = new NewVehicleImported($vehicle, $fileName);
        
        // Send notification to each user
        Notification::send($usersToNotify, $notification);
        
        $this->log('info', "Sent notifications about new vehicle to " . $usersToNotify->count() . " managers");
    }
} 