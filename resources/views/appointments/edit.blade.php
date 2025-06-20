<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl font-bold text-gray-800 mb-1 px-4">
      Update Appointment
    </h2>
    <p class="text-gray-500 text-sm px-4">
      Update existing appointment details using the form below.
    </p>
  </x-slot>

  <div class="">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg p-6">

        <!-- Main Appointment Update Form -->
        <form method="POST" action="{{ route('appointments.update', $appointment->id) }}">
          @csrf
          @method('PUT')

          <!-- Customer Information -->
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Customer Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
              <label class="block text-sm font-medium text-gray-700">Customer Name</label>
              <input name="customer_name" value="{{ old('customer_name', $appointment->customer_name) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Phone Number</label>
              <input name="customer_phone" value="{{ old('customer_phone', $appointment->customer_phone) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>
          </div>

          <!-- Appointment Schedule -->
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Appointment Schedule</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
              <label class="block text-sm font-medium text-gray-700">Date</label>
              <input type="date" name="date" value="{{ old('date', $appointment->date) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Time</label>
              <input type="time" name="time" value="{{ old('time', $appointment->time) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>
          </div>

          <!-- Salesperson -->
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Salesperson</h3>
          <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700">Select Salesperson</label>
            <select name="salesperson_id"
              class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              @foreach($salespersons as $person)
                <option value="{{ $person->id }}" {{ $appointment->salesperson_id == $person->id ? 'selected' : '' }}>
                  {{ $person->name }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Notes -->
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Additional Notes</h3>
          <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea name="notes"
              class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes', $appointment->notes) }}</textarea>
          </div>

          <!-- Submit button -->
          <div class="flex justify-end">
            <button type="submit" style="background-color: #111827;"
              class="py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Update Appointment
            </button>
          </div>
        </form>

        <!-- Appointment Status Update Section -->
        <div class="mt-10 border-t pt-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Appointment Status Update</h3>

          @forelse($appointments as $index => $appt)
            <form method="POST" action="/appointments/{{ $appt->id }}/status" class="mb-4">
              @csrf
              <div class="flex flex-col md:flex-row md:items-center gap-3">
                <select name="status" class="border rounded px-3 py-2 text-base w-full md:w-56">
                  <option value="processing" {{ $appt->status == 'processing' ? 'selected' : '' }}>Processing</option>
                  <option value="completed" {{ $appt->status == 'completed' ? 'selected' : '' }}>Completed</option>
                  <option value="no_show" {{ $appt->status == 'no_show' ? 'selected' : '' }}>No Show</option>
                  <option value="Cancel" {{ $appt->status == 'Cancel' ? 'selected' : '' }}>Cancel</option>
                </select>

                <button type="submit"
                  class="bg-gray-900 text-white font-semibold rounded px-5 py-2 text-base transition duration-150">
                  Update
                </button>
              </div>
            </form>
          @empty
            <p class="text-gray-500 text-sm">No appointments found.</p>
          @endforelse
        </div>

      </div>
    </div>
  </div>
</x-app-layout>
