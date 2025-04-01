<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vehicle Inspections') }}
                @if(isset($stage))
                    - {{ $stage->name }}
                @endif
            </h2>
            <a href="{{ route('inspection.inspections.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-plus class="h-4 w-4 mr-1" />
                Start New Inspection
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Filter Bar -->
                    <div class="mb-6 flex flex-col md:flex-row gap-4 items-start md:items-center">
                        <div>
                            <label for="vehicle-status-filter" class="block text-sm font-medium text-gray-700 mb-1">Vehicle Status:</label>
                            <select id="vehicle-status-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="window.location.href='{{ route('inspection.inspections.index') }}' + (this.value ? '?vehicle_status=' + this.value : '')">
                                <option value="">All Statuses</option>
                                <option value="delivered" {{ request()->get('vehicle_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="arrived" {{ request()->get('vehicle_status') == 'arrived' ? 'selected' : '' }}>Arrived</option>
                                <option value="ready" {{ request()->get('vehicle_status') == 'ready' ? 'selected' : '' }}>Ready</option>
                            </select>
                        </div>

                        <div>
                            <label for="stage-filter" class="block text-sm font-medium text-gray-700 mb-1">Inspection Stage:</label>
                            <select id="stage-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="window.location.href='{{ route('inspection.inspections.index') }}' + 
                                    (document.getElementById('vehicle-status-filter').value ? '?vehicle_status=' + document.getElementById('vehicle-status-filter').value + '&' : '?') +
                                    'stage_id=' + this.value">
                                <option value="">All Stages</option>
                                @foreach($stages as $filterStage)
                                    @if(is_object($filterStage))
                                        <option value="{{ $filterStage->id }}" {{ isset($stageId) && $stageId == $filterStage->id ? 'selected' : '' }}>
                                            {{ $filterStage->name }}
                                        </option>
                                    @elseif(is_array($filterStage) && isset($filterStage['id']))
                                        <option value="{{ $filterStage['id'] }}" {{ isset($stageId) && $stageId == $filterStage['id'] ? 'selected' : '' }}>
                                            {{ $filterStage['name'] ?? 'Unknown Stage' }}
                                        </option>
                                    @else
                                        <option value="{{ $filterStage }}" {{ isset($stageId) && $stageId == $filterStage ? 'selected' : '' }}>
                                            {{ $filterStage }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Inspection Status:</label>
                            <select id="status-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="window.location.href='{{ route('inspection.inspections.index') }}' + 
                                    (document.getElementById('vehicle-status-filter').value ? '?vehicle_status=' + document.getElementById('vehicle-status-filter').value + '&' : '?') +
                                    (document.getElementById('stage-filter').value ? 'stage_id=' + document.getElementById('stage-filter').value + '&' : '') + 
                                    'status=' + this.value">
                                <option value="">All Status</option>
                                <option value="pending" {{ isset($status) && $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ isset($status) && $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ isset($status) && $status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h3 class="text-md font-medium text-blue-900 mb-2">Quick Actions</h3>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('inspection.inspections.index', ['vehicle_status' => 'delivered']) }}" 
                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full hover:bg-blue-200">
                                Delivered Vehicles
                            </a>
                            <a href="{{ route('inspection.inspections.index', ['vehicle_status' => 'arrived']) }}" 
                               class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full hover:bg-green-200">
                                Vehicles Ready for Inspection
                            </a>
                            <a href="{{ route('inspection.inspections.index', ['status' => 'in_progress']) }}" 
                               class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full hover:bg-yellow-200">
                                In-Progress Inspections
                            </a>
                        </div>
                    </div>

                    <!-- Vehicle Inspections Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($inspections as $inspection)
                                    <tr class="{{ $inspection->vehicle->status == 'arrived' ? 'bg-green-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('vehicles.show', $inspection->vehicle) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}
                                                </a>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $inspection->vehicle->stock_number }}
                                            </div>
                                            <div class="text-xs {{ $inspection->vehicle->status == 'arrived' ? 'text-green-600 font-semibold' : 'text-gray-500' }}">
                                                Vehicle: {{ ucfirst($inspection->vehicle->status) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($inspection->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $inspection->inspectionStage->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $inspection->inspection_date ? \Carbon\Carbon::parse($inspection->inspection_date)->format('M d, Y') : 'Not started' }}
                                            </div>
                                            @if($inspection->completed_date)
                                                <div class="text-xs text-gray-500">
                                                    Completed: {{ \Carbon\Carbon::parse($inspection->completed_date)->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                @php
                                                    $progress = 0;
                                                    $totalItems = $inspection->inspectionStage->inspectionItems->count();
                                                    $completedItems = $inspection->itemResults->count();
                                                    $passedItems = $inspection->itemResults->where('status', 'pass')->count();
                                                    $repairItems = $inspection->itemResults->whereIn('status', ['fail', 'warning'])->count();
                                                    
                                                    if ($totalItems > 0) {
                                                        $progress = ($completedItems / $totalItems) * 100;
                                                    }
                                                @endphp
                                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                                <span>{{ $completedItems }}/{{ $totalItems }} items</span>
                                                <span>
                                                    @if($passedItems > 0)
                                                        <span class="text-green-600">{{ $passedItems }} Pass</span>
                                                    @endif
                                                    @if($repairItems > 0)
                                                        <span class="text-red-600 ml-1">{{ $repairItems }} Need Repair</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('inspection.inspections.show', $inspection) }}" class="text-indigo-600 hover:text-indigo-900" title="View">
                                                    <x-heroicon-o-eye class="h-5 w-5" />
                                                </a>
                                                
                                                @if($inspection->status !== 'completed' && $inspection->vehicle->status == 'arrived')
                                                    <a href="{{ route('inspection.inspections.edit', $inspection) }}" class="text-indigo-600 hover:text-indigo-900" title="Continue Inspection">
                                                        <x-heroicon-o-clipboard-document-check class="h-5 w-5" />
                                                    </a>
                                                @endif
                                                
                                                <form method="POST" action="{{ route('inspection.inspections.destroy', $inspection) }}" class="inline delete-form" data-vehicle="{{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                        <x-heroicon-o-trash class="h-5 w-5" />
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No vehicle inspections found.
                                            <a href="{{ route('inspection.inspections.create') }}" class="text-indigo-600 hover:text-indigo-900">Start a new inspection</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($inspections) && method_exists($inspections, 'links'))
                        <div class="mt-4">
                            {{ $inspections->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const vehicleName = this.getAttribute('data-vehicle');
                    
                    if (confirm(`Are you sure you want to delete the inspection for "${vehicleName}"? This action cannot be undone.`)) {
                        this.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 