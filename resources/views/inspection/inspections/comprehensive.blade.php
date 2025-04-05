<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vehicle Inspection') }}: {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
            </h2>
            <a href="{{ route('vehicles.show', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to Vehicle
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Vehicle Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Vehicle Information</h3>
                            <p class="text-gray-600">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</p>
                            <p class="text-gray-600">VIN: {{ $vehicle->vin }}</p>
                            <p class="text-gray-600">Stock #: {{ $vehicle->stock_number }}</p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $vehicle->status === 'arrived' ? 'bg-green-100 text-green-800' : 
                                    ($vehicle->status === 'delivered' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') 
                                }}">
                                    Vehicle Status: {{ ucfirst($vehicle->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Legend</h3>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="flex items-center bg-green-50 p-4 rounded-lg border border-green-200 shadow-sm hover:shadow transition-all duration-200">
                                    <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full ring-2 ring-green-500 ring-opacity-25 mr-3"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-green-700">Pass</p>
                                        <p class="text-xs text-green-600">No issues found</p>
                                    </div>
                                </div>
                                <div class="flex items-center bg-yellow-50 p-4 rounded-lg border border-yellow-200 shadow-sm hover:shadow transition-all duration-200">
                                    <div class="flex-shrink-0 w-3 h-3 bg-yellow-500 rounded-full ring-2 ring-yellow-500 ring-opacity-25 mr-3"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-yellow-700">Repair</p>
                                        <p class="text-xs text-yellow-600">Needs repair</p>
                                    </div>
                                </div>
                                <div class="flex items-center bg-red-50 p-4 rounded-lg border border-red-200 shadow-sm hover:shadow transition-all duration-200">
                                    <div class="flex-shrink-0 w-3 h-3 bg-red-500 rounded-full ring-2 ring-red-500 ring-opacity-25 mr-3"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-red-700">Replace</p>
                                        <p class="text-xs text-red-600">Needs replacement</p>
                                    </div>
                                </div>
                                <div class="flex items-center bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm hover:shadow transition-all duration-200">
                                    <div class="flex-shrink-0 w-3 h-3 bg-gray-400 rounded-full ring-2 ring-gray-400 ring-opacity-25 mr-3"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">Pending</p>
                                        <p class="text-xs text-gray-600">Not inspected yet</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Manager Inspection Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Manager Inspection</h3>
                    <p class="mb-4 text-gray-700">First, complete your inspection below. After submitting, you'll be able to assign repairs to vendors.</p>
                
                    <form id="inspection-form" method="POST" action="/inspection/vehicles/{{ $vehicle->id }}/comprehensive" class="space-y-4" enctype="multipart/form-data">
                        @csrf
                        @if(isset($existingInspection))
                            @method('PUT')
                        @endif
                        <!-- Stages Tabs -->
                        <div class="mb-6 border-b border-gray-200">
                            <div class="flex overflow-x-auto">
                                @foreach($stages as $index => $stage)
                                    <button type="button" 
                                            class="stage-tab px-4 py-2 text-sm font-medium {{ $index === 0 ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}"
                                            data-stage-id="{{ $stage->id }}">
                                        {{ $stage->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    
                        <!-- Stage Content -->
                        @foreach($stages as $index => $stage)
                            <div id="stage-{{ $stage->id }}" class="stage-content {{ $index === 0 ? 'block' : 'hidden' }}">
                                <div class="space-y-6">
                                    @foreach($stage->inspectionItems as $item)
                                        <!-- Template for each inspection item -->
                                        <div class="border-b border-gray-200 mb-4 pb-4 item-container" data-stage-id="{{ $item->inspectionStage->id }}">
                                            <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-4">
                                                <div class="flex-grow">
                                                    <div class="flex flex-col">
                                                        <div class="font-semibold text-gray-900">{{ $item->name }}</div>
                                                        @if($item->description)
                                                            <div class="text-sm text-gray-600 mt-1">{{ $item->description }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-3 lg:mt-0 space-y-3">
                                                    <!-- Assessment Status -->
                                                    <div class="flex flex-wrap gap-4">
                                                        <label class="relative flex items-center group">
                                                            <input type="radio" 
                                                                class="peer sr-only item-status-radio" 
                                                                name="items[{{ $item->id }}][status]" 
                                                                value="pass" 
                                                                data-item-id="{{ $item->id }}"
                                                                {{ isset($existingInspection) && $existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->status === 'pass' ? 'checked' : '' }}
                                                                {{ old("items.{$item->id}.status") == 'pass' ? 'checked' : '' }} 
                                                                form="inspection-form">
                                                            <div class="flex items-center px-6 py-2.5 rounded-lg border-2 cursor-pointer
                                                                text-sm font-medium transition-all duration-200
                                                                border-green-200 text-green-700 bg-green-50
                                                                peer-checked:bg-green-100 peer-checked:border-green-500
                                                                hover:bg-green-100 hover:border-green-300
                                                                group-hover:scale-102 transform">
                                                                <div class="w-3 h-3 bg-green-500 rounded-full ring-2 ring-green-500 ring-opacity-25 mr-3"></div>
                                                                Pass
                                                            </div>
                                                        </label>
                                                        <label class="relative flex items-center group">
                                                            <input type="radio" 
                                                                class="peer sr-only item-status-radio" 
                                                                name="items[{{ $item->id }}][status]" 
                                                                value="warning" 
                                                                data-item-id="{{ $item->id }}"
                                                                {{ isset($existingInspection) && $existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->status === 'warning' ? 'checked' : '' }}
                                                                {{ old("items.{$item->id}.status") == 'warning' ? 'checked' : '' }} 
                                                                form="inspection-form">
                                                            <div class="flex items-center px-6 py-2.5 rounded-lg border-2 cursor-pointer
                                                                text-sm font-medium transition-all duration-200
                                                                border-yellow-200 text-yellow-700 bg-yellow-50
                                                                peer-checked:bg-yellow-100 peer-checked:border-yellow-500
                                                                hover:bg-yellow-100 hover:border-yellow-300
                                                                group-hover:scale-102 transform">
                                                                <div class="w-3 h-3 bg-yellow-500 rounded-full ring-2 ring-yellow-500 ring-opacity-25 mr-3"></div>
                                                                Repair
                                                            </div>
                                                        </label>
                                                        <label class="relative flex items-center group">
                                                            <input type="radio" 
                                                                class="peer sr-only item-status-radio" 
                                                                name="items[{{ $item->id }}][status]" 
                                                                value="fail" 
                                                                data-item-id="{{ $item->id }}"
                                                                {{ isset($existingInspection) && $existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->status === 'fail' ? 'checked' : '' }}
                                                                {{ old("items.{$item->id}.status") == 'fail' ? 'checked' : '' }} 
                                                                form="inspection-form">
                                                            <div class="flex items-center px-6 py-2.5 rounded-lg border-2 cursor-pointer
                                                                text-sm font-medium transition-all duration-200
                                                                border-red-200 text-red-700 bg-red-50
                                                                peer-checked:bg-red-100 peer-checked:border-red-500
                                                                hover:bg-red-100 hover:border-red-300
                                                                group-hover:scale-102 transform">
                                                                <div class="w-3 h-3 bg-red-500 rounded-full ring-2 ring-red-500 ring-opacity-25 mr-3"></div>
                                                                Replace
                                                            </div>
                                                        </label>
                                                    </div>
                                                    
                                                    <!-- Notes field -->
                                                    <div class="w-full">
                                                        <textarea name="items[{{ $item->id }}][notes]" rows="2" form="inspection-form"
                                                            class="w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                            placeholder="Add inspection notes...">{{ isset($existingInspection) ? ($existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->notes ?? '') : old("items.{$item->id}.notes") }}</textarea>
                                                    </div>

                                                    <!-- Image Upload -->
                                                    <div class="w-full">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                                            Upload Images
                                                            <span class="text-gray-500">(Optional)</span>
                                                        </label>
                                                        <div class="mt-1 flex items-center gap-2">
                                                            <input type="file" 
                                                                name="items[{{ $item->id }}][images][]" 
                                                                accept="image/*" 
                                                                multiple
                                                                class="block w-full text-sm text-gray-500
                                                                    file:mr-4 file:py-2 file:px-4
                                                                    file:rounded-md file:border-0
                                                                    file:text-sm file:font-semibold
                                                                    file:bg-indigo-50 file:text-indigo-700
                                                                    hover:file:bg-indigo-100"
                                                            >
                                                        </div>
                                                        <p class="mt-1 text-xs text-gray-500">
                                                            You can upload multiple images. Supported formats: JPG, PNG (max 5MB each)
                                                        </p>
                                                    </div>
                                                    
                                                    <!-- Vendor field - conditionally shown -->
                                                    @if($item->vendor_required)
                                                    <div id="vendor-field-{{ $item->id }}" class="w-full hidden">
                                                        <label for="vendor_{{ $item->id }}" class="block text-xs font-medium text-gray-700 mb-1">Select Vendor:</label>
                                                        <select id="vendor_{{ $item->id }}" name="items[{{ $item->id }}][vendor_id]" form="inspection-form"
                                                            class="w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm item-vendor-select">
                                                            <option value="">Select vendor (optional)</option>
                                                            @foreach($vendors as $vendor)
                                                                <option value="{{ $vendor->id }}" 
                                                                    {{ isset($existingInspection) && $existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->vendor_id == $vendor->id ? 'selected' : '' }}
                                                                    {{ old("items.{$item->id}.vendor_id") == $vendor->id ? 'selected' : '' }}>
                                                                    {{ $vendor->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @endif
                                                    
                                                    <!-- Cost field - conditionally shown -->
                                                    @if($item->cost_tracking)
                                                    <div id="cost-field-{{ $item->id }}" class="w-full hidden">
                                                        <label for="cost_{{ $item->id }}" class="block text-xs font-medium text-gray-700 mb-1">Estimated Cost:</label>
                                                        <div class="relative rounded-md shadow-sm">
                                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                                <span class="text-gray-500 sm:text-sm">$</span>
                                                            </div>
                                                            <input type="number" step="0.01" min="0" id="cost_{{ $item->id }}" name="items[{{ $item->id }}][cost]" form="inspection-form"
                                                                value="{{ isset($existingInspection) ? ($existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->cost ?? '') : old("items.{$item->id}.cost") }}" 
                                                                class="w-full pl-7 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </form>
                </div>
            </div>

            <!-- Vendor Assignment Section -->
            <div class="mt-8 p-4 bg-white border border-gray-200 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    <span class="text-blue-600"><i class="fas fa-tools mr-2"></i>Vendor Assignment</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Assign All Repairs to Vendor
                        </label>
                        <select id="vendor_id" name="vendor_id" form="inspection-form"
                            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select a vendor (optional)</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            This will assign all repair items to the selected vendor. You can also assign specific vendors to individual repair items below.
                        </p>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="button" id="batch-assign-vendor" class="px-4 py-2 bg-green-600 text-white rounded-md shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <i class="fas fa-user-plus mr-2"></i> Assign Selected Vendor to All Items
                        </button>
                    </div>
                </div>
                
                <div class="text-sm bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-yellow-700">
                                <strong>Note:</strong> For items that require repairs, you can assign a specific vendor to each item by using the vendor dropdown next to each failing item. If you don't assign a specific vendor, the global vendor above will be used (if selected).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation/Action Buttons -->
            <div class="flex justify-between mt-6">
                <div class="flex space-x-2">
                    <button type="button" id="prev-stage" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                        Previous Stage
                    </button>
                    <button type="button" id="next-stage" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Next Stage
                        <x-heroicon-o-arrow-right class="h-4 w-4 ml-1" />
                    </button>
                </div>
                
                <button type="submit" form="inspection-form" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-check-circle class="h-4 w-4 mr-1" />
                    Complete Inspection & Assign Vendors
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle tab switching
            const tabs = document.querySelectorAll('.stage-tab');
            const contents = document.querySelectorAll('.stage-content');
            
            // Add batch assign vendor functionality
            const batchAssignVendorBtn = document.getElementById('batch-assign-vendor');
            const globalVendorSelect = document.getElementById('vendor_id');
            
            if (batchAssignVendorBtn && globalVendorSelect) {
                batchAssignVendorBtn.addEventListener('click', function() {
                    const selectedVendorId = globalVendorSelect.value;
                    if (!selectedVendorId) {
                        alert('Please select a vendor first.');
                        return;
                    }
                    
                    // Update all vendor selects that are visible (items marked as warning or fail)
                    document.querySelectorAll('.item-vendor-select').forEach(select => {
                        const container = select.closest('.item-container');
                        const statusInputs = container.querySelectorAll('input[type="radio"]');
                        const selectedStatus = Array.from(statusInputs).find(input => input.checked)?.value;
                        
                        if (selectedStatus === 'warning' || selectedStatus === 'fail') {
                            select.value = selectedVendorId;
                            select.closest('div').classList.remove('hidden');
                        }
                    });
                });
            }
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const stageId = tab.dataset.stageId;
                    
                    // Update tab styles
                    tabs.forEach(t => t.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600'));
                    tab.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
                    
                    // Show/hide content
                    contents.forEach(content => {
                        if (content.id === `stage-${stageId}`) {
                            content.classList.remove('hidden');
                        } else {
                            content.classList.add('hidden');
                        }
                    });
                });
            });

            // Handle form submission
            const form = document.getElementById('inspection-form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset error states
                const errorItems = document.querySelectorAll('.error-highlight');
                errorItems.forEach(item => item.classList.remove('error-highlight'));
                
                let hasErrors = false;
                let firstErrorTab = null;
                
                // Check each item container
                document.querySelectorAll('.item-container').forEach(container => {
                    const radios = container.querySelectorAll('input[type="radio"]');
                    if (!radios.length) return; // Skip if no radio buttons found
                    
                    const itemId = radios[0].dataset.itemId; // Get itemId from first radio
                    if (!itemId) return; // Skip if no itemId found
                    
                    const checked = Array.from(radios).some(radio => radio.checked);
                    
                    if (!checked) {
                        container.classList.add('error-highlight');
                        hasErrors = true;
                        
                        if (!firstErrorTab) {
                            const stageContent = container.closest('.stage-content');
                            if (stageContent) {
                                firstErrorTab = stageContent.id.replace('stage-', '');
                            }
                        }
                    }
                });
                
                if (hasErrors) {
                    // Switch to the first tab with errors
                    if (firstErrorTab) {
                        document.querySelector(`.stage-tab[data-stage-id="${firstErrorTab}"]`).click();
                    }
                    
                    alert('Please complete all inspection items before submitting.');
                    return;
                }
                
                // Submit the form if no errors
                form.submit();
            });

            // Handle status radio changes
            document.querySelectorAll('.item-status-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const itemId = this.dataset.itemId;
                    const container = this.closest('.item-container');
                    const vendorField = container.querySelector(`#vendor-field-${itemId}`);
                    const costField = container.querySelector(`#cost-field-${itemId}`);
                    
                    // Remove error highlight if it exists
                    container.classList.remove('error-highlight');
                    
                    // Show/hide vendor and cost fields based on status
                    if (vendorField) {
                        vendorField.classList.toggle('hidden', this.value === 'pass');
                    }
                    
                    if (costField) {
                        costField.classList.toggle('hidden', this.value === 'pass');
                    }
                });
            });
        });
    </script>
    
    <style>
        .error-highlight {
            border-color: #ef4444;
            background-color: #fee2e2;
            padding: 1rem;
            border-radius: 0.375rem;
        }
    </style>
    @endpush
</x-app-layout> 