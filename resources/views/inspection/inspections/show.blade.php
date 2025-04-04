<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection Details') }}: {{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}
                <span class="text-base font-normal text-gray-500">({{ $inspection->inspectionStage->name }})</span>
            </h2>
            <div class="flex space-x-2">
                @if($inspection->status !== 'completed')
                    <a href="{{ route('inspection.inspections.edit', $inspection) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <x-heroicon-o-clipboard-document-check class="h-4 w-4 mr-1" />
                        Continue Inspection
                    </a>
                @endif
                <a href="{{ route('inspection.inspections.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                    Back to Inspections
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Inspection Overview Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Vehicle Information</h3>
                            <p class="text-gray-600">{{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}</p>
                            <p class="text-gray-600">VIN: {{ $inspection->vehicle->vin }}</p>
                            <p class="text-gray-600">Stock #: {{ $inspection->vehicle->stock_number }}</p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $inspection->vehicle->status === 'arrived' ? 'bg-green-100 text-green-800' : 
                                    ($inspection->vehicle->status === 'delivered' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') 
                                }}">
                                    Vehicle Status: {{ ucfirst($inspection->vehicle->status) }}
                                </span>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('vehicles.show', $inspection->vehicle) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                    View Full Vehicle Details
                                </a>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Inspection Details</h3>
                            <p class="text-gray-600">Stage: {{ $inspection->inspectionStage->name }}</p>
                            <p class="text-gray-600">Status: 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                    ($inspection->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                                </span>
                            </p>
                            <p class="text-gray-600">Started: {{ $inspection->inspection_date ? \Carbon\Carbon::parse($inspection->inspection_date)->format('M d, Y') : 'Not started' }}</p>
                            @if($inspection->completed_date)
                                <p class="text-gray-600">Completed: {{ \Carbon\Carbon::parse($inspection->completed_date)->format('M d, Y') }}</p>
                            @endif
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Assignment</h3>
                            @if($inspection->vendor_id)
                                <p class="text-gray-600">Vendor: {{ $inspection->vendor->name }}</p>
                            @endif
                            
                            @php
                                $passCount = $inspection->itemResults->where('status', 'pass')->count();
                                $warningCount = $inspection->itemResults->where('status', 'warning')->count();
                                $failCount = $inspection->itemResults->where('status', 'fail')->count();
                                $pendingCount = $inspection->itemResults->where('status', 'not_applicable')->count();
                                $totalItems = $inspection->inspectionStage->inspectionItems->count();
                            @endphp
                            
                            <div class="mt-3">
                                <h4 class="text-sm font-medium text-gray-900">Assessment Summary</h4>
                                <div class="mt-2 flex flex-col space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Pass:</span>
                                        <span class="text-sm font-medium text-green-600">{{ $passCount }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Repair Needed:</span>
                                        <span class="text-sm font-medium text-yellow-600">{{ $warningCount }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Replace Needed:</span>
                                        <span class="text-sm font-medium text-red-600">{{ $failCount }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Pending Assessment:</span>
                                        <span class="text-sm font-medium text-gray-600">{{ $pendingCount }}</span>
                                    </div>
                                    <div class="flex justify-between pt-1 border-t">
                                        <span class="text-sm font-medium text-gray-800">Total Cost:</span>
                                        <span class="text-sm font-bold text-gray-900">${{ number_format($inspection->total_cost, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($inspection->notes)
                        <div class="mt-4 border-t pt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Notes</h3>
                            <p class="text-gray-600">{{ $inspection->notes }}</p>
                        </div>
                    @endif

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Inspection Progress</h3>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                            @php
                                $progress = 0;
                                $completedItems = $passCount + $warningCount + $failCount;
                                if ($totalItems > 0) {
                                    $progress = ($completedItems / $totalItems) * 100;
                                }
                            @endphp
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="text-sm text-gray-600">{{ $completedItems }} of {{ $totalItems }} items assessed ({{ round($progress) }}%)</p>
                    </div>
                </div>
            </div>

            <!-- Inspection Results -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $inspection->inspectionStage->name }} - Inspection Results</h3>
                        @if($inspection->status !== 'completed' && $inspection->vehicle->status == 'arrived')
                            <a href="{{ route('inspection.inspections.edit', $inspection) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <x-heroicon-o-clipboard-document-check class="h-4 w-4 mr-1" />
                                Continue Assessment
                            </a>
                        @endif
                    </div>

                    <!-- Legend -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-md border border-gray-200 flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-1">
                                Status: Pass
                            </span>
                            <span class="text-sm text-gray-500">No issues found</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-1">
                                Status: Repair
                            </span>
                            <span class="text-sm text-gray-500">Needs repair</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-1">
                                Status: Replace
                            </span>
                            <span class="text-sm text-gray-500">Needs replacement</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                                Pending
                            </span>
                            <span class="text-sm text-gray-500">Not inspected yet</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Repair Cost</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Images</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $resultsMap = $inspection->itemResults->keyBy('inspection_item_id');
                                @endphp

                                @foreach($inspection->inspectionStage->inspectionItems as $item)
                                    <tr class="{{ 
                                        $resultsMap->has($item->id) && $resultsMap[$item->id]->status === 'pass' ? 'bg-green-50' :
                                        ($resultsMap->has($item->id) && $resultsMap[$item->id]->status === 'warning' ? 'bg-yellow-50' :
                                        ($resultsMap->has($item->id) && $resultsMap[$item->id]->status === 'fail' ? 'bg-red-50' : '')) 
                                    }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                            @if($item->description)
                                                <div class="text-xs text-gray-500">{{ $item->description }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($resultsMap->has($item->id))
                                                @php $result = $resultsMap->get($item->id); @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($result->status === 'pass') bg-green-100 text-green-800
                                                    @elseif($result->status === 'warning') bg-yellow-100 text-yellow-800
                                                    @elseif($result->status === 'fail') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    @if($result->status === 'pass')
                                                        Status: Pass
                                                    @elseif($result->status === 'warning')
                                                        Status: Repair
                                                    @elseif($result->status === 'fail')
                                                        Status: Replace
                                                    @else
                                                        {{ ucfirst($result->status) }}
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">
                                                {{ isset($resultsMap[$item->id]) ? ($resultsMap[$item->id]->notes ?: 'No notes') : 'Not inspected yet' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if(isset($resultsMap[$item->id]) && $resultsMap[$item->id]->cost > 0)
                                                    ${{ number_format($resultsMap[$item->id]->cost, 2) }}
                                                @else
                                                    --
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if(isset($resultsMap[$item->id]) && $resultsMap[$item->id]->vendor_id)
                                                    {{ $resultsMap[$item->id]->assignedVendor->name }}
                                                @else
                                                    --
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(isset($resultsMap[$item->id]) && $resultsMap[$item->id]->repairImages->count() > 0)
                                                <div class="flex items-center space-x-1">
                                                    <span class="text-sm text-gray-500">{{ $resultsMap[$item->id]->repairImages->count() }}</span>
                                                    <button type="button" 
                                                            class="text-indigo-600 hover:text-indigo-900"
                                                            onclick="openImagesModal('{{ $item->id }}')">
                                                        <x-heroicon-o-camera class="h-5 w-5" />
                                                    </button>
                                                </div>
                                                
                                                <!-- Hidden div with images for this result -->
                                                <div id="images-{{ $item->id }}" class="hidden">
                                                    @foreach($resultsMap[$item->id]->repairImages as $image)
                                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Repair Image {{ $loop->iteration }}" class="max-w-full rounded shadow-md mb-2">
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">None</span>
                                            @endif
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

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-medium text-gray-900" id="modal-title">Repair Images</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeImagesModal()">
                    <x-heroicon-o-x-mark class="h-6 w-6" />
                </button>
            </div>
            <div class="p-4" id="modal-content">
                <!-- Images will be loaded here -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openImagesModal(itemId) {
            const modal = document.getElementById('imageModal');
            const content = document.getElementById('modal-content');
            const imagesContainer = document.getElementById('images-' + itemId);
            const title = document.getElementById('modal-title');
            
            // Get the item name from the table
            const itemName = document.querySelector(`[id="images-${itemId}"]`).closest('tr').querySelector('td:first-child .text-sm').textContent;
            title.textContent = `Images for ${itemName}`;
            
            // Clear previous content
            content.innerHTML = '';
            
            // Clone the images
            const images = imagesContainer.innerHTML;
            content.innerHTML = images;
            
            // Show modal
            modal.classList.remove('hidden');
            
            // Prevent scrolling on background
            document.body.style.overflow = 'hidden';
        }
        
        function closeImagesModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImagesModal();
            }
        });
    </script>
    @endpush
</x-app-layout> 