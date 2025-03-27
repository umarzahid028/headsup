<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Goodwill Repairs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Goodwill Repairs Management</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Track and manage goodwill repairs for customers.
                            </p>
                        </div>
                        <div class="flex space-x-4">
                            <a href="{{ route('post-sale.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                Archived Vehicles
                            </a>
                            <a href="{{ route('post-sale.we-owe-items') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                We-Owe Items
                            </a>
                            <a href="{{ route('goodwill-repairs.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                New Goodwill Repair
                            </a>
                        </div>
                    </div>

                    <!-- Pending and In Progress Goodwill Repairs -->
                    <div class="mb-8">
                        <h4 class="text-md font-medium text-gray-800 mb-4">Pending & In Progress Goodwill Repairs</h4>
                        
                        @if($pendingRepairs->isEmpty())
                            <div class="bg-yellow-50 border border-yellow-100 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            No pending goodwill repairs found.
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
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waiver Signed</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($pendingRepairs as $repair)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('vehicles.show', $repair->vehicle) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $repair->vehicle->year }} {{ $repair->vehicle->make }} {{ $repair->vehicle->model }}
                                                    </a>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $repair->vehicle->stock_number }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div>{{ $repair->customer_name }}</div>
                                                    @if($repair->customer_phone)
                                                        <div class="text-xs">{{ $repair->customer_phone }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $repair->title }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $repair->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                                        {{ ucfirst($repair->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if($repair->waiver_signed)
                                                        <span class="text-green-600">
                                                            <svg class="h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                            Signed
                                                        </span>
                                                    @else
                                                        <a href="{{ route('goodwill-repairs.waiver', $repair) }}" class="text-red-600 hover:text-red-800">
                                                            Not Signed
                                                        </a>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if($repair->due_date)
                                                        {{ $repair->due_date->format('M d, Y') }}
                                                    @else
                                                        <span class="text-gray-400">Not set</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    ${{ number_format($repair->cost, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('goodwill-repairs.show', $repair) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                                    <a href="{{ route('goodwill-repairs.edit', $repair) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Recently Completed Goodwill Repairs -->
                    <div>
                        <h4 class="text-md font-medium text-gray-800 mb-4">Recently Completed Goodwill Repairs</h4>
                        
                        @if($completedRepairs->isEmpty())
                            <div class="bg-yellow-50 border border-yellow-100 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            No completed goodwill repairs found.
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
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed By</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed On</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($completedRepairs as $repair)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('vehicles.show', $repair->vehicle) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $repair->vehicle->year }} {{ $repair->vehicle->make }} {{ $repair->vehicle->model }}
                                                    </a>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $repair->vehicle->stock_number }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div>{{ $repair->customer_name }}</div>
                                                    @if($repair->customer_phone)
                                                        <div class="text-xs">{{ $repair->customer_phone }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $repair->title }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $repair->assignedTo ? $repair->assignedTo->name : 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $repair->completed_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    ${{ number_format($repair->cost, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('goodwill-repairs.show', $repair) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                    @if($repair->waiver_pdf_path)
                                                        <a href="{{ route('goodwill-repairs.download-waiver', $repair) }}" class="ml-3 text-red-600 hover:text-red-900">
                                                            PDF
                                                        </a>
                                                    @endif
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
