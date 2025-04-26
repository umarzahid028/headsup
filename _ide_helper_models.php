<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $batch_number
 * @property string|null $name
 * @property string $status
 * @property int|null $transporter_id
 * @property \Illuminate\Support\Carbon|null $scheduled_pickup_date
 * @property \Illuminate\Support\Carbon|null $scheduled_delivery_date
 * @property \Illuminate\Support\Carbon|null $pickup_date
 * @property \Illuminate\Support\Carbon|null $delivery_date
 * @property string|null $origin
 * @property string $destination
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GatePass> $gatePasses
 * @property-read int|null $gate_passes_count
 * @property-read string $full_name
 * @property-read int $vehicle_count
 * @property-read \App\Models\Transporter|null $transporter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transport> $transports
 * @property-read int|null $transports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vehicle> $vehicles
 * @property-read int|null $vehicles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereBatchNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch wherePickupDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereScheduledDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereScheduledPickupDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereTransporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Batch whereUpdatedAt($value)
 */
	class Batch extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $vendor_id
 * @property string|null $reference_number
 * @property numeric $estimated_cost
 * @property string|null $description
 * @property string $status
 * @property int|null $approved_by_user_id
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $rejected_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereApprovedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereEstimatedCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereRejectedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estimate withoutTrashed()
 */
	class Estimate extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $pass_number
 * @property int $vehicle_id
 * @property int|null $transporter_id
 * @property int|null $batch_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $issue_date
 * @property \Illuminate\Support\Carbon|null $expiry_date
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property string|null $authorized_by
 * @property string|null $file_path
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Batch|null $batch
 * @property-read \App\Models\Transporter|null $transporter
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereAuthorizedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass wherePassNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereTransporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GatePass whereVehicleId($value)
 */
	class GatePass extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int|null $sales_issue_id
 * @property int $created_by_user_id
 * @property string $customer_name
 * @property string $customer_phone
 * @property string|null $customer_email
 * @property string $issue_description
 * @property string $requested_resolution
 * @property bool $customer_consent
 * @property \Illuminate\Support\Carbon|null $customer_consent_date
 * @property string|null $customer_signature
 * @property bool $signed_in_person
 * @property \Illuminate\Support\Carbon|null $signature_date
 * @property string $status
 * @property int|null $approved_by_user_id
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $approval_notes
 * @property numeric|null $estimated_cost
 * @property numeric|null $actual_cost
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\SalesIssue|null $salesIssue
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereActualCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereApprovalNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereApprovedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereCreatedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereCustomerConsent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereCustomerConsentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereCustomerSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereEstimatedCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereIssueDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereRequestedResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereSalesIssueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereSignatureDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereSignedInPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GoodwillClaim withoutTrashed()
 */
	class GoodwillClaim extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $inspection_stage_id
 * @property int $order
 * @property int|null $vendor_id
 * @property string|null $estimated_cost
 * @property string|null $actual_cost
 * @property string|null $notes
 * @property string|null $completion_notes
 * @property string|null $photos
 * @property string|null $estimate_submitted_at
 * @property string|null $completed_at
 * @property bool $vendor_required
 * @property bool $cost_tracking
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InspectionStage $inspectionStage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InspectionItemResult> $itemResults
 * @property-read int|null $item_results_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereActualCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereCompletionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereCostTracking($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereEstimateSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereEstimatedCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereInspectionStageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItem whereVendorRequired($value)
 */
	class InspectionItem extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $vehicle_inspection_id
 * @property int $inspection_item_id
 * @property string|null $status
 * @property string|null $notes
 * @property string|null $completion_notes
 * @property string|null $photo_path
 * @property numeric $cost
 * @property numeric|null $actual_cost
 * @property bool $requires_repair
 * @property bool $repair_completed
 * @property string|null $diagnostic_status
 * @property bool $is_vendor_visible
 * @property \Illuminate\Support\Carbon|null $assigned_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property int|null $vendor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Vendor|null $assignedVendor
 * @property-read \App\Models\InspectionItem $inspectionItem
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RepairImage> $repairImages
 * @property-read int|null $repair_images_count
 * @property-read \App\Models\VehicleInspection $vehicleInspection
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VendorEstimate> $vendorEstimates
 * @property-read int|null $vendor_estimates_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereActualCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereCompletionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereDiagnosticStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereInspectionItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereIsVendorVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereRepairCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereRequiresRepair($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereVehicleInspectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionItemResult whereVendorId($value)
 */
	class InspectionItemResult extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InspectionItem> $inspectionItems
 * @property-read int|null $inspection_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VehicleInspection> $vehicleInspections
 * @property-read int|null $vehicle_inspections_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InspectionStage whereUpdatedAt($value)
 */
	class InspectionStage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $inspection_item_result_id
 * @property string $image_path
 * @property string $image_type
 * @property string|null $caption
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $image_url
 * @property-read \App\Models\InspectionItemResult $itemResult
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairImage whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairImage whereImageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairImage whereInspectionItemResultId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RepairImage whereUpdatedAt($value)
 */
	class RepairImage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $user_id
 * @property numeric $amount
 * @property string $customer_name
 * @property string|null $customer_email
 * @property string|null $customer_phone
 * @property string|null $notes
 * @property string $status
 * @property \Illuminate\Support\Carbon $sale_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereSaleDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale withoutTrashed()
 */
	class Sale extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $reported_by_user_id
 * @property string $issue_type
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property int|null $reviewed_by_user_id
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property string|null $review_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\GoodwillClaim|null $goodwillClaim
 * @property-read \App\Models\User $reportedBy
 * @property-read \App\Models\User|null $reviewedBy
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereIssueType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereReportedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereReviewNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereReviewedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesIssue withoutTrashed()
 */
	class SalesIssue extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property string|null $phone
 * @property string $position
 * @property string|null $bio
 * @property string|null $photo_path
 * @property bool $is_active
 * @property int|null $manager_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $photo_url
 * @property-read \App\Models\User|null $manager
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam wherePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesTeam withoutTrashed()
 */
	class SalesTeam extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int|null $transporter_id
 * @property string|null $batch_id
 * @property string|null $origin
 * @property string $destination
 * @property \Illuminate\Support\Carbon|null $pickup_date
 * @property \Illuminate\Support\Carbon|null $delivery_date
 * @property string $status
 * @property bool $is_acknowledged
 * @property \Illuminate\Support\Carbon|null $acknowledged_at
 * @property int|null $acknowledged_by
 * @property string|null $transporter_name
 * @property string|null $transporter_phone
 * @property string|null $transporter_email
 * @property string|null $notes
 * @property string|null $batch_name
 * @property string|null $gate_pass_path
 * @property string|null $qr_code_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $acknowledgedBy
 * @property-read \App\Models\Batch|null $batch
 * @property-read \App\Models\Transporter|null $transporter
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereAcknowledgedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereAcknowledgedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereBatchName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereGatePassPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereIsAcknowledged($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport wherePickupDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereQrCodePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereTransporterEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereTransporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereTransporterName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereTransporterPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transport whereVehicleId($value)
 */
	class Transport extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $contact_person
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $notes
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Batch> $batches
 * @property-read int|null $batches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GatePass> $gatePasses
 * @property-read int|null $gate_passes_count
 * @property-read string $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transport> $transports
 * @property-read int|null $transports_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transporter whereZip($value)
 */
	class Transporter extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property \App\Enums\Role|null $role
 * @property string|null $vendor_type
 * @property bool $is_active
 * @property string|null $remember_token
 * @property int|null $transporter_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VehicleInspection> $assignedInspections
 * @property-read int|null $assigned_inspections_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\SalesTeam|null $salesTeam
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\Transporter|null $transporter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VehicleRead> $vehicleReads
 * @property-read int|null $vehicle_reads_count
 * @property-read \App\Models\Vendor|null $vendor
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTransporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVendorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $stock_number
 * @property string $vin
 * @property int|null $year
 * @property string|null $make
 * @property string|null $model
 * @property string|null $trim
 * @property \Illuminate\Support\Carbon|null $date_in_stock
 * @property int|null $odometer
 * @property string|null $exterior_color
 * @property string|null $interior_color
 * @property int|null $number_of_leads
 * @property string|null $status
 * @property int|null $sales_team_id
 * @property int|null $assigned_for_sale_by
 * @property \Illuminate\Support\Carbon|null $assigned_for_sale_at
 * @property string|null $transport_status
 * @property string|null $body_type
 * @property string|null $drive_train
 * @property string|null $engine
 * @property string|null $fuel_type
 * @property bool $is_featured
 * @property bool $has_video
 * @property int|null $number_of_pics
 * @property string|null $image_path
 * @property string|null $purchased_from
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property string|null $transmission
 * @property string|null $transmission_type
 * @property string|null $vehicle_purchase_source
 * @property numeric|null $advertising_price
 * @property string|null $deal_status
 * @property \Illuminate\Support\Carbon|null $sold_date
 * @property string|null $buyer_name
 * @property string|null $import_file
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GatePass> $gatePasses
 * @property-read int|null $gate_passes_count
 * @property-read bool $has_main_image
 * @property-read string $image_url
 * @property-read array $image_urls
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VehicleImage> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\Sale|null $sale
 * @property-read \App\Models\User|null $salesTeam
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transport> $transports
 * @property-read int|null $transports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VehicleInspection> $vehicleInspections
 * @property-read int|null $vehicle_inspections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VehicleRead> $vehicleReads
 * @property-read int|null $vehicle_reads_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle archived()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle assignedToSales()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle byStatusCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle inGoodwillClaimsProcess()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle inInspectionProcess()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle inRepairProcess()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle inSalesProcess()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle inTransportProcess()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle readyForSale()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle readyForSalesAssignment()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle sold()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereAdvertisingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereAssignedForSaleAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereAssignedForSaleBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereBodyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereBuyerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereDateInStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereDealStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereDriveTrain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereEngine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereExteriorColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereFuelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereHasVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereImportFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereInteriorColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereMake($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereNumberOfLeads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereNumberOfPics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle wherePurchasedFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereSalesTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereSoldDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereStockNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereTransmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereTransmissionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereTransportStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereTrim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereVehiclePurchaseSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereVin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle whereYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vehicle withoutTrashed()
 */
	class Vehicle extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $vehicle_id
 * @property string $image_url
 * @property string|null $title
 * @property string|null $description
 * @property int $sort_order
 * @property bool $is_featured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleImage whereVehicleId($value)
 */
	class VehicleImage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $vehicle_id
 * @property int $inspection_stage_id
 * @property int $user_id
 * @property int|null $vendor_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $inspection_date
 * @property \Illuminate\Support\Carbon|null $completed_date
 * @property string|null $notes
 * @property numeric $total_cost
 * @property array<array-key, mixed>|null $meta_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InspectionItemResult> $inspectionItems
 * @property-read int|null $inspection_items_count
 * @property-read \App\Models\InspectionStage $inspectionStage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InspectionItemResult> $itemResults
 * @property-read int|null $item_results_count
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Vehicle $vehicle
 * @property-read \App\Models\Vendor|null $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereCompletedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereInspectionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereInspectionStageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereMetaData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereTotalCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleInspection whereVendorId($value)
 */
	class VehicleInspection extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $vehicle_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Vehicle $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleRead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleRead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleRead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleRead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleRead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleRead whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleRead whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VehicleRead whereVehicleId($value)
 */
	class VehicleRead extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $vendor_type_id
 * @property string $name
 * @property string|null $contact_person
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property array<array-key, mixed>|null $specialty_tags
 * @property bool $is_active
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $type_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InspectionItemResult> $inspectionItemResults
 * @property-read int|null $inspection_item_results_count
 * @property-read \App\Models\VendorType|null $type
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VehicleInspection> $vehicleInspections
 * @property-read int|null $vehicle_inspections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VendorEstimate> $vendorEstimates
 * @property-read int|null $vendor_estimates_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereSpecialtyTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorTypeId($value)
 */
	class Vendor extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $vendor_id
 * @property int $inspection_item_result_id
 * @property numeric $estimated_cost
 * @property string|null $description
 * @property string $status
 * @property int|null $approved_by_user_id
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property string|null $rejected_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\InspectionItemResult $inspectionItemResult
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereApprovedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereEstimatedCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereInspectionItemResultId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereRejectedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorEstimate whereVendorId($value)
 */
	class VendorEstimate extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_on_site
 * @property bool $has_system_access
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vendor> $vendors
 * @property-read int|null $vendors_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereHasSystemAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereIsOnSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorType whereUpdatedAt($value)
 */
	class VendorType extends \Eloquent {}
}

