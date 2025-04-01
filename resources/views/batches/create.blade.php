<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Create New Batch') }}
            </h2>
            <x-button href="{{ route('batches.index') }}" variant="outline">
                <x-heroicon-o-arrow-left class="w-5 h-5 mr-1" />
                {{ __('Back to Batches') }}
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('batches.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                            <!-- Batch Name -->
                            <div>
                                <x-input-label for="name" :value="__('Batch Name (Optional)')" />
                                <x-input id="name" name="name" type="text" class="block w-full mt-1" :value="old('name')" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Transporter -->
                            <div>
                                <x-input-label for="transporter_id" :value="__('Transporter')" />
                                <x-select id="transporter_id" name="transporter_id" class="block w-full mt-1">
                                    <option value="">{{ __('Select Transporter') }}</option>
                                    @foreach($transporters as $transporter)
                                        <option value="{{ $transporter->id }}" {{ old('transporter_id') == $transporter->id ? 'selected' : '' }}>
                                            {{ $transporter->name }}
                                        </option>
                                    @endforeach
                                </x-select>
                                <x-input-error :messages="$errors->get('transporter_id')" class="mt-2" />
                            </div>
                            
                            <!-- Origin -->
                            <div>
                                <x-input-label for="origin" :value="__('Origin')" />
                                <x-input id="origin" name="origin" type="text" class="block w-full mt-1" :value="old('origin')" />
                                <x-input-error :messages="$errors->get('origin')" class="mt-2" />
                            </div>

                            <!-- Destination -->
                            <div>
                                <x-input-label for="destination" :value="__('Destination')" />
                                <x-input id="destination" name="destination" type="text" class="block w-full mt-1" :value="old('destination')" required />
                                <x-input-error :messages="$errors->get('destination')" class="mt-2" />
                            </div>

                            <!-- Scheduled Pickup Date -->
                            <div>
                                <x-input-label for="scheduled_pickup_date" :value="__('Scheduled Pickup Date')" />
                                <x-input id="scheduled_pickup_date" name="scheduled_pickup_date" type="date" class="block w-full mt-1" :value="old('scheduled_pickup_date')" />
                                <x-input-error :messages="$errors->get('scheduled_pickup_date')" class="mt-2" />
                            </div>

                            <!-- Scheduled Delivery Date -->
                            <div>
                                <x-input-label for="scheduled_delivery_date" :value="__('Scheduled Delivery Date')" />
                                <x-input id="scheduled_delivery_date" name="scheduled_delivery_date" type="date" class="block w-full mt-1" :value="old('scheduled_delivery_date')" />
                                <x-input-error :messages="$errors->get('scheduled_delivery_date')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <x-select id="status" name="status" class="block w-full mt-1" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="in_transit" {{ old('status') == 'in_transit' ? 'selected' : '' }}>{{ __('In Transit') }}</option>
                                    <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                </x-select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <x-input-label for="notes" :value="__('Notes')" />
                                <textarea id="notes" name="notes" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Vehicle Selection -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-2">
                                <x-input-label for="vehicles" :value="__('Select Vehicles')" class="mb-2 text-lg" />
                                <div class="flex items-center">
                                    <x-input id="vehicleSearch" type="text" placeholder="Search vehicles..." class="w-64" onkeyup="filterVehicles()" />
                                    <button type="button" onclick="selectAllVehicles()" class="px-3 py-2 ml-2 text-xs text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                        {{ __('Select All') }}
                                    </button>
                                    <button type="button" onclick="deselectAllVehicles()" class="px-3 py-2 ml-2 text-xs text-white bg-gray-500 rounded-md hover:bg-gray-600">
                                        {{ __('Deselect All') }}
                                    </button>
                                </div>
                            </div>
                            
                            @if($vehicles->isEmpty())
                                <div class="p-4 mt-2 text-sm italic text-center text-gray-500 border border-gray-200 rounded-md">
                                    {{ __('No available vehicles found. All vehicles may already be assigned to other batches.') }}
                                </div>
                            @else
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach($vehicles as $vehicle)
                                        <div class="vehicle-item p-4 border border-gray-200 rounded-md hover:bg-gray-50">
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="vehicle_{{ $vehicle->id }}" name="vehicle_ids[]" type="checkbox" value="{{ $vehicle->id }}" class="vehicle-checkbox w-4 h-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500" {{ in_array($vehicle->id, old('vehicle_ids', [])) ? 'checked' : '' }}>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="vehicle_{{ $vehicle->id }}" class="font-medium text-gray-700 cursor-pointer">
                                                        {{ $vehicle->stock_number }}
                                                    </label>
                                                    <p class="text-gray-500">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</p>
                                                    <p class="text-xs text-gray-400">VIN: {{ $vehicle->vin }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <x-input-error :messages="$errors->get('vehicle_ids')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-button type="submit" class="ml-4">
                                {{ __('Create Batch') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function filterVehicles() {
            const searchTerm = document.getElementById('vehicleSearch').value.toLowerCase();
            const vehicleItems = document.querySelectorAll('.vehicle-item');
            
            vehicleItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        function selectAllVehicles() {
            const checkboxes = document.querySelectorAll('.vehicle-checkbox');
            checkboxes.forEach(checkbox => {
                const item = checkbox.closest('.vehicle-item');
                if (item.style.display !== 'none') {
                    checkbox.checked = true;
                }
            });
        }
        
        function deselectAllVehicles() {
            const checkboxes = document.querySelectorAll('.vehicle-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>
    @endpush
</x-app-layout> 