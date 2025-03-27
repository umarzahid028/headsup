<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vehicles') }}
            </h2>
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'sales_manager')
            <a href="{{ route('vehicles.create') }}">
                <x-ui-button>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 000 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Add New Vehicle
                </x-ui-button>
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui-card class="mb-6">
                <form method="GET" action="{{ route('vehicles.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                                placeholder="VIN, Stock #, Make, Model...">
                        </div>
                        
                        <div>
                            <label for="stage" class="block text-sm font-medium text-gray-700 mb-1">Stage</label>
                            <select name="stage" id="stage" 
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                                <option value="">All Stages</option>
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->slug }}" {{ request('stage') == $stage->slug ? 'selected' : '' }}>
                                        {{ $stage->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" 
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                                <option value="">Active Vehicles</option>
                                <option value="frontline" {{ request('status') == 'frontline' ? 'selected' : '' }}>Frontline Ready</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <x-ui-button type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                                Search
                            </x-ui-button>
                            
                            <a href="{{ route('vehicles.index') }}">
                                <x-ui-button variant="outline">
                                    Reset
                                </x-ui-button>
                            </a>
                        </div>
                    </div>
                </form>
            </x-ui-card>

            <x-ui-table :headers="['VIN/Stock', 'Vehicle', 'Current Stage', 'Status', 'Updated']" :actions="true">
                @forelse($vehicles as $vehicle)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">
                            <div class="font-medium">{{ $vehicle->vin }}</div>
                            <div class="text-xs text-gray-500">{{ $vehicle->stock_number ?? 'No Stock #' }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-medium">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</div>
                            <div class="text-xs text-gray-500">{{ $vehicle->color }} {{ $vehicle->trim }}</div>
                        </td>
                        <td class="p-4">
                            @php 
                                $stageBadgeColors = [
                                    'intake' => 'gray',
                                    'test_drive' => 'info',
                                    'arbitration' => 'danger',
                                    'mechanical' => 'warning',
                                    'exterior' => 'warning',
                                    'interior' => 'warning',
                                    'features' => 'info',
                                    'tires_brakes' => 'warning',
                                    'detail' => 'info',
                                    'walkaround' => 'info',
                                    'photos' => 'info',
                                    'frontline' => 'success',
                                ];
                                $stageColor = $stageBadgeColors[$vehicle->current_stage] ?? 'default';
                            @endphp
                            <x-ui-badge :variant="$stageColor">
                                {{ $stages->firstWhere('slug', $vehicle->current_stage)->name ?? 'Unknown' }}
                            </x-ui-badge>
                            
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $vehicle->daysInCurrentStage() }} days
                            </div>
                        </td>
                        <td class="p-4">
                            @if($vehicle->is_frontline_ready)
                                <x-ui-badge variant="success">Frontline Ready</x-ui-badge>
                            @elseif($vehicle->is_archived)
                                <x-ui-badge variant="gray">Archived</x-ui-badge>
                            @elseif($vehicle->is_sold)
                                <x-ui-badge variant="info">Sold</x-ui-badge>
                            @else
                                <x-ui-badge variant="warning">In Process</x-ui-badge>
                            @endif
                        </td>
                        <td class="p-4 text-sm text-gray-500">
                            {{ $vehicle->updated_at->format('M d, Y') }}
                        </td>
                        <td class="p-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('vehicles.show', $vehicle) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                
                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'sales_manager')
                                <a href="{{ route('vehicles.edit', $vehicle) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500">
                            No vehicles found
                        </td>
                    </tr>
                @endforelse
            </x-ui-table>
            
            <div class="mt-4">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>
</x-app-layout> 