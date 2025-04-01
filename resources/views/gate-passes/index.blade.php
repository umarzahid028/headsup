<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Gate Pass Management') }}
            </h2>
            <x-button href="{{ route('gate-passes.create') }}">
                <x-heroicon-o-plus class="w-5 h-5 mr-1" />
                {{ __('New Gate Pass') }}
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <form method="GET" action="{{ route('gate-passes.index') }}" class="flex items-center gap-4">
                            <div class="flex-grow">
                                <x-input-label for="search" class="sr-only">{{ __('Search') }}</x-input-label>
                                <x-input id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by pass number, vehicle, transporter or batch...') }}" class="w-full" />
                            </div>
                            <div class="w-48">
                                <x-input-label for="status" class="sr-only">{{ __('Status') }}</x-input-label>
                                <x-select id="status" name="status" class="w-full">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                    <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>{{ __('Used') }}</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
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
                                    <x-button href="{{ route('gate-passes.index') }}" variant="outline">
                                        <x-heroicon-o-x-mark class="w-5 h-5 mr-1" />
                                        {{ __('Clear') }}
                                    </x-button>
                                </div>
                            @endif
                        </form>
                    </div>

                    @if($gatePasses->isEmpty())
                        <div class="p-12 text-center">
                            <x-heroicon-o-document-check class="w-16 h-16 mx-auto text-gray-400" />
                            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('No Gate Passes Found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ request('search') || request('status') ? 
                                    __('No gate passes match your search criteria.') : 
                                    __('Get started by creating a new gate pass for vehicle pickup authorization.') }}
                            </p>
                            <div class="mt-6">
                                <x-button href="{{ route('gate-passes.create') }}">
                                    <x-heroicon-o-plus class="w-5 h-5 mr-1" />
                                    {{ __('New Gate Pass') }}
                                </x-button>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Pass Number') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Vehicle') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Transporter') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Batch') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Validity') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ __('Status') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                            {{ __('File') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($gatePasses as $gatePass)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $gatePass->pass_number }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    {{ __('Created') }}: {{ $gatePass->created_at->format('M d, Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $gatePass->vehicle->stock_number ?? '-' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $gatePass->vehicle->year ?? '' }} {{ $gatePass->vehicle->make ?? '' }} {{ $gatePass->vehicle->model ?? '' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $gatePass->transporter->name ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $gatePass->batch->batch_number ?? '-' }}
                                                </div>
                                                @if($gatePass->batch)
                                                    <div class="text-xs text-gray-500">
                                                        {{ $gatePass->batch->name ?? '' }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <div>{{ __('Issue') }}: {{ \Carbon\Carbon::parse($gatePass->issue_date)->format('M d, Y') }}</div>
                                                    <div>{{ __('Expiry') }}: {{ \Carbon\Carbon::parse($gatePass->expiry_date)->format('M d, Y') }}</div>
                                                </div>
                                                @if($gatePass->used_at)
                                                    <div class="text-xs text-gray-500">
                                                        {{ __('Used on') }}: {{ \Carbon\Carbon::parse($gatePass->used_at)->format('M d, Y') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full {{ 
                                                    $gatePass->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                    ($gatePass->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                                    ($gatePass->status === 'used' ? 'bg-green-100 text-green-800' : 
                                                    ($gatePass->status === 'rejected' ? 'bg-red-100 text-red-800' :
                                                    'bg-gray-100 text-gray-800'))) 
                                                }}">
                                                    {{ ucfirst($gatePass->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                                                @if($gatePass->file_path)
                                                    <a href="{{ route('gate-passes.download', $gatePass) }}" class="text-blue-600 hover:text-blue-900" title="{{ __('Download') }}">
                                                        <x-heroicon-o-document-arrow-down class="w-5 h-5 mx-auto" />
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">
                                                        <x-heroicon-o-document-minus class="w-5 h-5 mx-auto" />
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('gate-passes.show', $gatePass) }}" class="text-indigo-600 hover:text-indigo-900" title="{{ __('View') }}">
                                                        <x-heroicon-o-eye class="w-5 h-5" />
                                                    </a>
                                                    @if(!in_array($gatePass->status, ['used', 'expired']))
                                                        <a href="{{ route('gate-passes.edit', $gatePass) }}" class="text-blue-600 hover:text-blue-900" title="{{ __('Edit') }}">
                                                            <x-heroicon-o-pencil class="w-5 h-5" />
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('gate-passes.destroy', $gatePass) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="{{ __('Delete') }}" 
                                                                onclick="return confirm('{{ __('Are you sure you want to delete this gate pass?') }}')">
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
                            {{ $gatePasses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 