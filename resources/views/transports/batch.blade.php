<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Batch Details: ') }} {{ $batchId }}
                @if($batchData->batch_name)
                    <span class="text-gray-600 ml-2 text-lg">{{ $batchData->batch_name }}</span>
                @endif
            </h2>
            <div>
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
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Batch Information Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Batch Status</h3>
                            <p class="mt-1 text-md font-semibold">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $batchData->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $batchData->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $batchData->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $batchData->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst($batchData->status) }}
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Origin</h3>
                            <p class="mt-1 text-md font-semibold">{{ $batchData->origin ?: 'Not specified' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Destination</h3>
                            <p class="mt-1 text-md font-semibold">{{ $batchData->destination }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Vehicle Count</h3>
                            <p class="mt-1 text-md font-semibold">{{ $transports->count() }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Pickup Date</h3>
                            <p class="mt-1 text-md font-semibold">{{ $batchData->pickup_date ? date('M d, Y', strtotime($batchData->pickup_date)) : 'Not scheduled' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Delivery Date</h3>
                            <p class="mt-1 text-md font-semibold">{{ $batchData->delivery_date ? date('M d, Y', strtotime($batchData->delivery_date)) : 'Not scheduled' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Transporter</h3>
                            <p class="mt-1 text-md font-semibold">
                                @if($batchData->transporter_id)
                                    {{ optional($batchData->transporter)->full_name }}
                                @elseif($batchData->transporter_name)
                                    {{ $batchData->transporter_name }}
                                @else
                                    Not assigned
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created On</h3>
                            <p class="mt-1 text-md font-semibold">{{ date('M d, Y', strtotime($batchData->created_at)) }}</p>
                        </div>
                    </div>
                    
                    @if($batchData->qr_code_path)
                        <div class="mt-6 flex justify-center">
                            <div class="text-center">
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Batch QR Code</h3>
                                <img src="{{ Storage::url($batchData->qr_code_path) }}" alt="Batch QR Code" class="h-36 w-36 mx-auto">
                                <p class="mt-2 text-xs text-gray-500">Scan to track this batch</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($batchData->notes)
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Notes</h3>
                            <p class="text-gray-700 whitespace-pre-line">{{ $batchData->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Vehicles in Batch -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicles in Batch</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vehicle
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock Number
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        VIN
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Gate Pass
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transports as $transport)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transport->vehicle->year }} {{ $transport->vehicle->make }} {{ $transport->vehicle->model }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $transport->vehicle->color }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transport->vehicle->stock_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transport->vehicle->vin }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $transport->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $transport->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $transport->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $transport->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                            ">
                                                {{ ucfirst($transport->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($transport->gate_pass_path)
                                                <a href="{{ Storage::url($transport->gate_pass_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                                    View Gate Pass
                                                </a>
                                            @else
                                                <span class="text-gray-400">Not uploaded</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="{{ route('transports.show', $transport) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                View
                                            </a>
                                            <a href="{{ route('transports.edit', $transport) }}" class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 