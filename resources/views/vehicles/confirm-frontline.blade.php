<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Confirm Frontline Ready Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $workflow->vehicle->year }} {{ $workflow->vehicle->make }} {{ $workflow->vehicle->model }} - {{ $workflow->vehicle->stock_number }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            VIN: {{ $workflow->vehicle->vin }}
                        </p>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-800">Post-Repair Status</h4>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Detail Status -->
                            <div class="bg-white p-4 border rounded-lg shadow-sm">
                                <div class="flex items-center mb-2">
                                    <svg class="h-5 w-5 text-amber-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd" />
                                    </svg>
                                    <h5 class="text-sm font-medium text-gray-700">Detail Bucket</h5>
                                </div>
                                <div class="ml-7">
                                    @php
                                        $detailCategoryId = $workflow->inspectionItems()
                                            ->whereHas('category', function($q) {
                                                $q->where('slug', 'detail');
                                            })
                                            ->value('category_id');
                                        
                                        $detailCompleted = isset($categorySummary[$detailCategoryId]) && 
                                            $categorySummary[$detailCategoryId]->completed == $categorySummary[$detailCategoryId]->total;
                                    @endphp
                                    
                                    @if($detailCompleted)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Complete
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Incomplete
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Sales Manager Walkaround -->
                            <div class="bg-white p-4 border rounded-lg shadow-sm">
                                <div class="flex items-center mb-2">
                                    <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <h5 class="text-sm font-medium text-gray-700">Sales Manager Walkaround</h5>
                                </div>
                                <div class="ml-7">
                                    @php
                                        $walkaroundCategoryId = $workflow->inspectionItems()
                                            ->whereHas('category', function($q) {
                                                $q->where('slug', 'manager-walkaround');
                                            })
                                            ->value('category_id');
                                        
                                        $walkaroundCompleted = isset($categorySummary[$walkaroundCategoryId]) && 
                                            $categorySummary[$walkaroundCategoryId]->completed == $categorySummary[$walkaroundCategoryId]->total;
                                    @endphp
                                    
                                    @if($walkaroundCompleted)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Complete
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Incomplete
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Photos & Marketing -->
                            <div class="bg-white p-4 border rounded-lg shadow-sm">
                                <div class="flex items-center mb-2">
                                    <svg class="h-5 w-5 text-purple-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                    <h5 class="text-sm font-medium text-gray-700">Photos & Marketing</h5>
                                </div>
                                <div class="ml-7">
                                    @php
                                        $photosCategoryId = $workflow->inspectionItems()
                                            ->whereHas('category', function($q) {
                                                $q->where('slug', 'photos-marketing');
                                            })
                                            ->value('category_id');
                                        
                                        $photosCompleted = isset($categorySummary[$photosCategoryId]) && 
                                            $categorySummary[$photosCategoryId]->completed == $categorySummary[$photosCategoryId]->total;
                                    @endphp
                                    
                                    @if($photosCompleted)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Complete
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Incomplete
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('vehicles.frontline.mark', $workflow) }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('vehicles.frontline.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            
                            @if($detailCompleted && $walkaroundCompleted && $photosCompleted)
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Mark as Frontline Ready
                                </button>
                            @else
                                <button type="button" disabled class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed" title="Complete all post-repair steps first">
                                    Mark as Frontline Ready
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
