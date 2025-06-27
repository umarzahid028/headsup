<style>
@keyframes pulseActive {
  0% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.6); }
  70% { box-shadow: 0 0 0 12px rgba(37, 99, 235, 0); }
  100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0); }
}

.active-card {
  animation: pulseActive 1s infinite;
  border: 2px solid #2563eb;
  border-radius: 1rem;
}

  .fade-out {
    animation: fadeOut 0.5s forwards;
  }

  @keyframes fadeOut {
    to {
      opacity: 0;
      transform: scale(0.95);
    }
  }
</style>

<div id="customer-list">
  @foreach ($customers as $customer)
    @php
      $firstProcess = 'N/A';
      if (is_array($customer->process) && isset($customer->process[0])) {
        $firstProcess = $customer->process[0];
      } elseif (is_string($customer->process)) {
        $firstProcess = $customer->process;
      }

      $dispositions = is_array($customer->disposition)
        ? implode(', ', $customer->disposition)
        : ($customer->disposition ?? null);
    @endphp

    @if ($customer->forwarded_to_manager && empty($customer->disposition))
      <div
        class="customer-card w-full mx-auto bg-white shadow-md rounded-2xl mb-4 border border-gray-200 p-4 cursor-pointer transition-all duration-300"
        id="card-{{ $customer->id }}"
        data-customer-id="{{ $customer->id }}"
        data-name="{{ $customer->name }}"
        data-email="{{ $customer->email }}"
        data-phone="{{ $customer->phone ?? '' }}"
        data-interest="{{ $customer->interest ?? '' }}"
        data-process="{{ is_array($customer->process) ? implode(',', $customer->process) : $customer->process }}"
        data-disposition="{{ $dispositions }}"
        data-to="{{ $customer->is_to ? 'true' : 'false' }}"
      >
        <div class="flex justify-between items-center">
          <h2 class="text-xl font-semibold text-gray-800">Customer Info</h2>
        </div>

        <div class="space-y-2 text-gray-500 text-sm mt-3">
          <p>
            <span class="font-medium text-gray-400">Sales Person:</span>
            <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-semibold px-3 py-1 rounded ml-2">
              {{ $customer->user->name ?? 'Unknown' }}
            </span>
          </p>
          <p><span class="font-medium text-gray-400">Name:</span> {{ $customer->name }}</p>
          <p><span class="font-medium text-gray-400">Email:</span> {{ $customer->email }}</p>
          <p><span class="font-medium text-gray-400">Process:</span> {{ $firstProcess }}</p>
          <p><span class="font-medium text-gray-400">Disposition:</span> {{ $dispositions ?? 'N/A' }}</p>
        </div>
      </div>
    @endif
  @endforeach
</div>

