<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Vehicle') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('vehicles.show', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ __('View Details') }}
                </a>
                <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Basic Information -->
                            <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Basic Information</h3>
                                
                                <!-- Stock Number -->
                                <div class="mb-4">
                                    <label for="stock_number" class="block text-sm font-medium text-gray-700">Stock Number *</label>
                                    <x-shadcn.input 
                                        type="text" 
                                        name="stock_number" 
                                        id="stock_number" 
                                        :value="old('stock_number', $vehicle->stock_number)" 
                                        required 
                                    />
                                </div>
                                
                                <!-- VIN -->
                                <div class="mb-4">
                                    <label for="vin" class="block text-sm font-medium text-gray-700">VIN *</label>
                                    <x-shadcn.input 
                                        type="text" 
                                        name="vin" 
                                        id="vin" 
                                        :value="old('vin', $vehicle->vin)" 
                                        required 
                                    />
                                </div>
                                
                                <!-- Year -->
                                <div class="mb-4">
                                    <label for="year" class="block text-sm font-medium text-gray-700">Year *</label>
                                    <input type="number" name="year" id="year" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                                </div>
                                
                                <!-- Make -->
                                <div class="mb-4">
                                    <label for="make" class="block text-sm font-medium text-gray-700">Make *</label>
                                    <input type="text" name="make" id="make" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('make', $vehicle->make) }}" required>
                                </div>
                                
                                <!-- Model -->
                                <div class="mb-4">
                                    <label for="model" class="block text-sm font-medium text-gray-700">Model *</label>
                                    <input type="text" name="model" id="model" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('model', $vehicle->model) }}" required>
                                </div>
                                
                                <!-- Trim -->
                                <div class="mb-4">
                                    <label for="trim" class="block text-sm font-medium text-gray-700">Trim</label>
                                    <input type="text" name="trim" id="trim" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('trim', $vehicle->trim) }}">
                                </div>
                            </div>
                            
                            <!-- Vehicle Details -->
                            <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Vehicle Details</h3>
                                
                                <!-- Odometer -->
                                <div class="mb-4">
                                    <label for="odometer" class="block text-sm font-medium text-gray-700">Odometer</label>
                                    <input type="number" name="odometer" id="odometer" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('odometer', $vehicle->odometer) }}">
                                </div>
                                
                                <!-- Exterior Color -->
                                <div class="mb-4">
                                    <label for="exterior_color" class="block text-sm font-medium text-gray-700">Exterior Color</label>
                                    <input type="text" name="exterior_color" id="exterior_color" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('exterior_color', $vehicle->exterior_color) }}">
                                </div>
                                
                                <!-- Interior Color -->
                                <div class="mb-4">
                                    <label for="interior_color" class="block text-sm font-medium text-gray-700">Interior Color</label>
                                    <input type="text" name="interior_color" id="interior_color" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('interior_color', $vehicle->interior_color) }}">
                                </div>
                                
                                <!-- Body Type -->
                                <div class="mb-4">
                                    <label for="body_type" class="block text-sm font-medium text-gray-700">Body Type</label>
                                    <input type="text" name="body_type" id="body_type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('body_type', $vehicle->body_type) }}">
                                </div>
                                
                                <!-- Drive Train -->
                                <div class="mb-4">
                                    <label for="drive_train" class="block text-sm font-medium text-gray-700">Drive Train</label>
                                    <input type="text" name="drive_train" id="drive_train" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('drive_train', $vehicle->drive_train) }}">
                                </div>
                                
                                <!-- Engine -->
                                <div class="mb-4">
                                    <label for="engine" class="block text-sm font-medium text-gray-700">Engine</label>
                                    <input type="text" name="engine" id="engine" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('engine', $vehicle->engine) }}">
                                </div>
                                
                                <!-- Fuel Type -->
                                <div class="mb-4">
                                    <label for="fuel_type" class="block text-sm font-medium text-gray-700">Fuel Type</label>
                                    <input type="text" name="fuel_type" id="fuel_type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('fuel_type', $vehicle->fuel_type) }}">
                                </div>
                                
                                <!-- Transmission -->
                                <div class="mb-4">
                                    <label for="transmission" class="block text-sm font-medium text-gray-700">Transmission</label>
                                    <input type="text" name="transmission" id="transmission" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('transmission', $vehicle->transmission) }}">
                                </div>
                                
                                <!-- Transmission Type -->
                                <div class="mb-4">
                                    <label for="transmission_type" class="block text-sm font-medium text-gray-700">Transmission Type</label>
                                    <input type="text" name="transmission_type" id="transmission_type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('transmission_type', $vehicle->transmission_type) }}">
                                </div>
                            </div>
                            
                            <!-- Pricing & Status -->
                            <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Pricing & Status</h3>
                                
                                <!-- Advertising Price -->
                                <div class="mb-4">
                                    <label for="advertising_price" class="block text-sm font-medium text-gray-700">Advertising Price</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" name="advertising_price" id="advertising_price" class="pl-7 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('advertising_price', $vehicle->advertising_price) }}" step="0.01">
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <x-shadcn.select name="status" id="status" placeholder="-- Select Status --">
                                        <option value="available" {{ old('status', $vehicle->status) == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="pending" {{ old('status', $vehicle->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="sold" {{ old('status', $vehicle->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                                        <option value="in_transit" {{ old('status', $vehicle->status) == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                    </x-shadcn.select>
                                </div>
                                
                                <!-- Date in Stock -->
                                <div class="mb-4">
                                    <label for="date_in_stock" class="block text-sm font-medium text-gray-700">Date in Stock</label>
                                    <input type="date" name="date_in_stock" id="date_in_stock" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('date_in_stock', $vehicle->date_in_stock ? $vehicle->date_in_stock->format('Y-m-d') : '') }}">
                                </div>

                                <!-- Sold Date (only show if status is sold) -->
                                <div class="mb-4" id="sold_date_container" style="{{ old('status', $vehicle->status) == 'sold' ? 'display:block' : 'display:none' }}">
                                    <label for="sold_date" class="block text-sm font-medium text-gray-700">Sold Date</label>
                                    <input type="date" name="sold_date" id="sold_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('sold_date', $vehicle->sold_date ? $vehicle->sold_date->format('Y-m-d') : '') }}">
                                </div>
                                
                                <!-- Buyer Name (only show if status is sold) -->
                                <div class="mb-4" id="buyer_name_container" style="{{ old('status', $vehicle->status) == 'sold' ? 'display:block' : 'display:none' }}">
                                    <label for="buyer_name" class="block text-sm font-medium text-gray-700">Buyer Name</label>
                                    <input type="text" name="buyer_name" id="buyer_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('buyer_name', $vehicle->buyer_name) }}">
                                </div>
                                
                                <!-- Featured -->
                                <div class="mb-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="is_featured" id="is_featured" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" value="1" {{ old('is_featured', $vehicle->is_featured) ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_featured" class="font-medium text-gray-700">Featured Vehicle</label>
                                            <p class="text-gray-500">Show this vehicle prominently on the website</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="mt-8 pt-5">
                                    <div class="flex justify-end">
                                        <x-shadcn.button 
                                            type="submit" 
                                            variant="default" 
                                            class="mt-4"
                                        >
                                            Update Vehicle
                                        </x-shadcn.button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide sold date and buyer name fields based on status
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const soldDateContainer = document.getElementById('sold_date_container');
            const buyerNameContainer = document.getElementById('buyer_name_container');

            statusSelect.addEventListener('change', function() {
                if (this.value === 'sold') {
                    soldDateContainer.style.display = 'block';
                    buyerNameContainer.style.display = 'block';
                } else {
                    soldDateContainer.style.display = 'none';
                    buyerNameContainer.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout> 