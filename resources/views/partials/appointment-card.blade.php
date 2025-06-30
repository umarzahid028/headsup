<style>
  .active-card {
  animation: pulseActive 1s infinite;
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
}

@keyframes pulseActive {
  0% {
    box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(99, 102, 241, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
  }
}

.pause-animation {
  animation: none !important;
  box-shadow: none !important;
}

</style>
@if(
    $appointment &&
    $appointment->id &&
    !in_array($appointment->status, ['completed', 'canceled']) &&
    auth()->id() === $appointment->salesperson_id
)
  {{-- SHOW appointment card --}}
  <div id="customer-list" class="transition-opacity duration-300">
    <div
      id="appointment-card"
      class="customer-card max-w-sm mx-auto bg-white shadow-md rounded-2xl p-4 border border-gray-200 mt-6 cursor-pointer transition-all duration-300"
      data-name="{{ $appointment->customer_name }}"
      data-phone="{{ $appointment->customer_phone }}"
    >

      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Appointment Details</h2>
      </div>

      <div class="space-y-2 text-gray-500 text-sm mt-3">
        <p>
          <span class="font-medium text-gray-400">Sales Person:</span>
          <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full ml-2">
             {{ $appointment->salesperson->name ?? 'N/A' }}
          </span>
        </p>
        <p><span class="font-medium text-gray-400">Name:</span> {{ $appointment->customer_name }}</p>
        <p><span class="font-medium text-gray-400">Phone No :</span> {{ $appointment->customer_phone ?? '–' }}</p>
        <p><span class="font-medium text-gray-400">Date & Time:</span> {{ $appointment->date }} {{ $appointment->time }}</p>
      </div>

      <div class="w-full">
        <button
          class="w-full mt-4 bg-gray-800 text-white rounded text-sm transition font-semibold px-6 py-2 rounded">
          Transfer
        </button>
      </div>
    </div>
  </div>
@endif


