<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-foreground">
            {{ __('Assigned Customers') }}
        </h2>
        <p class="text-sm text-muted-foreground mt-1">
            View the list of customers currently assigned to you or your team.
        </p>
    </x-slot>

    <div class="px-6 space-y-6">
        <h3 class="text-2xl font-bold mb-4">All Assigned Customers</h3>

        @if ($customerSales->isEmpty())
            <div class="text-gray-500">No assigned customers found.</div>
        @else
            <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-black">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Customer Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Assigned At</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-left">
                        @foreach ($customerSales as $index => $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $index + 1 }}</td>
                                <td class="px-6 py-3">{{ $sale->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-3">{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y h:i A') }}</td>
                                <td class="px-6 py-3">
                                    @if ($sale->served_at)
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">Served</span>
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if ($sale->served_duration)
                                        <span class="font-mono text-black">{{ $sale->served_duration }}</span>
                                    @else
                                        <span class="live-duration font-mono text-black">00:00:00</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 space-x-2">
                                    @if (!$sale->served_duration)
                                        <button onclick="startCustomerTimer({{ $sale->id }})" class="bg-blue-600 text-white text-xs px-3 py-1 rounded">Start</button>
                                        <button onclick="stopCustomerTimer()" class="bg-red-600 text-white text-xs px-3 py-1 rounded">Stop</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- CSRF token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const TIMER_KEY = 'customer_timer_start';
            const CUSTOMER_ID_KEY = 'customer_id';
            let intervalId = null;

            function formatDuration(seconds) {
                const hrs = String(Math.floor(seconds / 3600)).padStart(2, '0');
                const mins = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
                const secs = String(seconds % 60).padStart(2, '0');
                return `${hrs}:${mins}:${secs}`;
            }

            function updateTimers() {
                const start = localStorage.getItem(TIMER_KEY);
                if (!start) return;

                const startTime = new Date(parseInt(start));
                const now = new Date();
                const diff = Math.floor((now - startTime) / 1000);
                const formatted = formatDuration(diff);

                document.querySelectorAll('.live-duration').forEach(el => {
                    el.textContent = formatted;
                });
            }

            window.startCustomerTimer = function(customerId) {
                localStorage.setItem(TIMER_KEY, Date.now());
                localStorage.setItem(CUSTOMER_ID_KEY, customerId);

                if (intervalId) clearInterval(intervalId);
                intervalId = setInterval(updateTimers, 1000);
                updateTimers();
            }

            window.stopCustomerTimer = async function() {
                const start = localStorage.getItem(TIMER_KEY);
                const customerId = localStorage.getItem(CUSTOMER_ID_KEY);

                if (intervalId) {
                    clearInterval(intervalId);
                    intervalId = null;
                }

                if (start && customerId) {
                    try {
                        const response = await fetch(`/stop-timer/${customerId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ start_time: parseInt(start) })
                        });

                        const data = await response.json();
                        document.querySelectorAll('.live-duration').forEach(el => {
                            el.textContent = data.duration + ' (Saved)';
                        });

                        localStorage.removeItem(TIMER_KEY);
                        localStorage.removeItem(CUSTOMER_ID_KEY);
                    } catch (error) {
                        console.error('Error saving duration:', error);
                    }
                }
            }

            // اگر لوکل اسٹوریج میں پرانا ٹائمر چل رہا ہے
            if (localStorage.getItem(TIMER_KEY)) {
                updateTimers();
                intervalId = setInterval(updateTimers, 1000);
            }
        });
    </script>
    @endpush
</x-app-layout>
