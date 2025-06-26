@if($appointment && $appointment->status !== 'completed')
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
          class="transfer-btn w-full mt-4 bg-[#111827] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#0f172a] transition">
          Transfer
        </button>

      </div>
    </div>
  </div>
@endif
