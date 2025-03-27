<div class="p-6">
    <h2 class="text-lg font-medium text-gray-900 mb-4">
        {{ __('Delete We-Owe Item') }}
    </h2>

    <p class="mb-4 text-gray-600">
        {{ __('Are you sure you want to delete this We-Owe item? This action cannot be undone.') }}
    </p>

    <div class="bg-gray-50 p-4 rounded mb-4">
        <h3 class="font-medium text-gray-900">{{ $weOweItem->details }}</h3>
        <p class="text-sm text-gray-600 mt-1">
            @if ($weOweItem->vehicle)
                {{ $weOweItem->vehicle->year }} {{ $weOweItem->vehicle->make }} {{ $weOweItem->vehicle->model }}
            @else
                <span class="italic">No vehicle associated</span>
            @endif
        </p>
        <p class="text-sm text-gray-600 mt-1">
            Status: 
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ 
                $weOweItem->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                ($weOweItem->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                ($weOweItem->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))
            }}">
                {{ ucfirst(str_replace('_', ' ', $weOweItem->status)) }}
            </span>
        </p>
        <p class="text-sm text-gray-600 mt-1">Cost: ${{ number_format($weOweItem->cost, 2) }}</p>
    </div>

    <div class="mt-6 flex justify-end">
        <x-secondary-button wire:click="$dispatch('closeModal')" class="mr-3">
            {{ __('Cancel') }}
        </x-secondary-button>
        
        <x-danger-button wire:click="delete">
            {{ __('Delete Item') }}
        </x-danger-button>
    </div>
</div> 