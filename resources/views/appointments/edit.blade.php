<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl font-bold text-gray-800 mb-1 px-4">
      Update Appointment
    </h2>
    <p class="text-gray-500 text-sm px-4">
      Update appointment details and status using the form below.
    </p>
  </x-slot>

  <div class="">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg p-6">

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

  <select name="salesperson_id" required
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

          <!-- Status -->
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Appointment Status</h3>
          <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700">Select Status</label>
            <select name="status"
              class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              <option value="scheduled" {{ $appointment->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
              <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Completed</option>
              <option value="cancel" {{ $appointment->status == 'cancel' ? 'selected' : '' }}>Canceled</option>
            </select>
          </div>

          <!-- Submit -->
          <div class="flex justify-end">
            <button type="submit"
              class="py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Update Appointment
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</x-app-layout>
