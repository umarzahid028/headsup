<x-app-layout>
  <x-slot name="header">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="flex justify-between items-center px-2">
      <div>
        <h1 class="text-xl font-semibold text-gray-800">Welcome, {{ Auth::user()->name }}</h1>
        <p class="text-sm text-gray-500">Manage your check-in and token activity.</p>
      </div>
    </div>
  </x-slot>

 

  <div class="w-full grid grid-cols-1 xl:grid-cols-4 gap-6 px-4 mt-4">
    <!-- LEFT SIDE: Customer Form -->
    <div class="xl:col-span-3">
      <form id="salesForm" method="POST" action="{{ route('appointments.form.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white rounded-2xl border border-gray-200 p-8 shadow-lg">
        @csrf
        <input type="hidden" name="id" id="customerId" value="">
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

        <div class="md:col-span-2">
          <h3 class="text-2xl font-bold text-gray-800">Customer Sales Form</h3>
          <p class="text-gray-500">Fill out the details below to log a customer sales interaction.</p>
        </div>

        <!-- Customer Info -->
        <div class="space-y-4">
          @foreach (['name', 'email', 'phone', 'interest'] as $field)
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">
              {{ ucfirst($field) }}
              @if(in_array($field, ['name', 'phone'])) <span class="text-red-600">*</span> @endif
            </label>
            <input id="field-{{ $field }}" name="{{ $field }}" type="{{ $field == 'email' ? 'email' : 'text' }}"
              class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full"
              value="{{ $sale->$field ?? '' }}"
              @if(in_array($field, ['name', 'email', 'phone'])) required @endif>
          </div>
          @endforeach
        </div>

        <!-- Sales Details -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="6" class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full">{{ $sale->notes ?? '' }}</textarea>
          </div>

          <!-- Process -->
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

          <!-- Modal Trigger -->
          <div class="text-right mt-4">
            <button id="openModalBtn" style="background-color: #111827;" type="button" class="bg-indigo-600 text-white font-semibold px-6 py-2 rounded-xl hover:bg-indigo-500">
              Close
            </button>
          </div>
        </div>

        <!-- Disposition Modal -->
        <div id="customerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
          <div class="bg-white p-6 rounded-xl w-full max-w-2xl relative">
            <button type="button" id="closeModalBtn" class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl font-bold">&times;</button>

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
              <button type="submit" style="background-color: #111827;" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-3 rounded-xl">
                Save
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- RIGHT SIDE: Appointment Card -->
    <div class="xl:col-span-1">
      <div class="bg-white shadow-md rounded-xl p-4 border border-gray-200 hover:shadow-lg transition-all cursor-pointer">
        <h3 class="text-lg font-bold mb-2">Appointment Details</h3>
        <ul class="space-y-1 text-gray-700 text-sm" id="appointmentCard">
          <li><strong>Name:</strong> <span id="card-customer-name">{{ $appointment->customer_name }}</span></li>
          <li><strong>Sales Person:</strong> {{ $appointment->salesperson->name ?? 'N/A' }}</li>
          <li><strong>Email:</strong> <span id="card-email">{{ $appointment->email ?? '–' }}</span></li>
          <li><strong>Phone:</strong> <span id="card-phone">{{ $appointment->customer_phone ?? '–' }}</span></li>
          <li><strong>Date:</strong> {{ $appointment->date }} {{ $appointment->time }}</li>
          <li><strong>Status:</strong>
            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
              @switch($appointment->status)
                @case('processing') bg-yellow-200 text-yellow-800 @break
                @case('completed') bg-green-200 text-green-800 @break
                @case('no_show') bg-red-200 text-red-800 @break
                @default bg-gray-200 text-gray-800
              @endswitch">
              {{ ucfirst($appointment->status) }}
            </span>
          </li>
        </ul>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
  document.addEventListener('DOMContentLoaded', function () {
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modal = document.getElementById('customerModal');

    openModalBtn?.addEventListener('click', () => modal.classList.remove('hidden'));
    closeModalBtn?.addEventListener('click', () => modal.classList.add('hidden'));
    window.addEventListener('click', (e) => {
      if (e.target === modal) modal.classList.add('hidden');
    });

    const appointmentCard = document.getElementById('appointmentCard');
    appointmentCard?.addEventListener('click', function () {
      const name = document.getElementById('card-customer-name')?.textContent.trim();
      const email = document.getElementById('card-email')?.textContent.trim();
      const phone = document.getElementById('card-phone')?.textContent.trim();

      document.getElementById('field-name').value = name || '';
      document.getElementById('field-email').value = email || '';
      document.getElementById('field-phone').value = phone || '';
      document.getElementById('field-interest').focus();
    });

    @if (session('success'))
      Swal.fire({
        title: 'Success!',
        text: '{{ session('success') }}',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
      });
    @endif
  });
</script>

  @endpush

</x-app-layout>
