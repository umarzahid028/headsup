<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('We-Owe & Goodwill Items') }}
            </h2>
            <a href="#" onclick="Livewire.dispatch('openModal', { component: 'we-owe.create-modal' })" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('Add We-Owe Item') }}
            </a>
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
                    <h3 class="text-lg font-semibold mb-4">We-Owe & Goodwill Items</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Vehicle
                                    </th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Details
                                    </th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Cost
                                    </th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Assigned To
                                    </th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Due Date
                                    </th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($weOweItems as $item)
                                    <tr>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            @if ($item->vehicle)
                                                <a href="{{ route('vehicles.show', $item->vehicle) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $item->vehicle->year }} {{ $item->vehicle->make }} {{ $item->vehicle->model }} 
                                                    <span class="text-xs text-gray-500">{{ $item->vehicle->vin }}</span>
                                                </a>
                                            @else
                                                <span class="text-gray-500">N/A</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->type === 'we_owe' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                {{ $item->type === 'we_owe' ? 'We-Owe' : 'Goodwill' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <div class="text-sm">{{ $item->details }}</div>
                                            @if ($item->description)
                                                <div class="text-xs text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'Pending',
                                                    'in_progress' => 'In Progress',
                                                    'completed' => 'Completed',
                                                    'cancelled' => 'Cancelled',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$item->status] }}">
                                                {{ $statusLabels[$item->status] }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            ${{ number_format($item->cost, 2) }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            {{ $item->assignedTo?->name ?? 'Unassigned' }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            {{ $item->due_date ? $item->due_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200 text-sm">
                                            <div class="flex space-x-2">
                                                <a href="#" onclick="Livewire.dispatch('openModal', { component: 'we-owe.edit-modal', arguments: { id: {{ $item->id }} }})" class="text-indigo-600 hover:text-indigo-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>
                                                </a>
                                                <a href="#" onclick="Livewire.dispatch('openModal', { component: 'we-owe.delete-modal', arguments: { id: {{ $item->id }} }})" class="text-red-600 hover:text-red-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-4 px-4 border-b border-gray-200 text-center text-gray-500">
                                            No We-Owe or Goodwill items found. 
                                            <a href="#" onclick="Livewire.dispatch('openModal', { component: 'we-owe.create-modal' })" class="text-blue-600 hover:underline">
                                                Create a new item
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $weOweItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 