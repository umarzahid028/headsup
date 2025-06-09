<x-app-layout>
  <x-slot name="header">
  </x-slot>
  <meta http-equiv="refresh" content="5">

  <h2 class="text-2xl font-bold text-gray-800 mb-2 max-w-4xl mx-auto px-6">Appointments List</h2>
  <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow mt-16" style="max-height: calc(100vh - 80px); overflow-y: auto;">
    <table class="w-full border border-gray-300 rounded table-auto">
      <thead>
        <tr class="bg-gray-100">
          <th class="border-b px-4 py-2 text-left">#</th> <!-- Serial Number -->
          <th class="border-b px-4 py-2 text-left">Customer</th>
          <th class="border-b px-4 py-2 text-left">With</th>
          <th class="border-b px-4 py-2 text-left">Time</th>
          <th class="border-b px-4 py-2 text-left">Status</th>
          <th class="border-b px-4 py-2 text-left">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($appointments as $index => $appt)
        <tr>
          <td class="border-b px-4 py-3">{{ $index + 1 }}</td> <!-- Serial number -->
          <td class="border-b px-4 py-3">{{ $appt->customer_name }}</td>
          <td class="border-b px-4 py-3">{{ $appt->salesperson->name }}</td>
          <td class="border-b px-4 py-3">{{ $appt->date }} {{ $appt->time }}</td>
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
    
    @if(auth()->user()->id === $appt->salesperson_id)
      <form method="POST" action="/appointments/{{ $appt->id }}/status">
        @csrf
        <div class="flex gap-3 items-center">
          <select name="status" class="border rounded px-3 py-2 text-base w-56">
            <option value="processing" {{ $appt->status == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="completed" {{ $appt->status == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="no_show" {{ $appt->status == 'no_show' ? 'selected' : '' }}>No Show</option>
          </select>

          <button type="submit" style="background-color: #111827;" class="hover:bg-blue-700 text-white font-semibold rounded px-5 py-2 text-base transition duration-150">
            Update
          </button>
        </div>
      </form>
    @endif

    @if(auth()->user()->hasRole('Sales person') && $appt->status != 'completed')
      <a href="{{ route('appointment.form') }}" style="background-color: #111827;" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        View
      </a>
    @endif

  </div>
</td>

        </tr>
        @endforeach
      </tbody>
    </table>

  </div>
</x-app-layout>