<style>
.active-card {
  animation: pulseActive 1s infinite;
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
}

/* Pause class */
.active-card.paused {
  animation-play-state: paused !important;
}

.customer-card {
  margin-top: 0 !important;
  margin-bottom: 20px !important;
}

</style>


<div id="customer-list">
  @foreach ($customers as $customer)
@php
  $latestProcess = 'N/A';
  $processArray = is_array($customer->process) ? $customer->process : [];

  if (!empty($processArray)) {
    $latestProcess = $processArray[array_key_last($processArray)];
  } elseif (is_string($customer->process)) {
    $latestProcess = $customer->process;
  }

  $dispositions = is_array($customer->disposition)
    ? implode(', ', $customer->disposition)
    : ($customer->disposition ?? null);
@endphp



   @if (is_null($customer->disposition) 
    && (is_null($customer->transferred_to_user_id) || $customer->transferred_to_user_id == auth()->id()) )

      <div
        id="card-{{ $customer->id }}"
        class="customer-card  max-w-sm mx-auto bg-white shadow-md rounded-2xl p-4 border border-gray-200 mb-6 mt-0 cursor-pointer transition-all duration-300"
        data-name="{{ $customer->name }}"
        data-email="{{ $customer->email }}"
        data-phone="{{ $customer->phone ?? '' }}"
        data-interest="{{ $customer->interest ?? '' }}"
        data-process="{{ is_array($customer->process) ? implode(',', $customer->process) : $customer->process }}"
        data-disposition="{{ $dispositions }}"
        data-customer-id="{{ $customer->id }}"
        data-customer-name="{{ $customer->name }}"
        data-notes="{{ $customer->notes ?? '' }}"
      >

        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Customer Info</h2>
        </div>

        <div class="space-y-2 text-gray-500 text-sm mt-3">
          <p>
            <span class="font-medium text-gray-400">Sales Person:</span>
            <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full ml-2">
              {{ $customer->user->name ?? 'Unknown' }}
            </span>
          </p>
          <p><span class="font-medium text-gray-400">Name:</span> {{ $customer->name }}</p>
          <p><span class="font-medium text-gray-400">Email:</span> {{ $customer->email }}</p>
          <p><span class="font-medium text-gray-400">Phone:</span> {{ $customer->phone }}</p>
          <p><span class="font-medium text-gray-400">Process:</span> {{ $latestProcess }}</p>
          <p><span class="font-medium text-gray-400">Disposition:</span> {{ $dispositions ?? 'N/A' }}</p>
        </div>

        <div class="w-full">
          <button
  class="transfer-btn w-full mt-4 bg-gray-800 text-white rounded text-sm  transition  font-semibold px-6 py-2 rounded "
  data-customer-id="{{ $customer->id }}"
  data-customer-name="{{ $customer->name }}"
>
  Transfer
</button>
    
        </div>
      </div>
    @endif
  @endforeach
</div>






