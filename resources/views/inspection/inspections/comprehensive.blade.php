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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Inspection Assignment</h3>
                            <form id="inspection-form" method="POST" action="/inspection/vehicles/{{ $vehicle->id }}/comprehensive" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700">Manager:</label>
                                    <select id="user_id" name="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ auth()->id() == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Legend</h3>
                            <div class="flex flex-col space-y-2 mt-2">
                                <div class="flex items-center">
                                    <span class="w-4 h-4 rounded-full bg-green-500 mr-2"></span>
                                    <span class="text-sm text-gray-600">Pass - Item meets standards</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-4 h-4 rounded-full bg-yellow-500 mr-2"></span>
                                    <span class="text-sm text-gray-600">Repair - Needs minor repair</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-4 h-4 rounded-full bg-red-500 mr-2"></span>
                                    <span class="text-sm text-gray-600">Replace - Major issue requiring replacement</span>
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
                                                    <label class="inline-flex items-center">
                                                        <input type="radio" class="item-status-radio" name="items[{{ $item->id }}][status]" value="pass" data-item-id="{{ $item->id }}" 
                                                            {{ old("items.{$item->id}.status") == 'pass' ? 'checked' : '' }} required form="inspection-form">
                                                        <span class="ml-2 text-sm text-gray-900 bg-green-100 px-2 py-1 rounded-full">Pass</span>
                                                    </label>
                                                    <label class="inline-flex items-center">
                                                        <input type="radio" class="item-status-radio" name="items[{{ $item->id }}][status]" value="warning" data-item-id="{{ $item->id }}" 
                                                            {{ old("items.{$item->id}.status") == 'warning' ? 'checked' : '' }} form="inspection-form">
                                                        <span class="ml-2 text-sm text-gray-900 bg-yellow-100 px-2 py-1 rounded-full">Warning</span>
                                                    </label>
                                                    <label class="inline-flex items-center">
                                                        <input type="radio" class="item-status-radio" name="items[{{ $item->id }}][status]" value="fail" data-item-id="{{ $item->id }}" 
                                                            {{ old("items.{$item->id}.status") == 'fail' ? 'checked' : '' }} form="inspection-form">
                                                        <span class="ml-2 text-sm text-gray-900 bg-red-100 px-2 py-1 rounded-full">Fail</span>
                                                    </label>
                                                </div>
                                                
                                                <!-- Notes field -->
                                                <div class="w-full">
                                                    <textarea name="items[{{ $item->id }}][notes]" rows="2" form="inspection-form"
                                                        class="w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                        placeholder="Add inspection notes...">{{ old("items.{$item->id}.notes") }}</textarea>
                                                </div>
                                                
                                                <!-- Vendor field - conditionally shown -->
                                                @if($item->vendor_required)
                                                <div id="vendor-field-{{ $item->id }}" class="w-full hidden">
                                                    <label for="vendor_{{ $item->id }}" class="block text-xs font-medium text-gray-700 mb-1">Select Vendor:</label>
                                                    <select id="vendor_{{ $item->id }}" name="items[{{ $item->id }}][vendor_id]" form="inspection-form"
                                                        class="w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm item-vendor-select">
                                                        <option value="">Select vendor (optional)</option>
                                                        @foreach($vendors as $vendor)
                                                            <option value="{{ $vendor->id }}" {{ old("items.{$item->id}.vendor_id") == $vendor->id ? 'selected' : '' }}>
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
                                                            value="{{ old("items.{$item->id}.cost") }}" 
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
            // Tab navigation logic
            const stageTabs = document.querySelectorAll('.stage-tab');
            const stageContents = document.querySelectorAll('.stage-content');
            const prevButton = document.getElementById('prev-stage');
            const nextButton = document.getElementById('next-stage');
            let currentStageIndex = 0;
            
            function showStage(index) {
                stageTabs.forEach(tab => tab.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600'));
                stageTabs.forEach(tab => tab.classList.add('text-gray-500', 'hover:text-gray-700'));
                stageContents.forEach(content => content.classList.add('hidden'));
                
                stageTabs[index].classList.remove('text-gray-500', 'hover:text-gray-700');
                stageTabs[index].classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
                stageContents[index].classList.remove('hidden');
                
                // Update buttons
                prevButton.disabled = index === 0;
                nextButton.disabled = index === stageTabs.length - 1;
                
                currentStageIndex = index;
            }
            
            stageTabs.forEach((tab, index) => {
                tab.addEventListener('click', () => showStage(index));
            });
            
            prevButton.addEventListener('click', () => {
                if (currentStageIndex > 0) {
                    showStage(currentStageIndex - 1);
                }
            });
            
            nextButton.addEventListener('click', () => {
                if (currentStageIndex < stageTabs.length - 1) {
                    showStage(currentStageIndex + 1);
                }
            });
            
            // Initialize
            showStage(0);
            
            // Form validation
            const form = document.getElementById('inspection-form');
            form.addEventListener('submit', function(e) {
                // Validate that at least one radio button is selected for each item
                const itemGroups = document.querySelectorAll('[name^="items["]');
                const groups = {};
                
                itemGroups.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name.includes('[status]')) {
                        const group = name;
                        if (!groups[group]) {
                            groups[group] = {
                                selected: false,
                                elements: []
                            };
                        }
                        
                        if (input.checked) {
                            groups[group].selected = true;
                        }
                        
                        groups[group].elements.push(input);
                    }
                });
                
                // Check if all groups have a selection
                let valid = true;
                let firstInvalidTab = null;
                
                Object.entries(groups).forEach(([groupName, group]) => {
                    if (!group.selected) {
                        valid = false;
                        
                        // Highlight the group
                        const element = group.elements[0];
                        const itemContainer = element.closest('.border');
                        itemContainer.classList.add('border-red-500', 'bg-red-50/30');
                        
                        // Find which tab contains this invalid item
                        const stageContent = element.closest('.stage-content');
                        const stageId = stageContent.id.replace('stage-', '');
                        
                        if (!firstInvalidTab) {
                            firstInvalidTab = Array.from(stageTabs).findIndex(tab => 
                                tab.getAttribute('data-stage-id') === stageId
                            );
                        }
                    } else {
                        const element = group.elements[0];
                        const itemContainer = element.closest('.border');
                        itemContainer.classList.remove('border-red-500', 'bg-red-50/30');
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                    alert('Please complete the assessment for all inspection items.');
                    
                    // Navigate to the first tab with an error
                    if (firstInvalidTab !== null) {
                        showStage(firstInvalidTab);
                    }
                    
                    window.scrollTo(0, 0);
                }
            });

            // Handle batch vendor assignment
            document.getElementById('batch-assign-vendor').addEventListener('click', function() {
                const globalVendorId = document.getElementById('vendor_id').value;
                if (!globalVendorId) {
                    alert('Please select a vendor first');
                    return;
                }
                
                // Find all individual vendor selects and set them to the global vendor
                const vendorSelects = document.querySelectorAll('.item-vendor-select');
                vendorSelects.forEach(select => {
                    select.value = globalVendorId;
                });
                
                alert('Vendor assigned to all repair items');
            });

            // Toggle vendor and cost fields based on item status
            document.querySelectorAll('.item-status-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const itemId = this.getAttribute('data-item-id');
                    const status = this.value;
                    const vendorField = document.getElementById(`vendor-field-${itemId}`);
                    const costField = document.getElementById(`cost-field-${itemId}`);
                    
                    if (status === 'warning' || status === 'fail') {
                        // Show vendor and cost fields for repairs
                        if (vendorField) vendorField.classList.remove('hidden');
                        if (costField) costField.classList.remove('hidden');
                    } else {
                        // Hide vendor and cost fields for passing items
                        if (vendorField) vendorField.classList.add('hidden');
                        if (costField) costField.classList.add('hidden');
                    }
                });
            });
            
            // Initialize fields visibility based on current status
            document.querySelectorAll('.item-status-radio:checked').forEach(radio => {
                const itemId = radio.getAttribute('data-item-id');
                const status = radio.value;
                const vendorField = document.getElementById(`vendor-field-${itemId}`);
                const costField = document.getElementById(`cost-field-${itemId}`);
                
                if (status === 'warning' || status === 'fail') {
                    if (vendorField) vendorField.classList.remove('hidden');
                    if (costField) costField.classList.remove('hidden');
                } else {
                    if (vendorField) vendorField.classList.add('hidden');
                    if (costField) costField.classList.add('hidden');
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 