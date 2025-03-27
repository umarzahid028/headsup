<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Task Management') }}
            </h2>
            <div>
                <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Add Task') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Task List</h3>
                    
                    @if($tasks->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Task
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Vehicle
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Workflow Stage
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Due Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ $task->title }}
                                                    </a>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ Str::limit($task->description, 50) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($task->vehicle)
                                                    <div class="text-sm text-gray-900">
                                                        <a href="{{ route('vehicles.show', $task->vehicle) }}" class="text-blue-600 hover:text-blue-900">
                                                            {{ $task->vehicle->year }} {{ $task->vehicle->make }} {{ $task->vehicle->model }}
                                                        </a>
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $task->vehicle->stock_number }}
                                                    </div>
                                                @else
                                                    <span class="text-gray-500">Not Assigned</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    {{ $task->workflowStage->name ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                                        'completed' => 'bg-green-100 text-green-800',
                                                        'cancelled' => 'bg-red-100 text-red-800',
                                                    ];
                                                    $statusClass = $statusClasses[$task->status] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($task->due_date)
                                                    {{ $task->due_date->format('M d, Y') }}
                                                    @if($task->isOverdue() && $task->status !== 'completed')
                                                        <span class="text-red-600 font-semibold">(Overdue)</span>
                                                    @endif
                                                @else
                                                    No Due Date
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    
                                                    @if($task->status !== 'completed')
                                                        <form action="{{ route('tasks.complete', $task) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-green-600 hover:text-green-900">Complete</button>
                                                        </form>
                                                    @endif
                                                    
                                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">No tasks found.</p>
                            <a href="{{ route('tasks.create') }}" class="inline-block mt-2 text-blue-600 hover:text-blue-800">Create a new task</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 