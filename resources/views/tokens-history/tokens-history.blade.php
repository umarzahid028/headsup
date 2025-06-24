{{-- resources/views/assigned-customers.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-foreground">
            {{ __('Customers') }}
        </h2>
        <p class="text-sm text-muted-foreground mt-1">
            View the list of customers currently assigned to you or your team.
        </p>
    </x-slot>

    <div class="flex items-center justify-end mb-4 px-6">
        <a href="{{ route('add.customer') }}" class="bg-black text-white px-4 py-2 rounded">
            Add Customer
        </a>
    </div>

    <div class="mt-10 px-6 space-y-6">
        <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
            <table class="min-w-full bg-white divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Customer Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Assigned At</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Activities</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Disposition</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Duration</th>

                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-left">
                    @forelse ($customerSales as $index => $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">{{ $index + 1 }}</td>
                            <td class="px-6 py-3">{{ $sale->name ?? 'Unknown' }}</td>
                            <td class="px-6 py-3">
                                {{ optional($sale->created_at)->format('d M Y h:i A') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-3">
                                @if (!empty($sale->process) && is_array($sale->process))
                                    @foreach ($sale->process as $process)
                                        <span
                                            class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">
                                            {{ $process }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-gray-400 text-xs">No Activities</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $dispositions = json_decode($sale->disposition, true);
                                @endphp

                                @if (is_array($dispositions) && count($dispositions))
                                    @foreach ($dispositions as $item)
                                        <span
                                            class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded mr-1 mb-1">
                                            {{ $item }}
                                        </span>
                                    @endforeach
                                @else
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">
                                        N/A
                                    </span>
                                @endif

                            </td>
                            @php
                                $duration = 'N/A';

                                // Find the latest queue for this customer that has took_turn_at (ignore user_id filter)
                                $queue = \App\Models\Queue::where('customer_id', $sale->customer_id ?? null)
                                    ->whereNotNull('took_turn_at')
                                    ->latest('took_turn_at')
                                    ->first();

                                // If we have both times, calculate duration
                                if (!empty($queue) && $sale->ended_at) {
                                    $start = \Carbon\Carbon::parse($queue->took_turn_at);
                                    $end = \Carbon\Carbon::parse($sale->ended_at);
                                    $duration = $start->diff($end)->format('%Hh %Im %Ss');
                                }
                            @endphp


                            <td class="px-6 py-3">
                                <span
                                    class="inline-block px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">
                                    {{ $duration }}
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No assigned customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        {{-- You can add JS scripts here if needed --}}
    @endpush
</x-app-layout>
