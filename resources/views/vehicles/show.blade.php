<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vehicle Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit Vehicle') }}
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Stock Number</p>
                                    <p class="mt-1">{{ $vehicle->stock_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">VIN</p>
                                    <p class="mt-1">{{ $vehicle->vin }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Year</p>
                                    <p class="mt-1">{{ $vehicle->year }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Make</p>
                                    <p class="mt-1">{{ $vehicle->make }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Model</p>
                                    <p class="mt-1">{{ $vehicle->model }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Trim</p>
                                    <p class="mt-1">{{ $vehicle->trim ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $vehicle->status == 'available' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $vehicle->status == 'sold' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $vehicle->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $vehicle->status == 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ !in_array($vehicle->status, ['available', 'sold', 'pending', 'in_transit']) ? 'bg-gray-100 text-gray-800' : '' }}
                                        ">
                                            {{ ucfirst($vehicle->status ?? 'Unknown') }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Price</p>
                                    <p class="mt-1">${{ number_format($vehicle->advertising_price, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Additional Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Odometer</p>
                                    <p class="mt-1">{{ number_format($vehicle->odometer) ?? 'N/A' }} miles</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Exterior Color</p>
                                    <p class="mt-1">{{ $vehicle->exterior_color ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Interior Color</p>
                                    <p class="mt-1">{{ $vehicle->interior_color ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Body Type</p>
                                    <p class="mt-1">{{ $vehicle->body_type ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Drive Train</p>
                                    <p class="mt-1">{{ $vehicle->drive_train ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Engine</p>
                                    <p class="mt-1">{{ $vehicle->engine ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Transmission</p>
                                    <p class="mt-1">{{ $vehicle->transmission ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Fuel Type</p>
                                    <p class="mt-1">{{ $vehicle->fuel_type ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Acquisition Information -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Acquisition Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Date in Stock</p>
                                    <p class="mt-1">{{ $vehicle->date_in_stock ? $vehicle->date_in_stock->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Purchased From</p>
                                    <p class="mt-1">{{ $vehicle->purchased_from ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Purchase Date</p>
                                    <p class="mt-1">{{ $vehicle->purchase_date ? $vehicle->purchase_date->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Purchase Source</p>
                                    <p class="mt-1">{{ $vehicle->vehicle_purchase_source ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Information -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Sales Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Deal Status</p>
                                    <p class="mt-1">{{ $vehicle->deal_status ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Sold Date</p>
                                    <p class="mt-1">{{ $vehicle->sold_date ? $vehicle->sold_date->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Buyer Name</p>
                                    <p class="mt-1">{{ $vehicle->buyer_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Featured</p>
                                    <p class="mt-1">{{ $vehicle->is_featured ? 'Yes' : 'No' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 