<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection Details') }}
            </h2>
            <a href="{{ route('vendor.inspections.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Vehicle Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Vehicle</p>
                            <p class="text-base font-medium">{{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">VIN</p>
                            <p class="text-base font-medium">{{ $inspection->vehicle->vin }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Stock Number</p>
                            <p class="text-base font-medium">{{ $inspection->vehicle->stock_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="text-base font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($inspection->status) }}
                                </span>
                            </p>
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
                                <div class="border rounded-lg p-4 {{ $item->completed_at ? 'bg-gray-50' : 'bg-white' }}">
                                    <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                                        <div class="flex-grow">
                                            <h4 class="text-base font-medium text-gray-900">{{ $item->inspectionItem->name }}</h4>
                                            @if($item->inspectionItem->description)
                                                <p class="mt-1 text-sm text-gray-500">{{ $item->inspectionItem->description }}</p>
                                            @endif
                                            
                                            @if($item->notes)
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-600">Notes:</p>
                                                    <p class="text-sm text-gray-900">{{ $item->notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-4 md:mt-0 md:ml-6">
                                            @if($item->completed_at)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $item->status === 'repair' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            @else
                                                <form action="{{ route('vendor.inspections.update-item', $item) }}" method="POST" class="flex flex-col space-y-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    
                                                    <div class="flex space-x-2">
                                                        <button type="submit" name="status" value="repair" class="inline-flex items-center px-3 py-1 border border-blue-600 rounded-md text-sm font-medium text-blue-600 hover:bg-blue-50">
                                                            <x-heroicon-o-wrench class="h-4 w-4 mr-1" />
                                                            Repair
                                                        </button>
                                                        <button type="submit" name="status" value="replace" class="inline-flex items-center px-3 py-1 border border-purple-600 rounded-md text-sm font-medium text-purple-600 hover:bg-purple-50">
                                                            <x-heroicon-o-arrow-path class="h-4 w-4 mr-1" />
                                                            Replace
                                                        </button>
                                                    </div>
                                                    
                                                    <textarea name="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Add notes (optional)"></textarea>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 