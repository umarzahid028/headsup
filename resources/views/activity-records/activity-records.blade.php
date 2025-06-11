<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="px-6">
          <h2 class="text-xl font-semibold mb-2">Check-In Activity Report</h2>
        <div class="mb-4">
            <strong>Total Check-ins:</strong> {{ $checkInCount }}<br>
            <strong>Total Check-outs:</strong> {{ $checkOutCount }}<br>
            <strong>Total Duration:</strong> {{ floor($totalDurationMinutes / 60) }} hrs {{ $totalDurationMinutes % 60 }} mins
        </div>

        <table class="w-full text-sm text-left border">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Check In</th>
                    <th class="border px-4 py-2">Check Out</th>
                    <th class="border px-4 py-2">Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report as $entry)
                    <tr>
                        <td class="border px-4 py-2">{{ $entry['checked_in_at'] ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $entry['checked_out_at'] ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $entry['duration'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
