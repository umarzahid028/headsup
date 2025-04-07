@php
    use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex flex-col space-y-1">
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    {{ __('Edit Vehicle') }}
                    <span class="text-sm font-normal bg-gray-100 text-gray-600 px-2 py-1 rounded">
                        {{ $vehicle->stock_number }}
                    </span>
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} {{ $vehicle->trim }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('vehicles.show', $vehicle) }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <x-heroicon-o-eye class="h-4 w-4 mr-2" />
                    {{ __('View Details') }}
                </a>
                <a href="{{ route('vehicles.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <x-heroicon-o-arrow-left class="h-4 w-4 mr-2" />
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <x-heroicon-m-x-circle class="h-5 w-5 text-red-400" />
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        There {{ $errors->count() === 1 ? 'is' : 'are' }} {{ $errors->count() }} {{ Str::plural('error', $errors->count()) }} with your submission
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc space-y-1 pl-5">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('vehicles.update', $vehicle->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Basic Information -->
                            <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Basic Information</h3>
                                
                                <!-- Stock Number -->
                                <div class="mb-4">
                                    <label for="stock_number" class="block text-sm font-medium text-gray-700">Stock Number *</label>
                                    <x-shadcn.input 
                                        type="text" 
                                        name="stock_number" 
                                        id="stock_number" 
                                        :value="old('stock_number', $vehicle->stock_number)" 
                                        required 
                                    />
                                </div>
                                
                                <!-- VIN -->
                                <div class="mb-4">
                                    <label for="vin" class="block text-sm font-medium text-gray-700">VIN *</label>
                                    <x-shadcn.input 
                                        type="text" 
                                        name="vin" 
                                        id="vin" 
                                        :value="old('vin', $vehicle->vin)" 
                                        required 
                                    />
                                </div>
                                
                                <!-- Year -->
                                <div class="mb-4">
                                    <label for="year" class="block text-sm font-medium text-gray-700">Year *</label>
                                    <input type="number" name="year" id="year" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                                </div>
                                
                                <!-- Make -->
                                <div class="mb-4">
                                    <label for="make" class="block text-sm font-medium text-gray-700">Make *</label>
                                    <input type="text" name="make" id="make" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('make', $vehicle->make) }}" required>
                                </div>
                                
                                <!-- Model -->
                                <div class="mb-4">
                                    <label for="model" class="block text-sm font-medium text-gray-700">Model *</label>
                                    <input type="text" name="model" id="model" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('model', $vehicle->model) }}" required>
                                </div>
                                
                                <!-- Trim -->
                                <div class="mb-4">
                                    <label for="trim" class="block text-sm font-medium text-gray-700">Trim</label>
                                    <input type="text" name="trim" id="trim" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('trim', $vehicle->trim) }}">
                                </div>
                            </div>
                            
                            <!-- Vehicle Details -->
                            <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Vehicle Details</h3>
                                
                                <!-- Odometer -->
                                <div class="mb-4">
                                    <label for="odometer" class="block text-sm font-medium text-gray-700">Odometer</label>
                                    <input type="number" name="odometer" id="odometer" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('odometer', $vehicle->odometer) }}">
                                </div>
                                
                                <!-- Exterior Color -->
                                <div class="mb-4">
                                    <label for="exterior_color" class="block text-sm font-medium text-gray-700">Exterior Color</label>
                                    <input type="text" name="exterior_color" id="exterior_color" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('exterior_color', $vehicle->exterior_color) }}">
                                </div>
                                
                                <!-- Interior Color -->
                                <div class="mb-4">
                                    <label for="interior_color" class="block text-sm font-medium text-gray-700">Interior Color</label>
                                    <input type="text" name="interior_color" id="interior_color" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('interior_color', $vehicle->interior_color) }}">
                                </div>
                                
                                <!-- Body Type -->
                                <div class="mb-4">
                                    <label for="body_type" class="block text-sm font-medium text-gray-700">Body Type</label>
                                    <input type="text" name="body_type" id="body_type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('body_type', $vehicle->body_type) }}">
                                </div>
                                
                                <!-- Drive Train -->
                                <div class="mb-4">
                                    <label for="drive_train" class="block text-sm font-medium text-gray-700">Drive Train</label>
                                    <input type="text" name="drive_train" id="drive_train" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('drive_train', $vehicle->drive_train) }}">
                                </div>
                                
                                <!-- Engine -->
                                <div class="mb-4">
                                    <label for="engine" class="block text-sm font-medium text-gray-700">Engine</label>
                                    <input type="text" name="engine" id="engine" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('engine', $vehicle->engine) }}">
                                </div>
                                
                                <!-- Fuel Type -->
                                <div class="mb-4">
                                    <label for="fuel_type" class="block text-sm font-medium text-gray-700">Fuel Type</label>
                                    <input type="text" name="fuel_type" id="fuel_type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('fuel_type', $vehicle->fuel_type) }}">
                                </div>
                                
                                <!-- Transmission -->
                                <div class="mb-4">
                                    <label for="transmission" class="block text-sm font-medium text-gray-700">Transmission</label>
                                    <input type="text" name="transmission" id="transmission" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('transmission', $vehicle->transmission) }}">
                                </div>
                                
                                <!-- Transmission Type -->
                                <div class="mb-4">
                                    <label for="transmission_type" class="block text-sm font-medium text-gray-700">Transmission Type</label>
                                    <input type="text" name="transmission_type" id="transmission_type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('transmission_type', $vehicle->transmission_type) }}">
                                </div>
                            </div>
                            
                            <!-- Pricing & Status -->
                            <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Pricing & Status</h3>
                                
                                <!-- Advertising Price -->
                                <div class="mb-4">
                                    <label for="advertising_price" class="block text-sm font-medium text-gray-700">Advertising Price</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" name="advertising_price" id="advertising_price" class="pl-7 mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('advertising_price', $vehicle->advertising_price) }}" step="0.01">
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <x-shadcn.select name="status" id="status" placeholder="-- Select Status --">
                                        <option value="available" {{ old('status', $vehicle->status) == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="pending" {{ old('status', $vehicle->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="sold" {{ old('status', $vehicle->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                                        <option value="in_transit" {{ old('status', $vehicle->status) == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                    </x-shadcn.select>
                                </div>
                                
                                <!-- Date in Stock -->
                                <div class="mb-4">
                                    <label for="date_in_stock" class="block text-sm font-medium text-gray-700">Date in Stock</label>
                                    <input type="date" name="date_in_stock" id="date_in_stock" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('date_in_stock', $vehicle->date_in_stock ? $vehicle->date_in_stock->format('Y-m-d') : '') }}">
                                </div>

                                <!-- Sold Date (only show if status is sold) -->
                                <div class="mb-4" id="sold_date_container" style="{{ old('status', $vehicle->status) == 'sold' ? 'display:block' : 'display:none' }}">
                                    <label for="sold_date" class="block text-sm font-medium text-gray-700">Sold Date</label>
                                    <input type="date" name="sold_date" id="sold_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('sold_date', $vehicle->sold_date ? $vehicle->sold_date->format('Y-m-d') : '') }}">
                                </div>
                                
                                <!-- Buyer Name (only show if status is sold) -->
                                <div class="mb-4" id="buyer_name_container" style="{{ old('status', $vehicle->status) == 'sold' ? 'display:block' : 'display:none' }}">
                                    <label for="buyer_name" class="block text-sm font-medium text-gray-700">Buyer Name</label>
                                    <input type="text" name="buyer_name" id="buyer_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ old('buyer_name', $vehicle->buyer_name) }}">
                                </div>
                                
                                <!-- Featured -->
                                <div class="mb-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="is_featured" id="is_featured" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" value="1" {{ $vehicle->is_featured ? 'checked' : '' }}>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_featured" class="font-medium text-gray-700">Featured Vehicle</label>
                                            <p class="text-gray-500">Show this vehicle prominently on the website</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Vehicle Images -->
                        <div class="col-span-full mt-6">
                            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                                <div class="flex items-center justify-between mb-6 pb-2 border-b">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Vehicle Images</h3>
                                        <p class="mt-1 text-sm text-gray-500">Manage the main image and additional gallery images for this vehicle.</p>
                                    </div>
                                    <x-heroicon-o-photo class="h-5 w-5 text-gray-400" />
                                </div>

                                <div class="space-y-8">
                                    <!-- Main Vehicle Image -->
                                    <div>
                                        <h4 class="text-base font-medium text-gray-900 mb-4">Main Vehicle Image</h4>
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            <!-- Current Image -->
                                            <div>
                                                <p class="text-sm text-gray-600 mb-2">Current main image:</p>
                                                <div class="relative group">
                                                    <img 
                                                        src="{{ $vehicle->image_url }}" 
                                                        alt="{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}" 
                                                        class="w-full h-64 object-cover rounded-lg border border-gray-200"
                                                    >
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all rounded-lg flex items-center justify-center">
                                                        <div class="hidden group-hover:flex space-x-2">
                                                            <button type="button" class="p-2 bg-white rounded-full text-gray-700 hover:text-indigo-600 transition-colors" onclick="window.open('{{ $vehicle->image_url }}', '_blank')" title="View full size">
                                                                <x-heroicon-o-eye class="h-5 w-5" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Upload New Image -->
                                            <div>
                                                <p class="text-sm text-gray-600 mb-2">Upload new main image:</p>
                                                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg" id="main_image_drop_zone">
                                                    <div class="space-y-2 text-center">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        <div class="flex text-sm text-gray-600 justify-center">
                                                            <label for="vehicle_image" class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                                <span>Upload main image</span>
                                                                <input id="vehicle_image" name="vehicle_image" type="file" class="sr-only" accept="image/*">
                                                            </label>
                                                            <p class="pl-1">or drag and drop</p>
                                                        </div>
                                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                                    </div>
                                                </div>
                                                <div id="main_image_preview" class="mt-4 hidden">
                                                    <div class="relative">
                                                        <img src="" alt="New main image preview" class="w-full h-48 object-cover rounded-lg">
                                                        <button type="button" onclick="removeMainImage()" class="absolute top-2 right-2 p-1.5 bg-white rounded-full text-gray-400 hover:text-red-500 transition-colors">
                                                            <x-heroicon-o-x-mark class="h-5 w-5" />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gallery Images -->
                                    <div>
                                        <h4 class="text-base font-medium text-gray-900 mb-4">Gallery Images</h4>
                                        
                                        <!-- Current Gallery Images -->
                                        <div class="mb-6">
                                            <p class="text-sm text-gray-600 mb-3">Current gallery images:</p>
                                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="image-gallery">
                                                @forelse($vehicle->images()->orderBy('sort_order')->get() as $image)
                                                <div class="relative group" data-image-id="{{ $image->id }}">
                                                    <img 
                                                        src="{{ $image->image_url }}" 
                                                        alt="Gallery image" 
                                                        class="w-full h-40 object-cover rounded-lg border border-gray-200 {{ $image->is_featured ? 'ring-2 ring-indigo-500' : '' }}"
                                                    >
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all rounded-lg flex items-center justify-center">
                                                        <div class="hidden group-hover:flex space-x-2">
                                                            <button type="button" onclick="window.open('{{ $image->image_url }}', '_blank')" class="p-1.5 bg-white rounded-full text-gray-700 hover:text-indigo-600 transition-colors" title="View full size">
                                                                <x-heroicon-o-eye class="h-5 w-5" />
                                                            </button>
                                                            @if(!$image->is_featured)
                                                            <button 
                                                                type="button" 
                                                                class="p-1.5 bg-white rounded-full text-gray-700 hover:text-indigo-600 transition-colors featured-toggle"
                                                                title="Set as featured"
                                                                data-image-id="{{ $image->id }}"
                                                            >
                                                                <x-heroicon-o-star class="h-5 w-5" />
                                                            </button>
                                                            @endif
                                                            <button 
                                                                type="button" 
                                                                class="p-1.5 bg-white rounded-full text-gray-700 hover:text-red-600 transition-colors delete-image"
                                                                title="Delete image"
                                                                data-image-id="{{ $image->id }}"
                                                            >
                                                                <x-heroicon-o-trash class="h-5 w-5" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @if($image->is_featured)
                                                    <div class="absolute top-2 right-2 bg-indigo-500 text-white rounded-full p-1.5" title="Featured image">
                                                        <x-heroicon-o-star class="h-4 w-4" />
                                                    </div>
                                                    @endif
                                                </div>
                                                @empty
                                                <div class="col-span-full flex flex-col items-center justify-center py-8 text-gray-500 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                                    <x-heroicon-o-photo class="h-12 w-12 text-gray-400 mb-2" />
                                                    <p>No additional images yet</p>
                                                </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <!-- Upload Additional Images -->
                                        <div>
                                            <p class="text-sm text-gray-600 mb-3">Upload additional images:</p>
                                            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg" id="gallery_images_drop_zone">
                                                <div class="space-y-2 text-center">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <div class="flex text-sm text-gray-600 justify-center">
                                                        <label for="gallery_images" class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                            <span>Upload multiple images</span>
                                                            <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" multiple accept="image/*">
                                                        </label>
                                                        <p class="pl-1">or drag and drop</p>
                                                    </div>
                                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB each</p>
                                                </div>
                                            </div>
                                            <div id="gallery_images_preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 hidden"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 pt-5">
                            <div class="flex justify-end">
                                <x-shadcn.button 
                                    type="submit" 
                                    variant="default" 
                                    class="mt-4"
                                >
                                    Update Vehicle
                                </x-shadcn.button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show/hide sold date and buyer name fields based on status
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const soldDateContainer = document.getElementById('sold_date_container');
            const buyerNameContainer = document.getElementById('buyer_name_container');

            statusSelect.addEventListener('change', function() {
                if (this.value === 'sold') {
                    soldDateContainer.style.display = 'block';
                    buyerNameContainer.style.display = 'block';
                } else {
                    soldDateContainer.style.display = 'none';
                    buyerNameContainer.style.display = 'none';
                }
            });
        });

        document.getElementById('vehicle_image').onchange = function(evt) {
            const [file] = this.files;
            if (file) {
                const previewContainer = document.getElementById('image_preview_container');
                const preview = document.getElementById('image_preview');
                preview.src = URL.createObjectURL(file);
                previewContainer.classList.remove('hidden');
            }
        }
    </script>

    @section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Status change handler
            const statusSelect = document.getElementById('status');
            const soldDateContainer = document.getElementById('sold_date_container');
            const buyerNameContainer = document.getElementById('buyer_name_container');

            function updateSoldFields() {
                const isSold = statusSelect.value === 'sold';
                soldDateContainer.style.display = isSold ? 'block' : 'none';
                buyerNameContainer.style.display = isSold ? 'block' : 'none';
                
                if (isSold && !document.getElementById('sold_date').value) {
                    document.getElementById('sold_date').value = new Date().toISOString().split('T')[0];
                }
            }

            statusSelect.addEventListener('change', updateSoldFields);
            updateSoldFields(); // Run on initial load

            // Main image upload handling
            const mainImageInput = document.getElementById('vehicle_image');
            const mainImageDropZone = document.getElementById('main_image_drop_zone');
            const mainImagePreview = document.getElementById('main_image_preview');
            const mainImagePreviewImg = mainImagePreview.querySelector('img');

            function handleMainImage(file) {
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        mainImagePreviewImg.src = e.target.result;
                        mainImagePreview.classList.remove('hidden');
                        mainImageDropZone.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            }

            function removeMainImage() {
                mainImageInput.value = '';
                mainImagePreview.classList.add('hidden');
                mainImageDropZone.classList.remove('hidden');
            }

            mainImageInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    handleMainImage(this.files[0]);
                }
            });

            // Gallery images upload handling
            const galleryInput = document.getElementById('gallery_images');
            const galleryDropZone = document.getElementById('gallery_images_drop_zone');
            const galleryPreview = document.getElementById('gallery_images_preview');

            function handleGalleryImages(files) {
                if (files.length > 0) {
                    galleryPreview.innerHTML = '';
                    Array.from(files).forEach((file, index) => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const previewDiv = document.createElement('div');
                                previewDiv.className = 'relative group';
                                previewDiv.innerHTML = `
                                    <img src="${e.target.result}" alt="Gallery preview ${index + 1}" class="w-full h-40 object-cover rounded-lg">
                                    <button type="button" onclick="removeGalleryImage(${index})" class="absolute top-2 right-2 p-1.5 bg-white rounded-full text-gray-400 hover:text-red-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                `;
                                galleryPreview.appendChild(previewDiv);
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                    galleryPreview.classList.remove('hidden');
                    galleryDropZone.classList.add('hidden');
                }
            }

            function removeGalleryImage(index) {
                const dt = new DataTransfer();
                const { files } = galleryInput;
                
                for (let i = 0; i < files.length; i++) {
                    if (i !== index) {
                        dt.items.add(files[i]);
                    }
                }
                
                galleryInput.files = dt.files;
                if (galleryInput.files.length === 0) {
                    galleryPreview.classList.add('hidden');
                    galleryDropZone.classList.remove('hidden');
                } else {
                    handleGalleryImages(galleryInput.files);
                }
            }

            galleryInput.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    handleGalleryImages(this.files);
                }
            });

            // Drag and drop handling
            [mainImageDropZone, galleryDropZone].forEach(dropZone => {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, unhighlight, false);
                });

                function highlight(e) {
                    dropZone.classList.add('border-indigo-600', 'border-2');
                }

                function unhighlight(e) {
                    dropZone.classList.remove('border-indigo-600', 'border-2');
                }
            });

            mainImageDropZone.addEventListener('drop', function(e) {
                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    mainImageInput.files = e.dataTransfer.files;
                    handleMainImage(file);
                }
            });

            galleryDropZone.addEventListener('drop', function(e) {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    galleryInput.files = files;
                    handleGalleryImages(files);
                }
            });

            // Image gallery management
            const deleteButtons = document.querySelectorAll('.delete-image');
            const featureButtons = document.querySelectorAll('.featured-toggle');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this image?')) {
                        const imageId = this.dataset.imageId;
                        const imageCard = this.closest('[data-image-id]');
                        
                        fetch(`/vehicles/${vehicleId}/images/${imageId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                imageCard.remove();
                                if (document.querySelectorAll('#image-gallery > div').length === 0) {
                                    document.getElementById('image-gallery').innerHTML = `
                                        <div class="col-span-full flex flex-col items-center justify-center py-8 text-gray-500 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                            <svg class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p>No additional images yet</p>
                                        </div>
                                    `;
                                }
                            } else {
                                alert(data.message || 'Failed to delete image');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the image');
                        });
                    }
                });
            });

            featureButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const imageId = this.dataset.imageId;
                    const imageCard = this.closest('[data-image-id]');
                    
                    fetch(`/vehicles/${vehicleId}/images/${imageId}/feature`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove featured status from all images
                            document.querySelectorAll('.featured-badge').forEach(badge => badge.remove());
                            document.querySelectorAll('[data-image-id]').forEach(card => {
                                card.querySelector('img').classList.remove('ring-2', 'ring-indigo-500');
                            });
                            
                            // Add featured status to the selected image
                            const img = imageCard.querySelector('img');
                            img.classList.add('ring-2', 'ring-indigo-500');
                            const badge = document.createElement('div');
                            badge.className = 'featured-badge absolute top-2 right-2 bg-indigo-500 text-white rounded-full p-1.5';
                            badge.title = 'Featured image';
                            badge.innerHTML = `
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            `;
                            imageCard.appendChild(badge);
                        } else {
                            alert(data.message || 'Failed to set featured image');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while setting the featured image');
                    });
                });
            });
        });

        window.removeGalleryImage = removeGalleryImage;
        window.removeMainImage = removeMainImage;
    </script>
    @endsection
</x-app-layout> 