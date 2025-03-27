<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Vehicle') }}
            </h2>
            <a href="{{ route('vehicles.index') }}">
                <x-ui-button variant="outline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to List
                </x-ui-button>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui-card>
                <x-slot name="title">Vehicle Information</x-slot>
                
                <form method="POST" action="{{ route('vehicles.store') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="vin" class="block text-sm font-medium text-gray-700 mb-1">VIN *</label>
                                <input type="text" id="vin" name="vin" value="{{ old('vin') }}" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('vin') border-red-500 @enderror">
                                @error('vin')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="stock_number" class="block text-sm font-medium text-gray-700 mb-1">Stock Number</label>
                                <input type="text" id="stock_number" name="stock_number" value="{{ old('stock_number') }}"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('stock_number') border-red-500 @enderror">
                                @error('stock_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year *</label>
                                <input type="number" id="year" name="year" value="{{ old('year') }}" required min="1900" max="{{ date('Y') + 1 }}"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('year') border-red-500 @enderror">
                                @error('year')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="make" class="block text-sm font-medium text-gray-700 mb-1">Make *</label>
                                <input type="text" id="make" name="make" value="{{ old('make') }}" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('make') border-red-500 @enderror">
                                @error('make')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model *</label>
                                <input type="text" id="model" name="model" value="{{ old('model') }}" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('model') border-red-500 @enderror">
                                @error('model')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="trim" class="block text-sm font-medium text-gray-700 mb-1">Trim</label>
                                <input type="text" id="trim" name="trim" value="{{ old('trim') }}"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('trim') border-red-500 @enderror">
                                @error('trim')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color *</label>
                                <input type="text" id="color" name="color" value="{{ old('color') }}" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('color') border-red-500 @enderror">
                                @error('color')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="mileage" class="block text-sm font-medium text-gray-700 mb-1">Mileage *</label>
                                <input type="number" id="mileage" name="mileage" value="{{ old('mileage') }}" required min="0"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('mileage') border-red-500 @enderror">
                                @error('mileage')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-1">Purchase Date</label>
                                <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('purchase_date') border-red-500 @enderror">
                                @error('purchase_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">Purchase Price *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" required min="0" step="0.01"
                                        class="w-full rounded-md border border-gray-300 pl-7 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('purchase_price') border-red-500 @enderror">
                                </div>
                                @error('purchase_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="current_stage" class="block text-sm font-medium text-gray-700 mb-1">Initial Stage *</label>
                                <select id="current_stage" name="current_stage" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('current_stage') border-red-500 @enderror">
                                    @foreach ($stages as $stage)
                                        <option value="{{ $stage->slug }}" {{ old('current_stage') == $stage->slug ? 'selected' : ($loop->first ? 'selected' : '') }}>
                                            {{ $stage->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('current_stage')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="pt-1">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="is_frontline_ready" name="is_frontline_ready" class="h-4 w-4 text-gray-600 rounded" {{ old('is_frontline_ready') ? 'checked' : '' }}>
                                    <label for="is_frontline_ready" class="text-sm font-medium text-gray-700">Frontline Ready</label>
                                </div>
                                @error('is_frontline_ready')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('vehicles.index') }}">
                            <x-ui-button type="button" variant="outline">
                                Cancel
                            </x-ui-button>
                        </a>
                        <x-ui-button type="submit">
                            Create Vehicle
                        </x-ui-button>
                    </div>
                </form>
            </x-ui-card>
        </div>
    </div>
</x-app-layout> 