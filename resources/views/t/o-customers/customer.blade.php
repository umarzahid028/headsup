<x-app-layout>
     <x-slot name="header">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <h3 class="text-2xl font-bold text-gray-800 leading-tight mb-0 px-2">Customer Sales Form</h3>
    <p class="text-gray-500 mt-0 leading-tight px-2">Fill out the details below to log a customer sales interaction.</p>
     </x-slot>
  <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

    <!-- Customer Sales Form -->
    <div class="md:col-span-8 mx-4">
      <form id="salesForm" method="POST" action="{{ route('customer.sales.store') }}" class="bg-white rounded-2xl border border-gray-200 p-8 shadow-lg">
        @csrf
        <div>
        </div>

        <input type="hidden" name="id" id="customerId" value="">
        <input type="hidden" name="user_id" value="{{ auth()->id() }}" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
          <!-- Customer Info -->
          <div class="space-y-4">
            @foreach (['name', 'email', 'phone', 'interest'] as $field)
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">
                {{ ucfirst($field) }}
                @if(in_array($field, ['name', 'phone']))
                  <span class="text-red-600">*</span>
                @endif
              </label>
              <input name="{{ $field }}" type="{{ $field == 'email' ? 'email' : 'text' }}"
                class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full"
                value="{{ $sale->$field ?? '' }}"
                @if(in_array($field, ['name', 'email', 'phone'])) required @endif />
            </div>
            @endforeach
          </div>

          <!-- Sales Details -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
              <textarea name="notes" rows="6" class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full">{{ $sale->notes ?? '' }}</textarea>
            </div>

            <fieldset class="border border-gray-300 rounded-xl p-4">
              <legend class="text-sm font-semibold text-gray-700 mb-3">Sales Process</legend>
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach(['Investigating','Test Driving','Desking','Credit Application','Penciling','F&I'] as $process)
                <label class="flex items-center space-x-2">
                  <input type="checkbox" name="process[]" value="{{ $process }}"
                    {{ isset($sale) && is_array($sale->process) && in_array($process, $sale->process) ? 'checked' : '' }}
                    class="form-checkbox h-5 w-5 text-indigo-600">
                  <span class="text-gray-700 text-sm">{{ $process }}</span>
                </label>
                @endforeach
              </div>
            </fieldset>

            <div class="text-right">
              <button id="openModalBtn" type="button" class="bg-indigo-600 text-white font-semibold px-6 py-2 rounded-xl hover:bg-indigo-700 transition">
                Close
              </button>
            </div>
          </div>
        </div>

        <!-- Disposition Modal -->
        <div id="customerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
          <div class="bg-white p-6 rounded-xl w-full max-w-2xl relative">
            <button type="button" id="closeModalBtn" class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl font-bold">&times;</button>

           <!-- Disposition Modal -->
<div id="customerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white p-6 rounded-xl w-full max-w-2xl relative">
    <button type="button" id="closeModalBtn"
      class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl font-bold">&times;</button>

    <fieldset class="border border-gray-300 rounded-xl p-4">
      <legend class="text-sm font-semibold text-gray-700 mb-3">Disposition</legend>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        @foreach([
          'Sold!', 'Walked Away', 'Challenged Credit', "Didn't Like Vehicle",
          "Didn't Like Price", "Didn't Like Finance Terms", 'Insurance Expensive',
          'Wants to keep looking', 'Wants to think about it', 'Needs Co-Signer'
        ] as $disposition)
          <label class="flex items-center space-x-2">
            <input type="radio" name="disposition" value="{{ $disposition }}"
              {{ isset($sale) && $sale->disposition === $disposition ? 'checked' : '' }}
              class="form-radio h-5 w-5 text-indigo-600">
            <span class="text-gray-700 text-sm">{{ $disposition }}</span>
          </label>
        @endforeach
      </div>
    </fieldset>

    <div class="text-right mt-4">
      <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-3 rounded-xl">
        Save
      </button>
    </div>
  </div>
</div>


          
          </div>
        </div>
      </form>
    </div>

    <!-- Customer Cards -->
    <div class="md:col-span-4">
      <div id="customer-list">
        @foreach ($customers as $customer)
          @php
            $firstProcess = is_array($customer->process) ? ($customer->process[0] ?? 'N/A') : ($customer->process ?? 'N/A');
            $dispositions = is_array($customer->disposition) ? implode(', ', $customer->disposition) : ($customer->disposition ?? null);
            $salesPerson = $customer->user->name ?? 'Unknown';
          @endphp

          @if (is_null($customer->disposition))
            <div
              id="card-{{ $customer->id }}"
              class="customer-card bg-white shadow-md rounded-2xl p-4 border border-gray-200 mt-6 cursor-pointer transition-all duration-300"
              data-customer-id="{{ $customer->id }}"
              data-salesperson="{{ $salesPerson }}"
            >
              <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Customer Info</h2>
              </div>

              <div class="space-y-2 text-gray-500 text-sm mt-3">
                <p>
                  <span class="font-medium text-gray-400">Sales Person:</span>
                  <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full ml-2">
                    {{ $salesPerson }}
                  </span>
                </p>
                <p><span class="font-medium text-gray-400">Name:</span> {{ $customer->name }}</p>
                <p><span class="font-medium text-gray-400">Email:</span> {{ $customer->email }}</p>
                <p><span class="font-medium text-gray-400">Process:</span> {{ $firstProcess }}</p>
                <p><span class="font-medium text-gray-400">Disposition:</span> {{ $dispositions ?? 'N/A' }}</p>
              </div>

              <div class="w-full flex justify-between gap-2 mt-4">
                <button class="transfer-btn bg-[#111827] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#0f172a] transition w-full">
                  Transfer
                </button>
                <button id="toBtn" class="bg-indigo-600 text-white font-semibold px-4 py-2 rounded-md text-sm hover:bg-indigo-700 transition w-full" type="button">
                  T/O
                </button>
              </div>
            </div>
          @endif
        @endforeach
      </div>
    </div>
  </div>

  @if(session('success'))
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: '{{ session('success') }}',
      confirmButtonColor: '#111827'
    });
  </script>
  @endif

  @if(session('error'))
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: '{{ session('error') }}',
      confirmButtonColor: '#d33'
    });
  </script>


  @endif

  <script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('customerModal');
  const openModalBtn = document.getElementById('openModalBtn');
  const closeModalBtn = document.getElementById('closeModalBtn');

  if (openModalBtn && closeModalBtn && modal) {
    openModalBtn.addEventListener('click', () => {
      modal.classList.remove('hidden');
    });

    closeModalBtn.addEventListener('click', () => {
      modal.classList.add('hidden');
    });
  }

  const cards = document.querySelectorAll('.customer-card');
  cards.forEach(card => {
    card.addEventListener('click', () => {
      // Remove highlight from others
      cards.forEach(c => c.classList.remove('ring-2', 'ring-indigo-500', 'shadow-lg'));

      // Add active style
      card.classList.add('ring-2', 'ring-indigo-500', 'shadow-lg');

      // Fill form with card data
      const data = JSON.parse(card.dataset.customer);
      document.getElementById('customerId').value = data.id || '';
      document.querySelector('input[name="name"]').value = data.name || '';
      document.querySelector('input[name="email"]').value = data.email || '';
      document.querySelector('input[name="phone"]').value = data.phone || '';
      document.querySelector('input[name="interest"]').value = data.interest || '';
    });
  });
});
</script>

</x-app-layout>