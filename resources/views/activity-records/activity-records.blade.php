<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Check-In Activity Report
        </h2>
    </x-slot>

    <div class="px-6 py-6 bg-white shadow-md rounded-lg mt-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Summary</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-gray-600">
                <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                    <strong>Total Check-ins:</strong>
                    <p>{{ $checkInCount }}</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                    <strong>Total Check-outs:</strong>
                    <p>{{ $checkOutCount }}</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                    <strong>Total Duration:</strong>
                    <p>{{ floor($totalDurationMinutes / 60) }} hrs {{ $totalDurationMinutes % 60 }} mins</p>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Detailed Report</h3>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full text-sm text-left text-gray-700 border border-gray-200">
                    <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 border border-gray-200">Check In</th>
                            <th class="px-6 py-3 border border-gray-200">Check Out</th>
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
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No check-in data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
