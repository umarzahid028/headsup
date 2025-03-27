<div class="p-6">
    <h2 class="text-lg font-medium text-gray-900 mb-4">
        {{ __('Create New We-Owe Item') }}
    </h2>

    <form wire:submit.prevent="save">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Vehicle Selection -->
            <div>
                <x-input-label for="vehicle_id" :value="__('Vehicle')" />
                <select id="vehicle_id" wire:model="vehicle_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Select Vehicle</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">
                            {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} - {{ $vehicle->stock_number }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
            </div>

            <!-- Type -->
            <div>
                <x-input-label for="type" :value="__('Type')" />
                <select id="type" wire:model="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="we_owe">We-Owe</option>
                    <option value="goodwill">Goodwill</option>
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <!-- Details -->
            <div class="md:col-span-2">
                <x-input-label for="details" :value="__('Details')" />
                <x-text-input id="details" wire:model="details" class="mt-1 block w-full" type="text" />
                <x-input-error :messages="$errors->get('details')" class="mt-2" />
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" wire:model="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <!-- Cost -->
            <div>
                <x-input-label for="cost" :value="__('Cost')" />
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <x-text-input id="cost" wire:model="cost" class="block w-full pl-7" type="number" step="0.01" min="0" />
                </div>
                <x-input-error :messages="$errors->get('cost')" class="mt-2" />
            </div>

            <!-- Status -->
            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" wire:model="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <!-- Assigned To -->
            <div>
                <x-input-label for="assigned_to" :value="__('Assigned To')" />
                <select id="assigned_to" wire:model="assigned_to" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Not Assigned</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
            </div>

            <!-- Vendor -->
            <div>
                <x-input-label for="vendor_id" :value="__('Vendor')" />
                <select id="vendor_id" wire:model="vendor_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Select Vendor</option>
                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('vendor_id')" class="mt-2" />
            </div>

            <!-- Due Date -->
            <div>
                <x-input-label for="due_date" :value="__('Due Date')" />
                <x-text-input id="due_date" wire:model="due_date" class="mt-1 block w-full" type="date" />
                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button wire:click="$dispatch('closeModal')" class="mr-3">
                {{ __('Cancel') }}
            </x-secondary-button>
            
            <x-primary-button type="submit">
                {{ __('Create') }}
            </x-primary-button>
        </div>
    </form>
</div> 