<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Check-In Activity Report
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            Overview and detailed breakdown of employee check-in and check-out activities.
        </p>
    </x-slot>

    <div class="px-6 py-2 space-y-10">




        <!-- ✅ Summary Section -->
        <section>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Activity Records</h3>

            @if ($from && $to)
            <p class="text-sm text-gray-600 mb-6">
                Showing records from <strong>{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</strong>
                to <strong>{{ \Carbon\Carbon::parse($to)->format('d M Y') }}</strong>
            </p>
            @else
            <p class="text-sm text-gray-600 mb-6">
                Showing all records
            </p>
            @endif
        </section>




        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-md">
                <p class="text-sm text-gray-500 mb-1">Total Check-ins</p>
                <h4 class="text-2xl font-semibold text-gray-800">{{ $checkInCount }}</h4>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-md">
                <p class="text-sm text-gray-500 mb-1">Total Check-outs</p>
                <h4 class="text-2xl font-semibold text-emerald-600">{{ $checkOutCount }}</h4>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-md">
                <p class="text-sm text-gray-500 mb-1">Total Duration</p>
                <h4 class="text-2xl font-semibold text-rose-600">
                    {{ floor($totalDurationMinutes / 60) }} hrs {{ $totalDurationMinutes % 60 }} mins
                </h4>
            </div>
        </div>
        </section>

        <!-- ✅ Date Filter Form -->
  <div class="mt-6" style="display: flex; justify-content:end;">
    <form method="GET" class="mb-6">
        <div class="flex items-center space-x-4">
            <div>
                <label for="from" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="from" id="from"
                    class="mt-1 block w-34 border-gray-300 rounded-md shadow-sm"
                    value="{{ old('from', $from) }}">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700">To Date</label>
                <input type="date" name="to" id="to"
                    class="mt-1 block w-34 border-gray-300 rounded-md shadow-sm"
                    value="{{ old('to', $to) }}">
            </div>
            <div class="pt-6">
                <button type="submit"
                    class="bg-gray-800 text-white px-4 py-2 rounded">
                    Filter
                </button>
            </div>
        </div>
    </form>
</div>


        <!-- ✅ Detailed Report Section -->
        <section>
            <div class="overflow-x-auto rounded-lg shadow border border-gray-200 mt-5">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border-b px-4 py-2 text-left">Check-In Time</th>
                            <th class="border-b px-4 py-2 text-left">Check-Out Time</th>
                            <th class="border-b px-4 py-2 text-left">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report as $entry)
                        @php
                        $checkIn = isset($entry['checked_in_at']) ? \Carbon\Carbon::parse($entry['checked_in_at']) : null;
                        $checkOut = isset($entry['checked_out_at']) ? \Carbon\Carbon::parse($entry['checked_out_at']) : null;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-all">
                            <td class="border-b px-4 py-3">
                                {{ $checkIn ? $checkIn->format('d M Y, h:i A') : '-' }}
                            </td>
                            <td class="border-b px-4 py-3">
                                {{ $checkOut ? $checkOut->format('d M Y, h:i A') : '-' }}
                            </td>
                            <td class="border-b px-4 py-3">
                                {{ $entry['duration'] ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-6 text-center text-gray-500">
                                No check-in data available for selected range.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
    <!-- SweetAlert Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @if ($infoMessage)
    <script>
        Swal.fire({
            icon: 'info',
            title: 'No Records Found',
            text: '{{ $infoMessage }}',
            confirmButtonColor: '#3085d6',
        }).then(() => {
            document.getElementById('from').value = '';
            document.getElementById('to').value = '';
        });
    </script>
    @endif




</x-app-layout>