{{-- resources/views/assigned-customers.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-foreground">
            {{ __('Customers') }}
        </h2>
        <p class="text-sm text-muted-foreground mt-1">
            View the list of customers.
        </p>
    </x-slot>

    {{-- Functions declared only ONCE --}}
    @php
        if (!function_exists('getCustomerStartTime')) {
            function getCustomerStartTime($sale) {
                $queue = \App\Models\Queue::where('customer_id', $sale->customer_id ?? null)
                    ->whereNotNull('took_turn_at')
                    ->latest('took_turn_at')
                    ->first();

                if ($queue && $queue->took_turn_at) {
                    return ['start_at' => $queue->took_turn_at, 'source' => 'Queue'];
                }

                if (!empty($sale->appointment_id)) {
                    $appointment = \App\Models\Appointment::find($sale->appointment_id);
                    if ($appointment && $appointment->arrival_time) {
                        return ['start_at' => $appointment->arrival_time, 'source' => 'Appointment'];
                    }
                }

                return ['start_at' => null, 'source' => 'None'];
            }
        }

        if (!function_exists('calculateDuration')) {
            function calculateDuration($startAt, $endAt) {
                if ($startAt && $endAt) {
                    $start = \Carbon\Carbon::parse($startAt);
                    $end = \Carbon\Carbon::parse($endAt);

                    if ($start->lte($end)) {
                        $seconds = $start->diffInSeconds($end);
                        $h = floor($seconds / 3600);
                        $m = floor(($seconds % 3600) / 60);
                        $s = $seconds % 60;

                        return sprintf('%02dh %02dm %02ds', $h, $m, $s);
                    } else {
                        return 'Invalid';
                    }
                }

                return 'N/A';
            }
        }
    @endphp

    <div class="py-6">
        <div class="container mx-auto space-y-6 py-6 px-4">
            <div class="flex items-center justify-end mb-4 px-6">
                <a href="{{ route('add.customer') }}" class="bg-black text-white px-4 py-2 rounded">
                    Add Customer
                </a>
            </div>
            <div>
                <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border-b px-4 py-2 text-left">Customer Name</th>
                                <th class="border-b px-4 py-2 text-left">Assigned At</th>
                                <th class="border-b px-4 py-2 text-left">Activities</th>
                                <th class="border-b px-4 py-2 text-left">Disposition</th>
                                <th class="border-b px-4 py-2 text-left">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customerSales as $index => $sale)
                                @php
                                    $endAt = $sale->ended_at ?? null;
                                    $startInfo = getCustomerStartTime($sale);
                                    $duration = calculateDuration($startInfo['start_at'], $endAt);
                                @endphp

                                <tr>
                                    <td class="border-b px-4 py-3">{{ $sale->name ?? 'Unknown' }}</td>
                                    <td class="border-b px-4 py-3">
                                        {{ optional($sale->created_at)->format('d M Y h:i A') ?? 'N/A' }}
                                    </td>
                                    <td class="border-b px-4 py-3">
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
                                    <td class="border-b px-4 py-3">
                                        <span
                                            class="inline-block px-2 py-1 text-xs font-semibold bg-gray-800 text-white rounded mr-1 mb-1">
                                            {{ $sale->disposition ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="border-b px-4 py-3">
                                        {{ $duration }}
                                        {{-- <small class="text-gray-400">({{ $startInfo['source'] }})</small> --}}
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

                    @if ($customerSales->hasPages())
                        <div class="mt-4 px-4">
                            {{ $customerSales->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- JS scripts if needed --}}
    @endpush
</x-app-layout>
