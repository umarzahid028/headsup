<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vendor Details') }}: {{ $vendor->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('vendors.edit', $vendor) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-pencil class="h-4 w-4 mr-1" />
                    Edit
                </a>
                <a href="{{ route('vendors.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Vendor Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vendor Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div>
                                <span class="text-gray-500 text-sm">Name:</span>
                                <p class="font-medium">{{ $vendor->name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Type:</span>
                                <p class="font-medium">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($vendor->type == 'mechanical') bg-blue-100 text-blue-800
                                        @elseif($vendor->type == 'body_shop') bg-yellow-100 text-yellow-800
                                        @elseif($vendor->type == 'detail') bg-green-100 text-green-800
                                        @elseif($vendor->type == 'tire') bg-purple-100 text-purple-800
                                        @elseif($vendor->type == 'upholstery') bg-pink-100 text-pink-800
                                        @elseif($vendor->type == 'glass') bg-indigo-100 text-indigo-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $vendor->type)) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Status:</span>
                                <p class="font-medium">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div>
                                <span class="text-gray-500 text-sm">Contact Person:</span>
                                <p class="font-medium">{{ $vendor->contact_person ?: 'Not specified' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Email:</span>
                                <p class="font-medium">
                                    @if($vendor->email)
                                        <a href="mailto:{{ $vendor->email }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $vendor->email }}
                                        </a>
                                    @else
                                        Not specified
                                    @endif
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Phone:</span>
                                <p class="font-medium">
                                    @if($vendor->phone)
                                        <a href="tel:{{ $vendor->phone }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $vendor->phone }}
                                        </a>
                                    @else
                                        Not specified
                                    @endif
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Address:</span>
                                <p class="font-medium">{{ $vendor->address ?: 'Not specified' }}</p>
                            </div>
                        </div>
                        
                        @if($vendor->notes)
                        <div class="col-span-1 md:col-span-2 mt-4">
                            <span class="text-gray-500 text-sm">Notes:</span>
                            <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $vendor->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Inspection History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Inspection & Repair History</h3>
                    
                    @if($inspectionItems->count() > 0)
                        <div class="space-y-8">
                            @foreach($inspectionItems as $vehicleId => $items)
                                @php
                                    $vehicle = $items->first()->vehicleInspection->vehicle;
                                    $totalCost = $items->sum('vendor_cost');
                                @endphp
                                
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <a href="{{ route('vehicles.show', $vehicle) }}" class="text-lg font-medium text-indigo-600 hover:text-indigo-900">
                                                    {{ $vehicle->stock_number }} - {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                                </a>
                                                <span class="ml-3 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    VIN: {{ $vehicle->vin }}
                                                </span>
                                            </div>
                                            <span class="font-semibold text-green-600">${{ number_format($totalCost, 2) }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="px-4 py-3">
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inspection Stage</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach($items as $result)
                                                        <tr>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                {{ $result->vehicleInspection->inspectionStage->name }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                {{ $result->inspectionItem->name }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                    @if($result->status == 'pass') bg-green-100 text-green-800
                                                                    @elseif($result->status == 'fail') bg-red-100 text-red-800
                                                                    @elseif($result->status == 'repaired') bg-blue-100 text-blue-800
                                                                    @else bg-yellow-100 text-yellow-800
                                                                    @endif">
                                                                    {{ ucfirst($result->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                ${{ number_format($result->vendor_cost, 2) }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                {{ $result->updated_at->format('M d, Y') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-md p-4 text-center text-gray-500">
                            This vendor has no inspection or repair history yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 