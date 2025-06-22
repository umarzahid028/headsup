<x-app-layout>
    <x-slot name="header">
       <h2 class="text-2xl font-bold text-gray-800 mb-1">
    Appointments
</h2>
<p class="text-gray-500 text-sm">
    Manage and view all your appointments here.
</p>

    </x-slot>

    <div class="px-6 space-y-6">
       
        
            <div class="flex items-center justify-end mb-4 px-6">

            <a href="{{ route('appointment.create') }}" class="text-white px-4 py-2 rounded" style="background-color: #111827;">
                Add Appoinments
            </a>
        </div>
      
            <!-- <div class="text-gray-500">No assigned customers found.</div> -->
      
        
            <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
           
                <table class="min-w-full bg-white divide-y divide-gray-200">
      <thead>
        <tr class="bg-gray-100">
          <th class="border-b px-4 py-2 text-left">#</th> <!-- Serial Number -->
          <th class="border-b px-4 py-2 text-left">Customer</th>
          <th class="border-b px-4 py-2 text-left">Sale Person</th>
          <th class="border-b px-4 py-2 text-left">Schedule At</th>
          <th class="border-b px-4 py-2 text-left">Status</th>
          <th class="border-b px-4 py-2 text-left">Action</th>
        </tr>
      </thead>
    <tbody>
  @forelse($appointments as $index => $appt)
    <tr>
      <td class="border-b px-4 py-3">{{ $loop->iteration }}</td>
      <td class="border-b px-4 py-3">{{ $appt->customer_name }}</td>
      <td class="border-b px-4 py-3">
  <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
    {{ $appt->salesperson->name }}
  </span>
</td>

      <td class="border-b px-4 py-3">
    {{ \Carbon\Carbon::parse($appt->date . ' ' . $appt->time)->format('d F Y, h:i A') }}
</td>

      <td class="border-b px-4 py-3">
        @php
          $statusClasses = [
            'processing' => 'bg-yellow-200 text-yellow-800',
            'completed' => 'bg-green-200 text-green-800',
            'no_show' => 'bg-red-200 text-red-800',
          ];
          $statusClass = $statusClasses[$appt->status] ?? 'bg-gray-200 text-gray-800';
        @endphp
        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $statusClass }}">
          {{ ucfirst($appt->status) }}
        </span>
      </td>
      <td class="border-b px-4 py-3">
        <div class="flex flex-wrap gap-3 items-center">
<a href="{{ route('sales.perosn', ['id' => $appt->id]) }}"
   class="text-white font-bold py-2 px-4 rounded"
   style="background-color: #111827;">
   Customer Arrive
</a>


             <a href="{{ route('appointments.edit', ['appointment' => $appt->id]) }}" class="text-yellow-600 border border-yellow-600 font-bold py-2   px-4 rounded hover:bg-yellow-600 hover:text-white transition" style="background-color:#111827; Color:white;">
                Edit
            </a>
        </div>
      </td>
    </tr>
  @empty
    <tr>
      <td colspan="6" class="text-center py-6 text-gray-500 text-base italic">
        No appointments found.
      </td>
    </tr>
  @endforelse
</tbody>

    </table>

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

            if (localStorage.getItem(TIMER_KEY)) {
                updateTimers();
                intervalId = setInterval(updateTimers, 1000);
            }
        });
    </script>
    @endpush
</x-app-layout>
