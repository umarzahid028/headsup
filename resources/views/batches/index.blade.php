<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Batch Management') }}
            </h2>
            <x-button href="{{ route('batches.create') }}">
                <x-heroicon-o-plus class="w-5 h-5 mr-1" />
                {{ __('New Batch') }}
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <form method="GET" action="{{ route('batches.index') }}" class="flex items-center gap-4">
                            <div class="flex-grow">
                                <x-input-label for="search" class="sr-only">{{ __('Search') }}</x-input-label>
                                <x-input id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by batch number, name, destination, or transporter...') }}" class="w-full" />
                            </div>
                            <div class="w-48">
                                <x-input-label for="status" class="sr-only">{{ __('Status') }}</x-input-label>
                                <x-select id="status" name="status" class="w-full">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>{{ __('In Transit') }}</option>
                                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                </x-select>
                            </div>
                            <div>
                                <x-button type="submit">
                                    <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-1" />
                                    {{ __('Search') }}
                                </x-button>
                            </div>
                            @if(request('search') || request('status'))
                                <div>
                                    <x-button href="{{ route('batches.index') }}" variant="outline">
                                        <x-heroicon-o-x-mark class="w-5 h-5 mr-1" />
                                        {{ __('Clear') }}
                                    </x-button>
                                </div>
                            @endif
                        </form>
                    </div>

                    @if($batches->isEmpty())
                        <div class="p-12 text-center">
                            <x-heroicon-o-truck class="w-16 h-16 mx-auto text-gray-400" />
                            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('No Batches Found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ request('search') || request('status') ? 
                                    __('No batches match your search criteria.') : 
                                    __('Get started by creating a new batch for vehicle transportation.') }}
                            </p>
                            <div class="mt-6">
                                <x-button href="{{ route('batches.create') }}">
                                    <x-heroicon-o-plus class="w-5 h-5 mr-1" />
                                    {{ __('New Batch') }}
                                </x-button>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Batch Number & Name') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Transporter') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Route') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Schedule') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Status') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Vehicles') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($batches as $batch)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $batch->batch_number }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $batch->name ?? '-' }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    {{ __('Created') }}: {{ $batch->created_at->format('M d, Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $batch->transporter->name ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if($batch->origin && $batch->destination)
                                                        {{ $batch->origin }} → {{ $batch->destination }}
                                                    @elseif($batch->destination)
                                                        {{ __('To') }}: {{ $batch->destination }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if($batch->scheduled_pickup_date)
                                                        <div>
                                                            {{ __('Pickup') }}: {{ \Carbon\Carbon::parse($batch->scheduled_pickup_date)->format('M d, Y') }}
                                                        </div>
                                                    @endif
                                                    @if($batch->scheduled_delivery_date)
                                                        <div>
                                                            {{ __('Delivery') }}: {{ \Carbon\Carbon::parse($batch->scheduled_delivery_date)->format('M d, Y') }}
                                                        </div>
                                                    @endif
                                                    @if(!$batch->scheduled_pickup_date && !$batch->scheduled_delivery_date)
                                                        -
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full {{ 
                                                    $batch->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                    ($batch->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : 
                                                    ($batch->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                                    'bg-red-100 text-red-800')) 
                                                }}">
                                                    {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $batch->transports_count ?? $batch->transports->count() }} {{ __('vehicles') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('batches.show', $batch) }}" class="text-indigo-600 hover:text-indigo-900" title="{{ __('View') }}">
                                                        <x-heroicon-o-eye class="w-5 h-5" />
                                                    </a>
                                                    <a href="{{ route('batches.edit', $batch) }}" class="text-blue-600 hover:text-blue-900" title="{{ __('Edit') }}">
                                                        <x-heroicon-o-pencil class="w-5 h-5" />
                                                    </a>
                                                    <form action="{{ route('batches.destroy', $batch) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="{{ __('Delete') }}" 
                                                                onclick="return confirm('{{ __('Are you sure you want to delete this batch?') }}')">
                                                            <x-heroicon-o-trash class="w-5 h-5" />
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $batches->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 