<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vehicle Inspections') }}
            </h2>
            <div>
                <a href="{{ route('inspection.inspections.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-plus class="h-4 w-4 mr-1" />
                    Start New Vehicle Inspection
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Bar -->
            <div class="mb-6">
                <form action="{{ route('inspection.inspections.index') }}" method="GET" class="flex gap-3">
                    <div class="flex-1 relative">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}"
                               class="block w-full pl-11 pr-4 py-3 text-base text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white shadow-sm"
                               placeholder="Search by Stock #, VIN, Make, or Model"
                        >
                    </div>

                    <div class="relative min-w-[200px]">
                        <select name="status" 
                                class="appearance-none block w-full py-3 pl-4 pr-10 text-base text-gray-900 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-300 bg-white shadow-sm cursor-pointer">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <button type="submit" 
                            class="px-6 py-3 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Inspections List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($inspections->count() > 0)
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ request('search') 
                                    ? 'Search Results for "' . request('search') . '"'
                                    : 'All Vehicle Inspections'
                                }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Showing {{ $inspections->firstItem() ?? 0 }} to {{ $inspections->lastItem() ?? 0 }} of {{ $inspections->total() }} inspections
                            </p>
                        </div>
                    @endif
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inspection Stages</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($inspections as $inspection)
                                    @php
                                        $vehicle = $inspection->vehicle;
                                        $vehicleInspections = $vehicle->vehicleInspections;
                                        $allCompleted = $vehicleInspections->every(function($insp) {
                                            return $insp->status === 'completed';
                                        });
                                        $anyFailed = $vehicleInspections->contains(function($insp) {
                                            return $insp->status === 'failed';
                                        });
                                        $anyInProgress = $vehicleInspections->contains(function($insp) {
                                            return $insp->status === 'in_progress';
                                        });
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Stock #: {{ $vehicle->stock_number }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-2">
                                                @foreach($vehicleInspections->sortBy('inspectionStage.order') as $vehicleInspection)
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm text-gray-900">{{ $vehicleInspection->inspectionStage->name }}</span>
                                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $vehicleInspection->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                            ($vehicleInspection->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                                            ($vehicleInspection->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                            {{ ucfirst($vehicleInspection->status) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $allCompleted ? 'bg-green-100 text-green-800' : 
                                                ($anyFailed ? 'bg-red-100 text-red-800' : 
                                                ($anyInProgress ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                {{ $allCompleted ? 'All Completed' : 
                                                ($anyFailed ? 'Has Failed Stages' : 
                                                ($anyInProgress ? 'In Progress' : 'Pending')) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('vehicles.show', $vehicle) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">View Vehicle Details</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No inspections found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $inspections->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 