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
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('inspection.inspections.index') }}" method="GET" class="space-y-4">
                        <div class="max-w-xl">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Search by Stock # or VIN</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" 
                                           name="search" 
                                           id="search" 
                                           value="{{ request('search') }}"
                                           placeholder="Enter Stock Number or VIN"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm"
                                    >
                                    <button type="submit" 
                                            class="ml-3 inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                        Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Inspections List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
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