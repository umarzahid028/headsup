<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection Details') }}
            </h2>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <!-- Vehicle Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">{{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}</p>
                            <p class="text-gray-600">VIN: {{ $inspection->vehicle->vin }}</p>
                            <p class="text-gray-600">Stock #: {{ $inspection->vehicle->stock_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">
                                <span class="font-medium">Inspection Created:</span> 
                                {{ $inspection->created_at->format('M d, Y h:ia') }}
                            </p>
                            @if($inspection->completed_at)
                                <p class="text-gray-600">
                                    <span class="font-medium">Completed:</span>
                                    {{ $inspection->completed_at->format('M d, Y h:ia') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspection Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Items</h3>
                    
                    @if($inspection->inspectionItems->isEmpty())
                        <p class="text-gray-500">No items assigned to you for this inspection.</p>
                    @else
                        <div class="space-y-6">
                            @foreach($inspection->inspectionItems as $item)
                                <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                                    <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                                        <div class="flex-grow">
                                            <h4 class="text-base font-medium text-gray-900">{{ $item->name }}</h4>
                                            @if($item->description)
                                                <p class="mt-1 text-sm text-gray-500">{{ $item->description }}</p>
                                            @endif
                                        </div>
                                        <div class="mt-2 md:mt-0 md:ml-4">
                                            @if($item->status === 'diagnostic')
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                                    Diagnostic Required
                                                </span>
                                            @elseif($item->status === 'pending_approval')
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending Approval
                                                </span>
                                            @elseif($item->status === 'completed')
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    Completed
                                                </span>
                                            @elseif($item->status === 'cancelled')
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                                    Cancelled
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                    In Progress
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if(in_array($item->status, ['pending', 'diagnostic']))
                                        <div class="mt-4">
                                            @if(auth()->user()->isOffSiteVendor() && $item->status === 'diagnostic')
                                                <!-- Off-Site Vendor Estimate Form -->
                                                <form action="{{ route('vendor.inspections.submit-estimate', $inspection) }}" method="POST" class="space-y-4">
                                                    @csrf
                                                    <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                                    
                                                    <div>
                                                        <label for="cost_{{ $item->id }}" class="block text-sm font-medium text-gray-700">Estimated Cost</label>
                                                        <div class="mt-1 relative rounded-md shadow-sm">
                                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                <span class="text-gray-500 sm:text-sm">$</span>
                                                            </div>
                                                            <input type="number" step="0.01" min="0" name="items[{{ $loop->index }}][estimated_cost]" id="cost_{{ $item->id }}"
                                                                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md @error('items.' . $loop->index . '.estimated_cost') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                                                placeholder="0.00" required
                                                                value="{{ old('items.' . $loop->index . '.estimated_cost') }}">
                                                        </div>
                                                        @error('items.' . $loop->index . '.estimated_cost')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div>
                                                        <label for="notes_{{ $item->id }}" class="block text-sm font-medium text-gray-700">Diagnostic Notes</label>
                                                        <div class="mt-1">
                                                            <textarea id="notes_{{ $item->id }}" name="items[{{ $loop->index }}][notes]" rows="3"
                                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('items.' . $loop->index . '.notes') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                                                placeholder="Enter diagnostic findings and repair recommendations..." required>{{ old('items.' . $loop->index . '.notes') }}</textarea>
                                                        </div>
                                                        @error('items.' . $loop->index . '.notes')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div class="flex justify-end">
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            Submit Estimate for Approval
                                                        </button>
                                                    </div>
                                                </form>
                                            @elseif(auth()->user()->isOnSiteVendor() || (!auth()->user()->isOffSiteVendor() && $item->status !== 'diagnostic'))
                                                <!-- On-Site Vendor Work Completion Form -->
                                                <form action="{{ route('vendor.inspections.update-status', $inspection) }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                                    
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Status Update</label>
                                                        <div class="mt-2 space-x-4">
                                                            <label class="inline-flex items-center">
                                                                <input type="radio" name="items[{{ $loop->index }}][status]" value="completed" class="form-radio text-indigo-600" required
                                                                    {{ old('items.' . $loop->index . '.status') === 'completed' ? 'checked' : '' }}>
                                                                <span class="ml-2 text-sm text-gray-700">Complete</span>
                                                            </label>
                                                            <label class="inline-flex items-center">
                                                                <input type="radio" name="items[{{ $loop->index }}][status]" value="cancelled" class="form-radio text-red-600"
                                                                    {{ old('items.' . $loop->index . '.status') === 'cancelled' ? 'checked' : '' }}>
                                                                <span class="ml-2 text-sm text-gray-700">Cancel</span>
                                                            </label>
                                                        </div>
                                                        @error('items.' . $loop->index . '.status')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    @if(auth()->user()->isOnSiteVendor())
                                                        <div>
                                                            <label for="actual_cost_{{ $item->id }}" class="block text-sm font-medium text-gray-700">
                                                                Actual Cost <span class="text-red-500">*</span>
                                                            </label>
                                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                    <span class="text-gray-500 sm:text-sm">$</span>
                                                                </div>
                                                                <input type="number" step="0.01" min="0" name="items[{{ $loop->index }}][actual_cost]" id="actual_cost_{{ $item->id }}"
                                                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md @error('items.' . $loop->index . '.actual_cost') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                                                    placeholder="0.00" required
                                                                    value="{{ old('items.' . $loop->index . '.actual_cost') }}">
                                                            </div>
                                                            @error('items.' . $loop->index . '.actual_cost')
                                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                            @enderror
                                                            <p class="mt-1 text-xs text-gray-500">As an on-site vendor, you must enter the actual cost of the work performed.</p>
                                                        </div>
                                                    @endif

                                                    <div>
                                                        <label for="completion_notes_{{ $item->id }}" class="block text-sm font-medium text-gray-700">
                                                            Completion Notes <span class="text-red-500">*</span>
                                                        </label>
                                                        <div class="mt-1">
                                                            <textarea id="completion_notes_{{ $item->id }}" name="items[{{ $loop->index }}][completion_notes]" rows="3"
                                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('items.' . $loop->index . '.completion_notes') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                                                placeholder="Enter detailed notes about the work performed..." required>{{ old('items.' . $loop->index . '.completion_notes') }}</textarea>
                                                        </div>
                                                        @error('items.' . $loop->index . '.completion_notes')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Photos</label>
                                                        <div class="mt-1">
                                                            <input type="file" name="items[{{ $loop->index }}][photos][]" multiple
                                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                                accept="image/*">
                                                        </div>
                                                        <p class="mt-1 text-xs text-gray-500">Upload photos of the completed work (recommended)</p>
                                                        @error('items.' . $loop->index . '.photos')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div class="flex justify-end">
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            {{ auth()->user()->isOnSiteVendor() ? 'Complete Work & Submit Cost' : 'Update Status' }}
                                                        </button>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <div class="mt-4 space-y-4">
                                            @if($item->estimated_cost)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Estimated Cost:</span>
                                                    <span class="ml-2 text-sm text-gray-900">${{ number_format($item->estimated_cost, 2) }}</span>
                                                </div>
                                            @endif

                                            @if($item->actual_cost)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Actual Cost:</span>
                                                    <span class="ml-2 text-sm text-gray-900">${{ number_format($item->actual_cost, 2) }}</span>
                                                </div>
                                            @endif

                                            @if($item->notes)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Notes:</span>
                                                    <p class="mt-1 text-sm text-gray-600">{{ $item->notes }}</p>
                                                </div>
                                            @endif

                                            @if($item->completion_notes)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Completion Notes:</span>
                                                    <p class="mt-1 text-sm text-gray-600">{{ $item->completion_notes }}</p>
                                                </div>
                                            @endif

                                            @if($item->photos && count($item->photos) > 0)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Photos:</span>
                                                    <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                                        @foreach($item->photos as $photo)
                                                            <a href="{{ Storage::url($photo) }}" target="_blank" class="block">
                                                                <img src="{{ Storage::url($photo) }}" alt="Inspection photo" class="object-cover h-24 w-full rounded-lg">
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if($item->completed_at)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Completed:</span>
                                                    <span class="ml-2 text-sm text-gray-900">{{ $item->completed_at->format('M d, Y h:ia') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 