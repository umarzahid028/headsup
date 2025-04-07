@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight text-center w-full">
            {{ __('GOODWILL REPAIR ACKNOWLEDGEMENT') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="p-8">
                    <form action="{{ route('sales.goodwill-claims.store') }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- Dealership Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="dealership_name" :value="__('Dealership Name')" class="text-base" />
                                <x-text-input id="dealership_name" name="dealership_name" type="text" 
                                    class="mt-1 block w-full rounded-lg bg-gray-100" 
                                    value="Trevino's Auto Mart" 
                                    readonly />
                            </div>
                            <div>
                                <x-input-label for="representative" :value="__('Representative')" class="text-base" />
                                <x-text-input id="representative" name="representative" type="text" 
                                    class="mt-1 block w-full rounded-lg" 
                                    :value="old('representative')" 
                                    required />
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="customer_name" :value="__('Customer Name(s)')" class="text-base" />
                                <x-text-input id="customer_name" name="customer_name" type="text" 
                                    class="mt-1 block w-full rounded-lg" 
                                    :value="old('customer_name')" 
                                    required />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="home_phone" :value="__('Home Telephone')" class="text-base" />
                                    <x-text-input id="home_phone" name="home_phone" type="tel" 
                                        class="mt-1 block w-full rounded-lg" 
                                        :value="old('home_phone')" 
                                        placeholder="(555) 555-5555" />
                                </div>
                                <div>
                                    <x-input-label for="work_phone" :value="__('Work Telephone')" class="text-base" />
                                    <x-text-input id="work_phone" name="work_phone" type="tel" 
                                        class="mt-1 block w-full rounded-lg" 
                                        :value="old('work_phone')" 
                                        placeholder="(555) 555-5555" />
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Information -->
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="vehicle_id" :value="__('Vehicle')" class="text-base" />
                                    <select id="vehicle_id" name="vehicle_id" 
                                        class="mt-1 block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 rounded-lg shadow-sm bg-white dark:bg-gray-700 dark:text-gray-300" 
                                        required>
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $selectedVehicle?->id) == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->stock_number }} - {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="vin" :value="__('VIN')" class="text-base" />
                                    <x-text-input id="vin" name="vin" type="text" 
                                        class="mt-1 block w-full rounded-lg bg-gray-100" 
                                        :value="$selectedVehicle?->vin" 
                                        readonly />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="odometer" :value="__('Odometer Reading (Miles)')" class="text-base" />
                                    <x-text-input id="odometer" name="odometer" type="number" 
                                        class="mt-1 block w-full rounded-lg" 
                                        :value="old('odometer')" 
                                        required />
                                </div>
                                <div>
                                    <x-input-label for="license_no" :value="__('License No')" class="text-base" />
                                    <x-text-input id="license_no" name="license_no" type="text" 
                                        class="mt-1 block w-full rounded-lg" 
                                        :value="old('license_no')" 
                                        required />
                                </div>
                                <div>
                                    <x-input-label for="repair_date" :value="__('Date')" class="text-base" />
                                    <x-text-input id="repair_date" name="repair_date" type="date" 
                                        class="mt-1 block w-full rounded-lg" 
                                        :value="old('repair_date', date('Y-m-d'))" 
                                        required />
                                </div>
                            </div>
                        </div>

                        <!-- Repair Details -->
                        <div class="space-y-6">
                            <x-input-label :value="__('Repairs Requested')" class="text-base" />
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                I hereby authorize the Dealership to operate my vehicle on public streets for testing purposes in connection with its rendering of the following repairs:
                            </p>
                            <textarea id="repairs_requested" name="repairs_requested" rows="6" 
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-300" 
                                required>{{ old('repairs_requested') }}</textarea>
                        </div>

                        <!-- Disclaimers and Agreements -->
                        <div class="space-y-6">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-600 dark:text-gray-400">
                                <p class="mb-4">
                                    By signing below, I acknowledge that the above-listed repairs that I am requesting the Dealership to attempt are not covered under the terms of any warranty and that the Dealership is not obligated to perform them. I understand that the Dealership is in no way creating a warranty of any kind on my vehicle by attempting the "goodwill" repairs to the vehicle at no charge to me.
                                </p>
                                <p class="mb-4">
                                    I also understand that the DEALERSHIP HEREBY DISCLAIMS ALL WARRANTIES, EXPRESS OR IMPLIED, INCLUDING ANY IMPLIED WARRANTIES OF MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSE RELATING TO THIS REPAIR AND ALL GOODS AND SERVICES UTILIZED AND/OR PERFORMED IN CONJUNCTION WITH THIS REPAIR.
                                </p>
                                <p>
                                    I further agree that the Dealership will not be liable for any damage to my vehicle or its contents due to fire, theft, an act of nature, or any cause beyond the Dealership's control.
                                </p>
                            </div>

                            <!-- Parts Return Option -->
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-shrink-0">
                                    <input id="discard_parts" name="discard_parts" type="checkbox" value="1"
                                        class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                        {{ old('discard_parts') ? 'checked' : '' }}>
                                </div>
                                <div>
                                    <label for="discard_parts" class="text-base font-medium text-gray-900 dark:text-gray-100">
                                        I hereby request that the Dealership discard any replaced parts.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Signatures -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                            <div>
                                <x-input-label for="customer_signature" :value="__('Customer Signature')" class="text-base" />
                                <div class="mt-1 p-4 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-gray-500">
                                    Signature will be captured in person
                                </div>
                            </div>
                            <div>
                                <x-input-label for="signature_date" :value="__('Date')" class="text-base" />
                                <x-text-input id="signature_date" name="signature_date" type="date" 
                                    class="mt-1 block w-full rounded-lg" 
                                    :value="old('signature_date', date('Y-m-d'))" 
                                    required />
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <a href="{{ route('sales.goodwill-claims.index') }}" 
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" 
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2">
                                {{ __('Create Claim') }}
                            </button>
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
@endsection 