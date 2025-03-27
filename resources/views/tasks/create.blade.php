<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Task') }}
            </h2>
            <a href="{{ route('tasks.index') }}">
                <x-ui-button variant="outline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Tasks
                </x-ui-button>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui-card>
                <x-slot name="title">Task Details</x-slot>
                
                <form method="POST" action="{{ route('tasks.store') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
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
                                        <option value="{{ $vehicle->id }}" {{ (old('vehicle_id', request('vehicle_id')) == $vehicle->id) ? 'selected' : '' }}>
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
                                        <option value="{{ $user->id }}" {{ old('assigned_to_user_id') == $user->id ? 'selected' : '' }}>
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
                                <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}"
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
                                        <option value="{{ $stage->slug }}" {{ old('stage') == $stage->slug ? 'selected' : '' }}>
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
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
                        <a href="{{ route('tasks.index') }}">
                            <x-ui-button type="button" variant="outline">
                                Cancel
                            </x-ui-button>
                        </a>
                        <x-ui-button type="submit">
                            Create Task
                        </x-ui-button>
                    </div>
                </form>
            </x-ui-card>
        </div>
    </div>
</x-app-layout> 