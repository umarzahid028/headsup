<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Transport') }}
            </h2>
            <div>
                <a href="{{ route('transports.index') }}">
                    <x-shadcn.button variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('Back to List') }}
                    </x-shadcn.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('transports.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Transport Information -->
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Transport Information</h3>
                                
                                <!-- Vehicle -->
                                <div class="mb-4">
                                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Vehicle *</label>
                                    <x-shadcn.select name="vehicle_id" id="vehicle_id" required>
                                        <option value="">-- Select Vehicle --</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->stock_number }} - {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                            </option>
                                        @endforeach
                                    </x-shadcn.select>
                                </div>
                                
                                <!-- Origin -->
                                <div class="mb-4">
                                    <label for="origin" class="block text-sm font-medium text-gray-700">Origin</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="origin"
                                        id="origin"
                                        :value="old('origin')"
                                    />
                                </div>
                                
                                <!-- Destination -->
                                <div class="mb-4">
                                    <label for="destination" class="block text-sm font-medium text-gray-700">Destination *</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="destination"
                                        id="destination"
                                        :value="old('destination')"
                                        required
                                    />
                                </div>
                                
                                <!-- Pickup Date -->
                                <div class="mb-4">
                                    <label for="pickup_date" class="block text-sm font-medium text-gray-700">Pickup Date</label>
                                    <x-shadcn.input
                                        type="date"
                                        name="pickup_date"
                                        id="pickup_date"
                                        :value="old('pickup_date')"
                                    />
                                </div>
                                
                                <!-- Delivery Date -->
                                <div class="mb-4">
                                    <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                                    <x-shadcn.input
                                        type="date"
                                        name="delivery_date"
                                        id="delivery_date"
                                        :value="old('delivery_date')"
                                    />
                                </div>
                            </div>
                            
                            <!-- Transporter Details & Status -->
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Transporter Details & Status</h3>
                                
                                <!-- Status -->
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                    <x-shadcn.select name="status" id="status" required>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_transit" {{ old('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                        <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </x-shadcn.select>
                                </div>
                                
                                <!-- Transporter Selection -->
                                <div class="mb-4">
                                    <label for="transporter_id" class="block text-sm font-medium text-gray-700">Select Transporter</label>
                                    <x-shadcn.select name="transporter_id" id="transporter_id">
                                        <option value="">-- Select a Transporter --</option>
                                        @foreach ($transporters as $transporter)
                                            <option value="{{ $transporter->id }}" {{ old('transporter_id') == $transporter->id ? 'selected' : '' }}>
                                                {{ $transporter->full_name }}
                                            </option>
                                        @endforeach
                                    </x-shadcn.select>
                                    <p class="mt-1 text-xs text-gray-500">Select a transporter or enter details manually below</p>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-4 mt-4">
                                    <h4 class="font-medium text-gray-700 mb-3">Or Enter Transporter Details Manually</h4>
                                
                                    <!-- Transporter Name -->
                                    <div class="mb-4">
                                        <label for="transporter_name" class="block text-sm font-medium text-gray-700">Transporter Name</label>
                                        <x-shadcn.input
                                            type="text"
                                            name="transporter_name"
                                            id="transporter_name"
                                            :value="old('transporter_name')"
                                        />
                                    </div>
                                    
                                    <!-- Transporter Phone -->
                                    <div class="mb-4">
                                        <label for="transporter_phone" class="block text-sm font-medium text-gray-700">Transporter Phone</label>
                                        <x-shadcn.input
                                            type="text"
                                            name="transporter_phone"
                                            id="transporter_phone"
                                            :value="old('transporter_phone')"
                                        />
                                    </div>
                                    
                                    <!-- Transporter Email -->
                                    <div class="mb-4">
                                        <label for="transporter_email" class="block text-sm font-medium text-gray-700">Transporter Email</label>
                                        <x-shadcn.input
                                            type="email"
                                            name="transporter_email"
                                            id="transporter_email"
                                            :value="old('transporter_email')"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Notes -->
                                <div class="mb-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="mt-6 flex justify-end">
                                    <x-shadcn.button type="submit" variant="default">
                                        Create Transport
                                    </x-shadcn.button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 