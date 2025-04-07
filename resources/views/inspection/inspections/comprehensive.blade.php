<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Comprehensive Inspection
        </h2>
    </x-slot>

<!-- Repair/Replace Form -->
<div class="repair-form hidden mt-4 space-y-4" id="repair-form-{{ $item->id }}">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Cost Field -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Actual Cost
            </label>
            <input type="number" 
                step="0.01" 
                name="items[{{ $item->id }}][cost]" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Enter actual cost"
                value="{{ isset($existingInspection) ? optional($existingInspection->itemResults->where('inspection_item_id', $item->id)->first())->cost : '' }}">
        </div>

        <!-- Completion Status -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Work Status
            </label>
            <select name="items[{{ $item->id }}][repair_status]" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                onchange="toggleCompletionFields({{ $item->id }}, this.value)">
                <option value="">Select Status</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
    </div>

    <!-- Completion Fields (hidden by default) -->
    <div id="completion-fields-{{ $item->id }}" class="hidden space-y-4">
        <!-- Completion Notes -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Completion Notes
            </label>
            <textarea 
                name="items[{{ $item->id }}][completion_notes]" 
                rows="2" 
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Describe the work completed">{{ isset($existingInspection) ? optional($existingInspection->itemResults->where('inspection_item_id', $item->id)->first())->completion_notes : '' }}</textarea>
        </div>

        <!-- Completion Image Upload -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Work Completion Images
            </label>
            
            <!-- Existing Completion Images -->
            @if(isset($existingInspection) && $existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->repairImages->where('type', 'like', '%completed')->count() > 0)
                <div class="flex flex-wrap gap-2 mb-2">
                    @foreach($existingInspection->itemResults->where('inspection_item_id', $item->id)->first()->repairImages->where('type', 'like', '%completed') as $image)
                        <div class="relative group">
                            <img src="{{ Storage::url($image->image_path) }}" 
                                alt="Completion image" 
                                class="h-16 w-16 object-cover rounded-lg border border-gray-200"
                                onclick="window.open('{{ Storage::url($image->image_path) }}', '_blank')">
                            <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                                <a href="{{ Storage::url($image->image_path) }}" 
                                    target="_blank"
                                    class="p-1.5 bg-white rounded-full text-gray-700 hover:text-gray-900 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- New Completion Image Upload -->
            <div class="flex items-center justify-center w-full">
                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-200">
                    <div class="flex flex-col items-center justify-center pt-4 pb-3">
                        <svg class="w-6 h-6 mb-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <p class="text-sm text-gray-500">
                            <span class="font-medium">Upload completion images</span>
                        </p>
                    </div>
                    <input type="file" 
                        name="items[{{ $item->id }}][completion_images][]" 
                        accept="image/*" 
                        multiple
                        class="hidden"
                        onchange="previewCompletionImages(this, {{ $item->id }})"
                    >
                </label>
            </div>

            <!-- Completion Image Preview -->
            <div id="completion-image-preview-{{ $item->id }}" class="flex flex-wrap gap-2 mt-2"></div>
        </div>
    </div>
</div>

</x-app-layout>

@push('scripts')
<script>
    function toggleCompletionFields(itemId, status) {
        const completionFields = document.getElementById(`completion-fields-${itemId}`);
        if (status === 'completed') {
            completionFields.classList.remove('hidden');
        } else {
            completionFields.classList.add('hidden');
        }
    }

    function previewCompletionImages(input, itemId) {
        const previewContainer = document.getElementById(`completion-image-preview-${itemId}`);
        previewContainer.innerHTML = ''; // Clear existing previews

        if (input.files) {
            Array.from(input.files).forEach(file => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewWrapper = document.createElement('div');
                    previewWrapper.className = 'relative group';
                    
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.className = 'h-16 w-16 object-cover rounded-lg border border-gray-200 shadow-sm';
                    
                    const removeButton = document.createElement('button');
                    removeButton.className = 'absolute -top-1 -right-1 p-1 bg-red-500 rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-sm';
                    removeButton.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    `;
                    removeButton.onclick = function(e) {
                        e.preventDefault();
                        previewWrapper.remove();
                        
                        // Create a new FileList without the removed file
                        const dt = new DataTransfer();
                        Array.from(input.files).forEach(f => {
                            if (f !== file) dt.items.add(f);
                        });
                        input.files = dt.files;
                    };
                    
                    previewWrapper.appendChild(preview);
                    previewWrapper.appendChild(removeButton);
                    previewContainer.appendChild(previewWrapper);
                };
                reader.readAsDataURL(file);
            });
        }
    }
</script>
@endpush
