<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('vehicles.index') }}">
                    <x-ui-button variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back to List
                    </x-ui-button>
                </a>
                
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'sales_manager')
                <a href="{{ route('vehicles.edit', $vehicle) }}">
                    <x-ui-button>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit Vehicle
                    </x-ui-button>
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Vehicle Info Card -->
                <x-ui-card class="lg:col-span-2">
                    <x-slot name="title">Vehicle Information</x-slot>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Left Column -->
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">VIN</h3>
                                <p class="text-base">{{ $vehicle->vin }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Stock Number</h3>
                                <p class="text-base">{{ $vehicle->stock_number ?? 'Not Assigned' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Year / Make / Model</h3>
                                <p class="text-base">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Trim / Color</h3>
                                <p class="text-base">{{ $vehicle->trim ?? 'Base' }} / {{ $vehicle->color }}</p>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div>
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Mileage</h3>
                                <p class="text-base">{{ number_format($vehicle->mileage) }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Purchase Date</h3>
                                <p class="text-base">{{ $vehicle->purchase_date ? $vehicle->purchase_date->format('M d, Y') : 'Not Recorded' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Purchase Price</h3>
                                <p class="text-base">${{ number_format($vehicle->purchase_price, 2) }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                                <div class="mt-1">
                                    @if($vehicle->is_frontline_ready)
                                        <x-ui-badge variant="success">Frontline Ready</x-ui-badge>
                                    @elseif($vehicle->is_archived)
                                        <x-ui-badge variant="gray">Archived</x-ui-badge>
                                    @elseif($vehicle->is_sold)
                                        <x-ui-badge variant="info">Sold</x-ui-badge>
                                    @else
                                        <x-ui-badge variant="warning">In Process</x-ui-badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($vehicle->notes)
                    <div class="mt-4 pt-4 border-t">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Notes</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $vehicle->notes }}</p>
                    </div>
                    @endif
                </x-ui-card>
                
                <!-- Stage Info Card -->
                <x-ui-card>
                    <x-slot name="title">Current Stage</x-slot>
                    
                    @php 
                        $stageBadgeColors = [
                            'intake' => 'gray',
                            'test_drive' => 'info',
                            'arbitration' => 'danger',
                            'mechanical' => 'warning',
                            'exterior' => 'warning',
                            'interior' => 'warning',
                            'features' => 'info',
                            'tires_brakes' => 'warning',
                            'detail' => 'info',
                            'walkaround' => 'info',
                            'photos' => 'info',
                            'frontline' => 'success',
                        ];
                        $stageColor = $stageBadgeColors[$vehicle->current_stage] ?? 'default';
                        $currentStage = $stages->firstWhere('slug', $vehicle->current_stage);
                    @endphp
                    
                    <div class="mb-6 flex justify-center">
                        <x-ui-badge :variant="$stageColor" class="text-lg py-1 px-3">
                            {{ $currentStage->name ?? 'Unknown' }}
                        </x-ui-badge>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Time in Current Stage</h3>
                        <p class="text-base">{{ $vehicle->daysInCurrentStage() }} days</p>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Stage Started</h3>
                        <p class="text-base">{{ $vehicle->stage_start_date ? $vehicle->stage_start_date->format('M d, Y') : 'Not Recorded' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Total Days in Process</h3>
                        <p class="text-base">{{ $vehicle->daysInProcess() }}</p>
                    </div>
                    
                    <div class="mt-6">
                        <form method="POST" action="{{ route('vehicles.update.stage', $vehicle) }}">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-3">
                                <label for="stage" class="block text-sm font-medium text-gray-700 mb-1">Update Stage</label>
                                <select name="stage" id="stage" 
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                                    @foreach ($stages as $stage)
                                        <option value="{{ $stage->slug }}" {{ $vehicle->current_stage == $stage->slug ? 'selected' : '' }}>
                                            {{ $stage->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <x-ui-button type="submit" class="w-full justify-center">
                                Update Stage
                            </x-ui-button>
                        </form>
                    </div>
                </x-ui-card>
            </div>
            
            <!-- Tasks Card -->
            <x-ui-card class="mt-6">
                <x-slot name="title">Tasks</x-slot>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($vehicle->tasks as $task)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($task->description, 50) }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <x-ui-badge :variant="$stageBadgeColors[$task->stage] ?? 'default'">
                                            {{ $stages->firstWhere('slug', $task->stage)->name ?? 'Unknown' }}
                                        </x-ui-badge>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $task->assignedUser ? $task->assignedUser->name : 'Unassigned' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($task->status === 'completed')
                                            <x-ui-badge variant="success">Completed</x-ui-badge>
                                        @elseif($task->status === 'in_progress')
                                            <x-ui-badge variant="warning">In Progress</x-ui-badge>
                                        @elseif($task->status === 'pending')
                                            <x-ui-badge variant="info">Pending</x-ui-badge>
                                        @elseif($task->status === 'blocked')
                                            <x-ui-badge variant="danger">Blocked</x-ui-badge>
                                        @else
                                            <x-ui-badge>Unknown</x-ui-badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No Due Date' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('tasks.edit', $task) }}" class="text-yellow-600 hover:text-yellow-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('tasks.complete', $task) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:text-green-800 {{ $task->status === 'completed' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                        {{ $task->status === 'completed' ? 'disabled' : '' }}>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                                        No tasks associated with this vehicle
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <a href="{{ route('tasks.create', ['vehicle_id' => $vehicle->id]) }}">
                        <x-ui-button variant="outline" class="w-full justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 000 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Add New Task
                        </x-ui-button>
                    </a>
                </div>
            </x-ui-card>
            
            <!-- Documents Card -->
            <x-ui-card class="mt-6">
                <x-slot name="title">Documents</x-slot>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded By</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upload Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($vehicle->documents as $document)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $document->title }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($document->type) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $document->user ? $document->user->name : 'System' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $document->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('documents.download', $document) }}" class="text-blue-600 hover:text-blue-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'sales_manager')
                                            <form method="POST" action="{{ route('documents.destroy', $document) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this document?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                        No documents associated with this vehicle
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Document Title</label>
                                <input type="text" id="title" name="title" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                            </div>
                            
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                                <select id="type" name="type" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                                    <option value="invoice">Invoice</option>
                                    <option value="title">Title</option>
                                    <option value="inspection">Inspection</option>
                                    <option value="repair">Repair Order</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">Document File</label>
                                <input type="file" id="file" name="file" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                            </div>
                        </div>
                        
                        <div>
                            <x-ui-button type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                Upload Document
                            </x-ui-button>
                        </div>
                    </form>
                </div>
            </x-ui-card>
        </div>
    </div>
</x-app-layout> 