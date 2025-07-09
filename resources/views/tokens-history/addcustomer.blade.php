<x-app-layout>
  <!-- Tailwind CSS CDN (v3) -->
<script src="https://cdn.tailwindcss.com"></script>
    <style>
.swal2-confirm {
  background-color: #111827 !important;
  color: #fff !important;
}
.swal2-confirm:hover,
.swal2-confirm:focus,
.swal2-confirm:active {
  background-color: #111827 !important;
}
</style>
  <x-slot name="header">
    <div class="md:col-span-2">
      <h3 class="text-2xl font-bold text-gray-800 leading-tight mb-0">Customer Sales Form</h3>
      <p class="text-gray-500 mt-0 leading-tight">Fill out the details below to log a customer sales interaction.</p>
    </div>
  </x-slot>

  <div class="px-4">
     <div id="formContainer">
      <form id="salesForm" method="POST" action="{{ route('customer.sales.store') }}"
        class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white rounded-2xl border border-gray-200 p-8 shadow-lg">
        @csrf
        <input type="hidden" name="appointment_id" value="{{ $appointment->id ?? '' }}">
        <input type="hidden" name="id" id="customerId" value="">
        <input type="hidden" name="user_id" value="{{ auth()->id() }}" />

        <div class="md:col-span-2">
          <h3 class="text-2xl font-bold text-gray-800 leading-tight mb-0">Customer Sales Form</h3>
          <p class="text-gray-500 mt-0 leading-tight">Fill out the details below to log a customer sales interaction.</p>
        </div>

        <!-- Customer Info -->
        <div class="space-y-4">
          @foreach (['name', 'email', 'phone', 'interest'] as $field)
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">{{ ucfirst($field) }}</label>
            <input
              id="{{ $field === 'name' ? 'nameInput' : $field . 'Input' }}"
              name="{{ $field }}"
              type="{{ $field === 'email' ? 'email' : 'text' }}"
              class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full"
            />
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
                <button type="submit" class="bg-gray-800 text-white px-3 py-1.5 rounded">Save</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Triggers -->
        <div class="md:col-span-2 text-right mt-4">
          <button id="openModalBtn" type="button" class="bg-gray-800 text-white px-3 py-1.5 rounded">Close</button>
          <button type="button" id="toBtn" class="relative bg-gray-800 text-white px-4 py-1.5 rounded">
            <span class="btn-label">T/O</span>
            <div class="toSpinner hidden absolute inset-0 bg-black/50 flex items-center justify-center z-10 rounded">
              <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            </div>
          </button>
        </div>
      </form>
    </div>
  </div>

 @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modal = document.getElementById('customerModal');
    const salesForm = document.getElementById('salesForm');

    // Modal toggle
    openModalBtn?.addEventListener('click', () => modal.classList.remove('hidden'));
    closeModalBtn?.addEventListener('click', () => modal.classList.add('hidden'));
    window.addEventListener('click', (e) => {
      if (e.target === modal) modal.classList.add('hidden');
    });

    // ✅ AJAX form submit with Swal
    salesForm?.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const url = this.action;

      fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
        },
        body: formData,
      })
      .then(res => res.json())
      .then(data => {
      if (data.status === 'success') {
  Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#111827',
            }).then(() => {
                window.location.href = data.redirect;
            });

  modal.classList.add('hidden');
  salesForm.reset();
}
 else {
          Swal.fire('Error', 'Something went wrong!', 'error');
        }
      })
      .catch(err => {
        Swal.fire('Error', 'Request failed!', 'error');
        console.error(err);
      });
    });
  });
</script>
@endpush


</x-app-layout>
