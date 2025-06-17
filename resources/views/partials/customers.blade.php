<style>
  .active-card {
    animation: pulseActive 1s infinite;
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.5);
  }

  @keyframes pulseActive {
    0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
    100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
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
    @endphp

    <div id="card-{{ $customer->id }}" class="customer-card max-w-sm mx-auto bg-white shadow-md rounded-2xl p-4 border border-gray-200 mt-6 cursor-pointer"
      data-name="{{ $customer->name }}"
      data-email="{{ $customer->email }}"
      data-phone="{{ $customer->phone ?? '' }}"
      data-interest="{{ $customer->interest ?? '' }}"
      data-process="{{ is_array($customer->process) ? implode(',', $customer->process) : $customer->process }}"
      data-customer-id="{{ $customer->id }}"
      data-customer-name="{{ $customer->name }}">
      
      <div style="display: flex; justify-content: space-between;">
        <h2 class="text-xl font-semibold text-gray-800">Customer Info</h2>
      </div>

      <span class="text-sm font-semibold mb-4 text-indigo-600 live-duration" style="color:#111827!important;">00:00:00</span>

      <div class="space-y-2 text-gray-500 text-sm">
        <p>
          <span class="font-medium text-gray-400">Sales Person:</span>
          <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full ml-2">
            {{ $customer->user->name ?? 'Unknown' }}
          </span>
        </p>
        <p><span class="font-medium text-gray-400">Name:</span> {{ $customer->name }}</p>
        <p><span class="font-medium text-gray-400">Email:</span> {{ $customer->email }}</p>
        <p><span class="font-medium text-gray-400">Process:</span> {{ $firstProcess }}</p>
      </div>

      <div>
        <button class="transfer-btn mt-4 bg-[#111827] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#0f172a] transition"
          data-customer-id="{{ $customer->id }}"
          data-customer-name="{{ $customer->name }}">
          Transfer
        </button>
      </div>
    </div>
  @endforeach
</div>
