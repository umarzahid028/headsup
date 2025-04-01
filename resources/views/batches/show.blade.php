<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Batch Details') }}: {{ $batch->batch_number }}
            </h2>
            <div class="flex space-x-2">
                <x-button href="{{ route('batches.edit', $batch) }}">
                    <x-heroicon-o-pencil class="w-5 h-5 mr-1" />
                    {{ __('Edit Batch') }}
                </x-button>
                <x-button href="{{ route('batches.index') }}" variant="outline">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-1" />
                    {{ __('Back to Batches') }}
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Batch Details -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Batch Information') }}</h3>
                            <div class="mt-4 space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Batch Number') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $batch->batch_number }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Batch Name') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $batch->name ?? '-' }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Status') }}:</span>
                                    <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full {{ 
                                        $batch->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($batch->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : 
                                        ($batch->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                        'bg-red-100 text-red-800')) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Transporter') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $batch->transporter->name ?? '-' }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Origin') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $batch->origin ?? '-' }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Destination') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $batch->destination }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Created On') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $batch->created_at->format('M d, Y H:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Schedule Information') }}</h3>
                            <div class="mt-4 space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Scheduled Pickup Date') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $batch->scheduled_pickup_date ? $batch->scheduled_pickup_date->format('M d, Y') : '-' }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Scheduled Delivery Date') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $batch->scheduled_delivery_date ? $batch->scheduled_delivery_date->format('M d, Y') : '-' }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Actual Pickup Date') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $batch->pickup_date ? $batch->pickup_date->format('M d, Y H:i A') : '-' }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Actual Delivery Date') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $batch->delivery_date ? $batch->delivery_date->format('M d, Y H:i A') : '-' }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Notes') }}:</span>
                                    <div class="mt-1 text-sm text-gray-900">{{ $batch->notes ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Status Form -->
                    <div class="p-4 mt-6 border border-gray-200 rounded-md bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Update Batch Status') }}</h3>
                        <form method="POST" action="{{ route('batches.update-status', $batch) }}" class="mt-4">
                            @csrf
                            @method('PATCH')
                            
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <x-select id="status" name="status" class="block w-full mt-1" required>
                                        <option value="pending" {{ $batch->status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                        <option value="in_transit" {{ $batch->status == 'in_transit' ? 'selected' : '' }}>{{ __('In Transit') }}</option>
                                        <option value="delivered" {{ $batch->status == 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                        <option value="cancelled" {{ $batch->status == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                    </x-select>
                                </div>
                                
                                <div>
                                    <x-input-label for="pickup_date" :value="__('Pickup Date')" />
                                    <x-input id="pickup_date" name="pickup_date" type="datetime-local" class="block w-full mt-1" />
                                </div>
                                
                                <div>
                                    <x-input-label for="delivery_date" :value="__('Delivery Date')" />
                                    <x-input id="delivery_date" name="delivery_date" type="datetime-local" class="block w-full mt-1" />
                                </div>
                                
                                <div class="flex items-end">
                                    <x-button type="submit" class="w-full">
                                        {{ __('Update Status') }}
                                    </x-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Vehicles in Batch -->
            <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Vehicles in Batch') }} ({{ $batch->transports->count() }})</h3>
                        <a href="{{ route('gate-passes.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-gray-800 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25">
                            <x-heroicon-o-document-plus class="w-4 h-4 mr-1" />
                            {{ __('Create Gate Pass') }}
                        </a>
                    </div>

                    @if($batch->transports->isEmpty())
                        <div class="p-4 mt-4 text-sm italic text-center text-gray-500 border border-gray-200 rounded-md">
                            {{ __('No vehicles assigned to this batch.') }}
                        </div>
                    @else
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full border divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Vehicle') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Status') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Gate Pass') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($batch->transports as $transport)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $transport->vehicle->stock_number ?? '-' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $transport->vehicle->year ?? '' }} {{ $transport->vehicle->make ?? '' }} {{ $transport->vehicle->model ?? '' }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    VIN: {{ $transport->vehicle->vin ?? '' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full {{ 
                                                    $transport->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                    ($transport->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : 
                                                    ($transport->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                                    'bg-red-100 text-red-800')) 
                                                }}">
                                                    {{ ucfirst(str_replace('_', ' ', $transport->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $gatePass = $transport->vehicle->gatePasses->where('batch_id', $batch->id)->first();
                                                @endphp
                                                
                                                @if($gatePass)
                                                    <div class="text-sm text-gray-900">
                                                        <a href="{{ route('gate-passes.show', $gatePass) }}" class="text-indigo-600 hover:text-indigo-900">
                                                            {{ $gatePass->pass_number }}
                                                        </a>
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ __('Status') }}: 
                                                        <span class="inline-flex px-1 text-xs font-semibold leading-5 rounded-full {{ 
                                                            $gatePass->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                            ($gatePass->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                                            ($gatePass->status === 'used' ? 'bg-green-100 text-green-800' : 
                                                            ($gatePass->status === 'rejected' ? 'bg-red-100 text-red-800' :
                                                            'bg-gray-100 text-gray-800'))) 
                                                        }}">
                                                            {{ ucfirst($gatePass->status) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="text-sm text-gray-500">
                                                        {{ __('No gate pass assigned') }}
                                                    </div>
                                                    <a href="{{ route('gate-passes.create', ['vehicle_id' => $transport->vehicle->id, 'batch_id' => $batch->id]) }}" class="text-xs text-indigo-600 hover:text-indigo-900">
                                                        {{ __('Create gate pass') }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('vehicles.show', $transport->vehicle->id) }}" class="text-indigo-600 hover:text-indigo-900" title="{{ __('View Vehicle') }}">
                                                        <x-heroicon-o-eye class="w-5 h-5" />
                                                    </a>
                                                    
                                                    @if($gatePass && $gatePass->file_path)
                                                        <a href="{{ route('gate-passes.download', $gatePass) }}" class="text-blue-600 hover:text-blue-900" title="{{ __('Download Gate Pass') }}">
                                                            <x-heroicon-o-document-arrow-down class="w-5 h-5" />
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Gate Passes -->
            <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Gate Passes for Batch') }}</h3>

                    @if($batch->gatePasses->isEmpty())
                        <div class="p-4 mt-4 text-sm italic text-center text-gray-500 border border-gray-200 rounded-md">
                            {{ __('No gate passes created for this batch.') }}
                        </div>
                    @else
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full border divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Pass Number') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Vehicle') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Validity') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Status') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                            {{ __('File') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($batch->gatePasses as $gatePass)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $gatePass->pass_number }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    {{ __('Created') }}: {{ $gatePass->created_at->format('M d, Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $gatePass->vehicle->stock_number ?? '-' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $gatePass->vehicle->year ?? '' }} {{ $gatePass->vehicle->make ?? '' }} {{ $gatePass->vehicle->model ?? '' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <div>{{ __('Issue') }}: {{ \Carbon\Carbon::parse($gatePass->issue_date)->format('M d, Y') }}</div>
                                                    <div>{{ __('Expiry') }}: {{ \Carbon\Carbon::parse($gatePass->expiry_date)->format('M d, Y') }}</div>
                                                </div>
                                                @if($gatePass->used_at)
                                                    <div class="text-xs text-gray-500">
                                                        {{ __('Used on') }}: {{ \Carbon\Carbon::parse($gatePass->used_at)->format('M d, Y') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full {{ 
                                                    $gatePass->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                    ($gatePass->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                                    ($gatePass->status === 'used' ? 'bg-green-100 text-green-800' : 
                                                    ($gatePass->status === 'rejected' ? 'bg-red-100 text-red-800' :
                                                    'bg-gray-100 text-gray-800'))) 
                                                }}">
                                                    {{ ucfirst($gatePass->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                                                @if($gatePass->file_path)
                                                    <a href="{{ route('gate-passes.download', $gatePass) }}" class="text-blue-600 hover:text-blue-900" title="{{ __('Download') }}">
                                                        <x-heroicon-o-document-arrow-down class="w-5 h-5 mx-auto" />
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">
                                                        <x-heroicon-o-document-minus class="w-5 h-5 mx-auto" />
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('gate-passes.show', $gatePass) }}" class="text-indigo-600 hover:text-indigo-900" title="{{ __('View') }}">
                                                        <x-heroicon-o-eye class="w-5 h-5" />
                                                    </a>
                                                    @if(!in_array($gatePass->status, ['used', 'expired']))
                                                        <a href="{{ route('gate-passes.edit', $gatePass) }}" class="text-blue-600 hover:text-blue-900" title="{{ __('Edit') }}">
                                                            <x-heroicon-o-pencil class="w-5 h-5" />
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 