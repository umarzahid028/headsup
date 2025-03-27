<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('We-Owe Items') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">We-Owe Items Management</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Track and manage we-owe items for sold vehicles.
                            </p>
                        </div>
                        <div class="flex space-x-4">
                            <a href="{{ route('post-sale.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                Archived Vehicles
                            </a>
                            <a href="{{ route('post-sale.goodwill-repairs') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                Goodwill Repairs
                            </a>
                        </div>
                    </div>

                    <!-- Pending We-Owe Items -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-800 mb-4">Pending We-Owe Items</h4>
                        
                        @if($pendingItems->isEmpty())
                            <div class="bg-yellow-50 border border-yellow-100 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            No pending we-owe items found.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pendingItems as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('vehicles.show', $item->vehicle) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $item->vehicle->year }} {{ $item->vehicle->make }} {{ $item->vehicle->model }}
                                                    </a>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $item->vehicle->stock_number }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $item->description }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $item->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if($item->assignedTo)
                                                        {{ $item->assignedTo->name }}
                                                    @else
                                                        <span class="text-gray-400">Unassigned</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if($item->due_date)
                                                        {{ $item->due_date->format('M d, Y') }}
                                                    @else
                                                        <span class="text-gray-400">Not set</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    ${{ number_format($item->cost, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                    <a href="#" class="text-green-600 hover:text-green-900">Complete</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Recently Completed We-Owe Items -->
                    <div>
                        <h4 class="text-md font-medium text-gray-800 mb-4">Recently Completed We-Owe Items</h4>
                        
                        @if($completedItems->isEmpty())
                            <div class="bg-yellow-50 border border-yellow-100 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            No completed we-owe items found.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed By</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed On</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($completedItems as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('vehicles.show', $item->vehicle) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $item->vehicle->year }} {{ $item->vehicle->make }} {{ $item->vehicle->model }}
                                                    </a>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $item->vehicle->stock_number }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $item->description }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $item->assignedTo ? $item->assignedTo->name : 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $item->completed_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    ${{ number_format($item->cost, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
