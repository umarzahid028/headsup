<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Check-In Activity Report
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            Overview and detailed breakdown of employee check-in and check-out activities.
        </p>
    </x-slot>

    <div class="px-6 py-6 space-y-10">

        <!-- Summary Section -->
        <section>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Activity Records</h3>
            <p class="text-sm text-gray-600 mb-6">
                Showing records for <strong>{{ \Carbon\Carbon::now()->format('F Y') }}</strong>
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-md">
                    <p class="text-sm text-gray-500 mb-1">Total Check-ins</p>
                    <h4 class="text-2xl font-semibold" style="color: #111827;">{{ $checkInCount }}</h4>
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

        <!-- Detailed Report Section -->
        <section>
              <div>
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
                            <tr class="hover:bg-gray-50 transition-all">
                                <td class="border-b px-4 py-3">{{ $entry['checked_in_at'] ?? '-' }}</td>
                                <td class="border-b px-4 py-3">{{ $entry['checked_out_at'] ?? '-' }}</td>
                                <td class="border-b px-4 py-3">{{ $entry['duration'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-6 text-center text-gray-500">
                                    No check-in data available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</x-app-layout>
