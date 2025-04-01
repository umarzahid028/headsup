<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Transporter') }}
            </h2>
            <div>
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
                    
                    <form action="{{ route('transporters.store') }}" method="POST">
                        @csrf
                        
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
                                        :value="old('name')"
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
                                        :value="old('contact_person')"
                                    />
                                </div>
                                
                                <!-- Phone -->
                                <div class="mb-4">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="phone"
                                        id="phone"
                                        :value="old('phone')"
                                    />
                                </div>
                                
                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <x-shadcn.input
                                        type="email"
                                        name="email"
                                        id="email"
                                        :value="old('email')"
                                    />
                                </div>
                                
                                <!-- Status -->
                                <div class="mb-4 flex items-center">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
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
                                        :value="old('address')"
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
                                            :value="old('city')"
                                        />
                                    </div>
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                                        <x-shadcn.input
                                            type="text"
                                            name="state"
                                            id="state"
                                            :value="old('state')"
                                        />
                                    </div>
                                    <div>
                                        <label for="zip" class="block text-sm font-medium text-gray-700">Zip</label>
                                        <x-shadcn.input
                                            type="text"
                                            name="zip"
                                            id="zip"
                                            :value="old('zip')"
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
                                        Create Transporter
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