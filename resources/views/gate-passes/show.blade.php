<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Gate Pass Details') }}: {{ $gatePass->pass_number }}
            </h2>
            <div class="flex space-x-2">
                @if(!in_array($gatePass->status, ['used', 'expired']))
                    <x-button href="{{ route('gate-passes.edit', $gatePass) }}">
                        <x-heroicon-o-pencil class="w-5 h-5 mr-1" />
                        {{ __('Edit Gate Pass') }}
                    </x-button>
                @endif
                <x-button href="{{ route('gate-passes.index') }}" variant="outline">
                    <x-heroicon-o-arrow-left class="w-5 h-5 mr-1" />
                    {{ __('Back to Gate Passes') }}
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Gate Pass Information') }}</h3>
                            <div class="mt-4 space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Pass Number') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $gatePass->pass_number }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Status') }}:</span>
                                    <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full {{ 
                                        $gatePass->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($gatePass->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                        ($gatePass->status === 'used' ? 'bg-green-100 text-green-800' : 
                                        ($gatePass->status === 'rejected' ? 'bg-red-100 text-red-800' :
                                        'bg-gray-100 text-gray-800'))) 
                                    }}">
                                        {{ ucfirst($gatePass->status) }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Vehicle') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $gatePass->vehicle->stock_number ?? '-' }}
                                        ({{ $gatePass->vehicle->year ?? '' }} {{ $gatePass->vehicle->make ?? '' }} {{ $gatePass->vehicle->model ?? '' }})
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Transporter') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $gatePass->transporter->name ?? '-' }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Batch') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        @if($gatePass->batch)
                                            <a href="{{ route('batches.show', $gatePass->batch) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $gatePass->batch->batch_number }}
                                            </a>
                                            {{ $gatePass->batch->name ? "- {$gatePass->batch->name}" : '' }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Created On') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">{{ $gatePass->created_at->format('M d, Y H:i A') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Validity Information') }}</h3>
                            <div class="mt-4 space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Issue Date') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($gatePass->issue_date)->format('M d, Y') }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Expiry Date') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($gatePass->expiry_date)->format('M d, Y') }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Used At') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $gatePass->used_at ? \Carbon\Carbon::parse($gatePass->used_at)->format('M d, Y H:i A') : '-' }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Authorized By') }}:</span>
                                    <span class="ml-2 text-sm text-gray-900">
                                        {{ $gatePass->authorized_by ?? '-' }}
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-sm font-medium text-gray-500">{{ __('Valid For Use') }}:</span>
                                    <span class="ml-2 text-sm {{ $gatePass->isValid() ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                        {{ $gatePass->isValid() ? __('Yes') : __('No') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gate Pass Document -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Gate Pass Document') }}</h3>
                        <div class="mt-4">
                            @if($gatePass->file_path)
                                <div class="p-4 mb-4 border border-gray-200 rounded-md bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <x-heroicon-o-document-text class="w-8 h-8 text-indigo-500" />
                                            <span class="ml-2 text-sm font-medium text-gray-900">{{ basename($gatePass->file_path) }}</span>
                                        </div>
                                        <a href="{{ route('gate-passes.download', $gatePass) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                            <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2 -ml-1" />
                                            {{ __('Download') }}
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="p-4 text-sm italic text-center text-gray-500 border border-gray-200 rounded-md">
                                    {{ __('No document attached to this gate pass.') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Notes') }}</h3>
                        <div class="p-4 mt-4 rounded-md bg-gray-50">
                            <p class="text-sm text-gray-700">
                                {{ $gatePass->notes ?? __('No notes available.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Update Status Form -->
                    @if(!in_array($gatePass->status, ['used', 'expired']))
                        <div class="p-4 mt-6 border border-gray-200 rounded-md bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Update Gate Pass Status') }}</h3>
                            <form method="POST" action="{{ route('gate-passes.update-status', $gatePass) }}" class="mt-4">
                                @csrf
                                @method('PATCH')
                                
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <x-input-label for="status" :value="__('Status')" />
                                        <x-select id="status" name="status" class="block w-full mt-1" required>
                                            <option value="pending" {{ $gatePass->status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                            <option value="approved" {{ $gatePass->status == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                            <option value="used" {{ $gatePass->status == 'used' ? 'selected' : '' }}>{{ __('Used') }}</option>
                                            <option value="rejected" {{ $gatePass->status == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                            <option value="expired" {{ $gatePass->status == 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                                        </x-select>
                                    </div>
                                    
                                    <div class="flex items-end">
                                        <x-button type="submit" class="w-full">
                                            {{ __('Update Status') }}
                                        </x-button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 