<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Transporter') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('transporters.show', $transporter) }}">
                    <x-shadcn.button variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ __('View Details') }}
                    </x-shadcn.button>
                </a>
                <a href="{{ route('transporters.index') }}">
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
        <div class="container mx-auto space-y-6">
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
                    
                    <form action="{{ route('transporters.update', $transporter) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Basic Information</h3>
                                
                                <!-- Company Name -->
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Company Name *</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="name"
                                        id="name"
                                        :value="old('name', $transporter->name)"
                                        required
                                    />
                                </div>
                                
                                <!-- Contact Person -->
                                <div class="mb-4">
                                    <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="contact_person"
                                        id="contact_person"
                                        :value="old('contact_person', $transporter->contact_person)"
                                    />
                                </div>
                                
                                <!-- Phone -->
                                <div class="mb-4">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="phone"
                                        id="phone"
                                        :value="old('phone', $transporter->phone)"
                                    />
                                </div>
                                
                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <x-shadcn.input
                                        type="email"
                                        name="email"
                                        id="email"
                                        :value="old('email', $transporter->email)"
                                    />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <!-- Password Section -->
                                <div class="border-t border-gray-200 pt-4 mt-4">
                                    <h4 class="font-medium text-gray-700 mb-3">Change Password (Optional)</h4>
                                    
                                    <!-- New Password -->
                                    <div class="mb-4">
                                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                        <x-shadcn.input
                                            type="password"
                                            name="password"
                                            id="password"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Leave blank to keep the current password</p>
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>

                                    <!-- Confirm New Password -->
                                    <div class="mb-4">
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                        <x-shadcn.input
                                            type="password"
                                            name="password_confirmation"
                                            id="password_confirmation"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="mb-4 flex items-center">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('is_active', $transporter->is_active) ? 'checked' : '' }}>
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                                </div>
                            </div>
                            
                            <!-- Address & Additional Information -->
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Address & Additional Information</h3>
                                
                                <!-- Address -->
                                <div class="mb-4">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="address"
                                        id="address"
                                        :value="old('address', $transporter->address)"
                                    />
                                </div>
                                
                                <!-- City, State, Zip in a row -->
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                        <x-shadcn.input
                                            type="text"
                                            name="city"
                                            id="city"
                                            :value="old('city', $transporter->city)"
                                        />
                                    </div>
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                                        <x-shadcn.input
                                            type="text"
                                            name="state"
                                            id="state"
                                            :value="old('state', $transporter->state)"
                                        />
                                    </div>
                                    <div>
                                        <label for="zip" class="block text-sm font-medium text-gray-700">Zip</label>
                                        <x-shadcn.input
                                            type="text"
                                            name="zip"
                                            id="zip"
                                            :value="old('zip', $transporter->zip)"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Notes -->
                                <div class="mb-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes', $transporter->notes) }}</textarea>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="mt-6 flex justify-end">
                                    <x-shadcn.button type="submit" variant="default">
                                        Update Transporter
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