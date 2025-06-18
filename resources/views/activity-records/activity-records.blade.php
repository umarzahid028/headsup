<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Check-In Activity Report
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            Overview and detailed breakdown of employee check-in and check-out activities.
        </p>
    </x-slot>

    <div class="px-6 py-4 bg-white shadow-md rounded-lg ">
        <!-- Summary Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Summary</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-gray-700">
                <div class="bg-gray-50 p-5 rounded-lg shadow-sm">
                    <span class="block text-sm font-medium text-gray-500">Total Check-ins</span>
                    <span class="text-lg font-semibold">{{ $checkInCount }}</span>
                </div>
                <div class="bg-gray-50 p-5 rounded-lg shadow-sm">
                    <span class="block text-sm font-medium text-gray-500">Total Check-outs</span>
                    <span class="text-lg font-semibold">{{ $checkOutCount }}</span>
                </div>
                <div class="bg-gray-50 p-5 rounded-lg shadow-sm">
                    <span class="block text-sm font-medium text-gray-500">Total Duration</span>
                    <span class="text-lg font-semibold">
                        {{ floor($totalDurationMinutes / 60) }} hrs {{ $totalDurationMinutes % 60 }} mins
                    </span>
                </div>
            </div>
        </div>

        <!-- Detailed Report Section -->
        <div>
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Detailed Report</h3>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200">
                    <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 border border-gray-200">Check-In Time</th>
                            <th class="px-6 py-3 border border-gray-200">Check-Out Time</th>
                            <th class="px-6 py-3 border border-gray-200">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($report as $entry)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 border border-gray-200">{{ $entry['checked_in_at'] ?? '-' }}</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $entry['checked_out_at'] ?? '-' }}</td>
                                <td class="px-6 py-4 border border-gray-200">{{ $entry['duration'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                    No check-in data available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
