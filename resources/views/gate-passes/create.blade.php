<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Create New Gate Pass') }}
            </h2>
            <x-button href="{{ route('gate-passes.index') }}" variant="outline">
                <x-heroicon-o-arrow-left class="w-5 h-5 mr-1" />
                {{ __('Back to Gate Passes') }}
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('gate-passes.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                            <!-- Vehicle -->
                            <div>
                                <x-input-label for="vehicle_id" :value="__('Vehicle')" />
                                <x-select id="vehicle_id" name="vehicle_id" class="block w-full mt-1" required>
                                    <option value="">{{ __('Select Vehicle') }}</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id || old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->stock_number }} - {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                        </option>
                                    @endforeach
                                </x-select>
                                <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                            </div>

                            <!-- Transporter -->
                            <div>
                                <x-input-label for="transporter_id" :value="__('Transporter')" />
                                <x-select id="transporter_id" name="transporter_id" class="block w-full mt-1" required>
                                    <option value="">{{ __('Select Transporter') }}</option>
                                    @foreach($transporters as $transporter)
                                        <option value="{{ $transporter->id }}" {{ old('transporter_id') == $transporter->id ? 'selected' : '' }}>
                                            {{ $transporter->name }}
                                        </option>
                                    @endforeach
                                </x-select>
                                <x-input-error :messages="$errors->get('transporter_id')" class="mt-2" />
                            </div>

                            <!-- Batch -->
                            <div>
                                <x-input-label for="batch_id" :value="__('Batch (Optional)')" />
                                <x-select id="batch_id" name="batch_id" class="block w-full mt-1">
                                    <option value="">{{ __('Select Batch') }}</option>
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id || old('batch_id') == $batch->id ? 'selected' : '' }}>
                                            {{ $batch->batch_number }} {{ $batch->name ? "- {$batch->name}" : '' }}
                                        </option>
                                    @endforeach
                                </x-select>
                                <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <x-select id="status" name="status" class="block w-full mt-1" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                    <option value="used" {{ old('status') == 'used' ? 'selected' : '' }}>{{ __('Used') }}</option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                    <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                                </x-select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Issue Date -->
                            <div>
                                <x-input-label for="issue_date" :value="__('Issue Date')" />
                                <x-input id="issue_date" name="issue_date" type="date" class="block w-full mt-1" :value="old('issue_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('issue_date')" class="mt-2" />
                            </div>

                            <!-- Expiry Date -->
                            <div>
                                <x-input-label for="expiry_date" :value="__('Expiry Date')" />
                                <x-input id="expiry_date" name="expiry_date" type="date" class="block w-full mt-1" :value="old('expiry_date', date('Y-m-d', strtotime('+30 days')))" required />
                                <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                            </div>

                            <!-- Gate Pass File -->
                            <div class="md:col-span-2">
                                <x-input-label for="file" :value="__('Gate Pass Document (Optional)')" />
                                <div class="flex items-center mt-1">
                                    <input id="file" name="file" type="file" class="w-full px-3 py-2 text-sm leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" accept=".pdf,.jpg,.jpeg,.png" />
                                </div>
                                <p class="mt-1 text-xs text-gray-500">{{ __('Accepted formats: PDF, JPG, JPEG, PNG. Max size: 10MB') }}</p>
                                <x-input-error :messages="$errors->get('file')" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <x-input-label for="notes" :value="__('Notes (Optional)')" />
                                <textarea id="notes" name="notes" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-button type="submit" class="ml-4">
                                {{ __('Create Gate Pass') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 