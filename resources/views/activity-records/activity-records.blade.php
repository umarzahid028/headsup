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
    <h3 class="text-2xl font-bold text-gray-800 mb-6">Summary</h3>
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
    <h3 class="text-2xl font-bold text-gray-800 mb-6 mt-5">Detailed Report</h3>
    <div class="overflow-x-auto bg-white border border-gray-200 rounded-2xl shadow-md">
      <table class="min-w-full text-sm text-gray-700">
        <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-600">
          <tr>
            <th class="px-6 py-4 text-left border-b">Check-In Time</th>
            <th class="px-6 py-4 text-left border-b">Check-Out Time</th>
            <th class="px-6 py-4 text-left border-b">Duration</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($report as $entry)
          <tr class="hover:bg-gray-50 transition-all">
            <td class="px-6 py-4 border-b">{{ $entry['checked_in_at'] ?? '-' }}</td>
            <td class="px-6 py-4 border-b">{{ $entry['checked_out_at'] ?? '-' }}</td>
            <td class="px-6 py-4 border-b">{{ $entry['duration'] ?? '-' }}</td>
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
