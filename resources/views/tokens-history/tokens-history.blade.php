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

    <div class="mt-10 px-6 space-y-6">


        @if ($customerSales->isEmpty())
            <div class="text-gray-500 text-center">No assigned customers found.</div>
        @else
            <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-black">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Customer Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Assigned At</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Activities</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                            <!-- <th class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Duration</th> -->
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-left">
                        @foreach ($customerSales as $index => $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $index + 1 }}</td>
                                <td class="px-6 py-3">{{ $sale->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-3">{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y h:i A') }}</td>
                                <td class="px-6 py-3">
                                    @foreach ($sale->process as $process)
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">{{ $process }}</span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-3">
                                    @if ($sale->served_at)
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded">Served</span>
                                    @else
                                        <span class="inline-block px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded">Processing</span>
                                    @endif
                                </td>
                                <!-- <td class="px-6 py-3">
                                    <span class="live-duration font-mono text-black">00:00:00</span>
                                </td> -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const TIMER_KEY = 'customer_timer_start';
            const LAST_DURATION_KEY = 'last_duration';
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

            function startTimer() {
                localStorage.setItem(TIMER_KEY, Date.now());
                if (intervalId) clearInterval(intervalId);
                intervalId = setInterval(updateTimers, 1000);
                updateTimers();
            }

            function stopTimer() {
                if (intervalId) {
                    clearInterval(intervalId);
                    intervalId = null;
                }

                const start = localStorage.getItem(TIMER_KEY);
                if (start) {
                    const end = Date.now();
                    const diff = Math.floor((end - parseInt(start)) / 1000);
                    const formatted = formatDuration(diff);
                    localStorage.setItem(LAST_DURATION_KEY, formatted);
                    document.querySelectorAll('.live-duration').forEach(el => {
                        el.textContent = formatted + ' (Ended)';
                    });
                }

                localStorage.removeItem(TIMER_KEY);
            }

            // Buttons are in other component
            const startBtn = document.getElementById('newCustomerBtn');
            const stopBtn = document.getElementById('openModalBtn');
            if (startBtn) startBtn.addEventListener('click', startTimer);
            if (stopBtn) stopBtn.addEventListener('click', stopTimer);

            // Resume or display last
            if (localStorage.getItem(TIMER_KEY)) {
                startTimer();
            } else {
                const last = localStorage.getItem(LAST_DURATION_KEY);
                if (last) {
                    document.querySelectorAll('.live-duration').forEach(el => {
                        el.textContent = last + ' (Ended)';
                    });
                }
            }
        });
    </script>
    @endpush
</x-app-layout>