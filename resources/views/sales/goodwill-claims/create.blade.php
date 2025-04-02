<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('New Goodwill Claim') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('sales.goodwill-claims.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Vehicle Selection -->
                        <div>
                            <x-input-label for="vehicle_id" :value="__('Vehicle')" />
                            <select id="vehicle_id" name="vehicle_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Select Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $selectedVehicle?->id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->stock_number }} - {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                        </div>

                        <!-- Sales Issue Selection (Optional) -->
                        @if(isset($salesIssue))
                            <div>
                                <x-input-label :value="__('Related Sales Issue')" />
                                <div class="mt-1 p-4 bg-gray-50 rounded-md">
                                    <input type="hidden" name="sales_issue_id" value="{{ $salesIssue->id }}">
                                    <div class="text-sm text-gray-900">
                                        <p class="font-medium">Issue Type: {{ ucfirst($salesIssue->issue_type) }}</p>
                                        <p class="mt-1">{{ $salesIssue->description }}</p>
                                        <p class="mt-2 text-gray-500">Reported by {{ $salesIssue->reportedBy->name }} - {{ $salesIssue->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Customer Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="customer_name" :value="__('Customer Name')" />
                                <x-text-input id="customer_name" name="customer_name" type="text" class="mt-1 block w-full" 
                                    :value="old('customer_name')" required autofocus />
                                <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="customer_phone" :value="__('Customer Phone')" />
                                <x-text-input id="customer_phone" name="customer_phone" type="tel" class="mt-1 block w-full" 
                                    :value="old('customer_phone')" required />
                                <x-input-error :messages="$errors->get('customer_phone')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="customer_email" :value="__('Customer Email')" />
                                <x-text-input id="customer_email" name="customer_email" type="email" class="mt-1 block w-full" 
                                    :value="old('customer_email')" />
                                <x-input-error :messages="$errors->get('customer_email')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Issue Details -->
                        <div>
                            <x-input-label for="issue_description" :value="__('Issue Description')" />
                            <textarea id="issue_description" name="issue_description" rows="3" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('issue_description', $salesIssue?->description) }}</textarea>
                            <x-input-error :messages="$errors->get('issue_description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="requested_resolution" :value="__('Requested Resolution')" />
                            <textarea id="requested_resolution" name="requested_resolution" rows="3" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('requested_resolution') }}</textarea>
                            <x-input-error :messages="$errors->get('requested_resolution')" class="mt-2" />
                        </div>

                        <!-- Cost Estimate -->
                        <div>
                            <x-input-label for="estimated_cost" :value="__('Estimated Cost')" />
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <x-text-input id="estimated_cost" name="estimated_cost" type="number" step="0.01" min="0"
                                    class="pl-7 block w-full" :value="old('estimated_cost')" placeholder="0.00" />
                            </div>
                            <x-input-error :messages="$errors->get('estimated_cost')" class="mt-2" />
                        </div>

                        <!-- Customer Consent -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="customer_consent" name="customer_consent" type="checkbox" value="1"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                    {{ old('customer_consent') ? 'checked' : '' }} required>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="customer_consent" class="font-medium text-gray-700">Customer Consent</label>
                                <p class="text-gray-500">Customer has been informed and agrees to the goodwill claim process.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('sales.goodwill-claims.index') }}" class="text-gray-600 hover:text-gray-900">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Create Claim') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif
</x-app-layout> 