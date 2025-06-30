{{-- resources/views/assigned-customers.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-foreground">
            {{ __('Appointment') }}
        </h2>
        <p class="text-sm text-muted-foreground mt-1">
            View the record of Appointment.
        </p>
    </x-slot>

@php
    if (!function_exists('calculateDuration')) {
        function calculateDuration($sale) {
            $endAt = $sale->ended_at;
            $startAt = null;
            $startSource = null;

            if (!$endAt) {
                return 'N/A';
            }

            // ✅ 1. Highest priority: Appointment arrival_time (if appointment_id exists)
            if ($sale->appointment_id) {
                $appointment = \App\Models\Appointment::find($sale->appointment_id);
                if ($appointment && $appointment->arrival_time) {
                    $startAt = $appointment->arrival_time;
                    $startSource = 'Appointment Arrival Time';
                }
            }

            // ✅ 2. Fallback to Queue
            if (!$startAt) {
                $queue = \App\Models\Queue::where('customer_id', $sale->customer_id)
                    ->where('user_id', $sale->user_id)
                    ->whereNotNull('took_turn_at')
                    ->where('took_turn_at', '<=', $endAt)
                    ->latest('took_turn_at')
                    ->first();

                if ($queue) {
                    $startAt = $queue->took_turn_at;
                    $startSource = 'Queue';
                }
            }

            // ✅ 3. Fallback to created_at
            if (!$startAt) {
                $startAt = $sale->created_at;
                $startSource = 'CreatedAt (Fallback)';
            }

            try {
                $start = \Carbon\Carbon::parse($startAt);
                $end = \Carbon\Carbon::parse($endAt);

                if ($start->gt($end)) {
                    return 'Invalid';
                }

                $seconds = $start->diffInSeconds($end);
                $h = floor($seconds / 3600);
                $m = floor(($seconds % 3600) / 60);
                $s = $seconds % 60;

                return sprintf('%02dh %02dm %02ds', $h, $m, $s);
            } catch (\Exception $e) {
                \Log::warning('Duration calculation failed', [
                    'sale_id' => $sale->id,
                    'error' => $e->getMessage(),
                ]);
                return 'Error';
            }
        }
    }
@endphp



    <div class="py-6">
        <div class="container mx-auto space-y-6 py-6 px-4">
           
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
                            @forelse ($customerSales as $sale)
                                <tr>
                                    <td class="border-b px-4 py-3">{{ $sale->name ?? 'Unknown' }}</td>
                                    <td class="border-b px-4 py-3">
                                        {{ optional($sale->created_at)->format('d M Y h:i A') ?? 'N/A' }}
                                    </td>
                                    <td class="border-b px-4 py-3">
                                        @if (!empty($sale->process) && is_array($sale->process))
                                            @foreach ($sale->process as $process)
                                                <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">
                                                    {{ $process }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-gray-400 text-xs">No Activities</span>
                                        @endif
                                    </td>
                                    <td class="border-b px-4 py-3">
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-800 text-white rounded mr-1 mb-1">
                                            {{ $sale->disposition ?? 'N/A' }}
                                        </span>
                                    </td>
<td class="border-b px-4 py-3">
    {{ calculateDuration($sale) }}
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
</x-app-layout>
