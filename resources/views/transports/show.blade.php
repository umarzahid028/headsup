<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transport Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('transports.edit', $transport) }}">
                    <x-shadcn.button variant="default">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('Edit Transport') }}
                    </x-shadcn.button>
                </a>
                <a href="{{ route('transports.index') }}">
                    <x-shadcn.button variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('Back to List') }}
                    </x-shadcn.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vehicle Information -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Vehicle Information</h3>
                            
                            @if ($transport->vehicle)
                                <div class="mb-6">
                                    <div class="flex items-center gap-4 mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-xl font-bold">
                                                {{ $transport->vehicle->year }} {{ $transport->vehicle->make }} {{ $transport->vehicle->model }}
                                            </h4>
                                            <p class="text-gray-500">Stock #: {{ $transport->vehicle->stock_number }}</p>
                                        </div>
                                        <div>
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $transport->vehicle->status == 'available' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $transport->vehicle->status == 'sold' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $transport->vehicle->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $transport->vehicle->status == 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                            ">
                                                {{ ucfirst($transport->vehicle->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">VIN</p>
                                            <p>{{ $transport->vehicle->vin ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Exterior Color</p>
                                            <p>{{ $transport->vehicle->exterior_color ?? 'N/A' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Advertising Price</p>
                                            <p>${{ number_format($transport->vehicle->advertising_price ?? 0, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Date In Stock</p>
                                            <p>{{ $transport->vehicle->date_in_stock ? $transport->vehicle->date_in_stock->format('M d, Y') : 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="{{ route('vehicles.show', $transport->vehicle) }}">
                                        <x-shadcn.button variant="outline" size="sm">
                                            View Full Vehicle Details
                                        </x-shadcn.button>
                                    </a>
                                </div>
                            @else
                                <div class="p-4 bg-red-100 text-red-700 rounded">
                                    Vehicle not found or has been deleted.
                                </div>
                            @endif
                        </div>
                        
                        <!-- Transport Details -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Transport Details</h3>
                            
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-xl font-bold">
                                        Transport #{{ $transport->id }}
                                    </h4>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $transport->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $transport->status == 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $transport->status == 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $transport->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ ucfirst($transport->status) }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 gap-4 mb-6">
                                    <div class="bg-gray-50 p-4 rounded">
                                        <h5 class="font-semibold mb-2">Route Information</h5>
                                        <div class="flex items-center">
                                            @if ($transport->origin)
                                                <span class="font-medium">{{ $transport->origin }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            @endif
                                            <span class="font-medium">{{ $transport->destination }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Pickup Date</p>
                                            <p>{{ $transport->pickup_date ? $transport->pickup_date->format('M d, Y') : 'Not set' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Delivery Date</p>
                                            <p>{{ $transport->delivery_date ? $transport->delivery_date->format('M d, Y') : 'Not set' }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded mb-4">
                                    <h5 class="font-semibold mb-2">Transporter Information</h5>
                                    @if ($transport->transporter)
                                        <p><span class="font-medium">Name:</span> {{ $transport->transporter->name }}</p>
                                        @if ($transport->transporter->contact_person)
                                            <p><span class="font-medium">Contact Person:</span> {{ $transport->transporter->contact_person }}</p>
                                        @endif
                                        @if ($transport->transporter->phone)
                                            <p><span class="font-medium">Phone:</span> {{ $transport->transporter->phone }}</p>
                                        @endif
                                        @if ($transport->transporter->email)
                                            <p><span class="font-medium">Email:</span> {{ $transport->transporter->email }}</p>
                                        @endif
                                        <div class="mt-2">
                                            <a href="{{ route('transporters.show', $transport->transporter) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                View Full Transporter Details
                                            </a>
                                        </div>
                                    @elseif ($transport->transporter_name)
                                        <p><span class="font-medium">Name:</span> {{ $transport->transporter_name }}</p>
                                        @if ($transport->transporter_phone)
                                            <p><span class="font-medium">Phone:</span> {{ $transport->transporter_phone }}</p>
                                        @endif
                                        @if ($transport->transporter_email)
                                            <p><span class="font-medium">Email:</span> {{ $transport->transporter_email }}</p>
                                        @endif
                                    @else
                                        <p class="text-gray-500">No transporter assigned</p>
                                    @endif
                                </div>
                                
                                @if ($transport->notes)
                                    <div class="bg-gray-50 p-4 rounded">
                                        <h5 class="font-semibold mb-2">Notes</h5>
                                        <p class="whitespace-pre-line">{{ $transport->notes }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-4 text-xs text-gray-500">
                                <p>Created: {{ $transport->created_at->format('M d, Y h:i A') }}</p>
                                <p>Last Updated: {{ $transport->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delete Form -->
                    <div class="mt-8 border-t pt-6">
                        <div class="flex justify-end">
                            <form action="{{ route('transports.destroy', $transport) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transport record?')">
                                @csrf
                                @method('DELETE')
                                <x-shadcn.button type="submit" variant="destructive">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete Transport
                                </x-shadcn.button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 