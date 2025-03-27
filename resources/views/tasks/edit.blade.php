<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Task') }}: {{ $task->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tasks.index') }}">
                    <x-ui-button variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back to Tasks
                    </x-ui-button>
                </a>
                @if($task->vehicle)
                <a href="{{ route('vehicles.show', $task->vehicle) }}">
                    <x-ui-button variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                        </svg>
                        View Vehicle
                    </x-ui-button>
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui-card>
                <x-slot name="title">Task Details</x-slot>
                
                <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('description') border-red-500 @enderror">{{ old('description', $task->description) }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-1">Related Vehicle</label>
                                <select id="vehicle_id" name="vehicle_id" 
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('vehicle_id') border-red-500 @enderror">
                                    <option value="">No Vehicle</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $task->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->vin }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="assigned_to_user_id" class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                                <select id="assigned_to_user_id" name="assigned_to_user_id" 
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('assigned_to_user_id') border-red-500 @enderror">
                                    <option value="">Unassigned</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to_user_id', $task->assigned_to_user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to_user_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('due_date') border-red-500 @enderror">
                                @error('due_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="stage" class="block text-sm font-medium text-gray-700 mb-1">Associated Stage *</label>
                                <select id="stage" name="stage" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('stage') border-red-500 @enderror">
                                    @foreach ($stages as $stage)
                                        <option value="{{ $stage->slug }}" {{ old('stage', $task->stage) == $stage->slug ? 'selected' : '' }}>
                                            {{ $stage->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('stage')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                <select id="status" name="status" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('status') border-red-500 @enderror">
                                    <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="blocked" {{ old('status', $task->status) == 'blocked' ? 'selected' : '' }}>Blocked</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200 flex justify-between">
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <x-ui-button type="submit" variant="destructive">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Delete Task
                            </x-ui-button>
                        </form>
                        
                        <div class="flex gap-3">
                            <a href="{{ route('tasks.index') }}">
                                <x-ui-button type="button" variant="outline">
                                    Cancel
                                </x-ui-button>
                            </a>
                            <x-ui-button type="submit">
                                Update Task
                            </x-ui-button>
                        </div>
                    </div>
                </form>
            </x-ui-card>
        </div>
    </div>
</x-app-layout> 