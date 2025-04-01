<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Conduct Inspection') }}: {{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}
                <span class="text-base font-normal text-gray-500">({{ $inspection->inspectionStage->name }})</span>
            </h2>
            <a href="{{ route('inspection.inspections.show', $inspection) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to Inspection
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Vehicle and Inspection Info Card -->
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
                        </div>
                        
                        <div>
                            <div class="mt-2">
                                <h4 class="text-sm font-medium text-gray-900">Progress</h4>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2 mb-2">
                                    @php
                                        $progress = 0;
                                        $completedItems = $inspection->itemResults->whereIn('status', ['pass', 'fail', 'warning'])->count();
                                        $totalItems = $inspection->inspectionStage->inspectionItems->count();
                                        if ($totalItems > 0) {
                                            $progress = ($completedItems / $totalItems) * 100;
                                        }
                                    @endphp
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                                <p class="text-sm text-gray-600">{{ $completedItems }} of {{ $totalItems }} items assessed ({{ round($progress) }}%)</p>
                            </div>

                            <div class="mt-4">
                                <form method="POST" action="{{ route('inspection.inspections.complete', $inspection) }}" class="confirm-form" data-message="Are you sure you want to mark this inspection as completed? This will finalize all results.">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <x-heroicon-o-check-circle class="h-4 w-4 mr-1" />
                                        Complete This Inspection Stage
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

            <!-- Legend -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 text-gray-900">
                    <h3 class="font-medium mb-2">Assessment Legend</h3>
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full bg-green-500 mr-1"></span>
                            <span class="text-sm">Pass - Item meets standards</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full bg-yellow-500 mr-1"></span>
                            <span class="text-sm">Warning - Needs minor repair</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full bg-red-500 mr-1"></span>
                            <span class="text-sm">Fail - Major issue requiring repair/replacement</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspection Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $inspection->inspectionStage->name }} - Inspection Checklist</h3>
                    
                    <div class="space-y-8">
                        @foreach($inspection->inspectionStage->inspectionItems as $item)
                            @php
                                $result = $inspection->itemResults->where('inspection_item_id', $item->id)->first();
                            @endphp
                            
                            <div class="border rounded-lg p-4 
                                {{ $result && $result->status === 'pass' ? 'border-green-300 bg-green-50' : 
                                   ($result && $result->status === 'fail' ? 'border-red-300 bg-red-50' : 
                                    ($result && $result->status === 'warning' ? 'border-yellow-300 bg-yellow-50' : 'border-gray-300 bg-gray-50')) }}">
                                <form method="POST" 
                                      action="{{ $result ? route('inspection.results.update', $result) : route('inspection.results.store') }}" 
                                      class="space-y-4" 
                                      enctype="multipart/form-data">
                                    @csrf
                                    @if($result)
                                        @method('PUT')
                                    @endif
                                    
                                    <!-- Hidden fields -->
                                    <input type="hidden" name="vehicle_inspection_id" value="{{ $inspection->id }}">
                                    <input type="hidden" name="inspection_item_id" value="{{ $item->id }}">
                                    
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div>
                                            <h4 class="text-base font-medium text-gray-900">{{ $item->name }}</h4>
                                            @if($item->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $item->description }}</p>
                                            @endif
                                        </div>
                                        
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-sm text-gray-500 mr-2">Assessment:</span>
                                            <label class="inline-flex items-center p-1 bg-green-100 rounded hover:bg-green-200 cursor-pointer">
                                                <input type="radio" name="status" value="pass" {{ $result && $result->status === 'pass' ? 'checked' : '' }} required
                                                       class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                                <span class="ml-2 text-sm text-green-700">Pass</span>
                                            </label>
                                            <label class="inline-flex items-center p-1 bg-yellow-100 rounded hover:bg-yellow-200 cursor-pointer">
                                                <input type="radio" name="status" value="warning" {{ $result && $result->status === 'warning' ? 'checked' : '' }} required
                                                       class="h-4 w-4 text-yellow-600 border-gray-300 focus:ring-yellow-500">
                                                <span class="ml-2 text-sm text-yellow-700">Repair</span>
                                            </label>
                                            <label class="inline-flex items-center p-1 bg-red-100 rounded hover:bg-red-200 cursor-pointer">
                                                <input type="radio" name="status" value="fail" {{ $result && $result->status === 'fail' ? 'checked' : '' }} required
                                                       class="h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500">
                                                <span class="ml-2 text-sm text-red-700">Replace</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="notes-{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                            <textarea id="notes-{{ $item->id }}" name="notes" rows="3" 
                                                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ $result ? $result->notes : '' }}</textarea>
                                        </div>
                                        
                                        @if($item->cost_tracking)
                                            <div>
                                                <label for="cost-{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Repair/Replace Cost ($)</label>
                                                <input type="number" step="0.01" min="0" id="cost-{{ $item->id }}" name="cost" 
                                                       value="{{ $result ? $result->cost : '0.00' }}" 
                                                       class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            </div>
                                        @endif
                                        
                                        @if($item->vendor_required)
                                            <div>
                                                <label for="vendor-{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Assign Vendor</label>
                                                <select id="vendor-{{ $item->id }}" name="vendor_id" 
                                                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    <option value="">Select vendor (optional)</option>
                                                    @foreach($vendors as $vendor)
                                                        <option value="{{ $vendor->id }}" {{ $result && $result->vendor_id == $vendor->id ? 'selected' : '' }}>
                                                            {{ $vendor->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        
                                        <div class="{{ !$item->cost_tracking || !$item->vendor_required ? 'md:col-span-2' : '' }}">
                                            <label for="images-{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                                Upload Images
                                                <span class="text-xs text-gray-500 ml-1">(optional, multiple files allowed)</span>
                                            </label>
                                            <input type="file" id="images-{{ $item->id }}" name="images[]" multiple 
                                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        </div>
                                    </div>
                                    
                                    @if($result && $result->repairImages->count() > 0)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Uploaded Images</label>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                @foreach($result->repairImages as $image)
                                                    <div class="relative group">
                                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Repair Image" class="h-20 w-20 object-cover rounded">
                                                        <form method="POST" action="{{ route('inspection.images.destroy', $image) }}" class="absolute -top-2 -right-2 hidden group-hover:block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="bg-red-600 text-white rounded-full p-1 hover:bg-red-700"
                                                                    onclick="return confirm('Are you sure you want to delete this image?')">
                                                                <x-heroicon-o-x-mark class="h-3 w-3" />
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="flex justify-end">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <x-heroicon-o-clipboard-document-check class="h-4 w-4 mr-1" />
                                            {{ $result && in_array($result->status, ['pass', 'fail', 'warning']) ? 'Update Assessment' : 'Save Assessment' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmForms = document.querySelectorAll('.confirm-form');
            confirmForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const message = this.getAttribute('data-message');
                    
                    if (confirm(message)) {
                        this.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 