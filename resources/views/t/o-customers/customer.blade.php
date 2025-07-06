<x-app-layout>
    <style>
        .swal2-confirm {
            background-color: #111827 !important;
            color: #fff !important;
            box-shadow: none !important;
        }

        .swal2-confirm:hover,
        .swal2-confirm:focus,
        .swal2-confirm:active {
            background-color: #111827 !important;
            color: #fff !important;
            box-shadow: none !important;
        }
    </style>

    <x-slot name="header">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <h3 class="text-2xl font-bold text-gray-800 leading-tight mb-0 px-2">Customer Sales Form</h3>
        <p class="text-gray-500 mt-0 leading-tight px-2">Fill out the details below to log a customer sales interaction.</p>
    </x-slot>

    <div class="py-1">
        <div class="container mx-auto space-y-6 py-1 px-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                <!-- Customer Sales Form -->
                <div class="md:col-span-9 mx-2">
  <div id="formContainer">
    <form id="salesForm" method="POST" action="{{ route('customer.sales.store') }}" class=" grid grid-cols-1 md:grid-cols-2 gap-8 bg-white rounded-2xl border border-gray-200 p-8 shadow-lg">
      @csrf
  <input type="hidden" name="appointment_id" value="{{ $appointment->id ?? '' }}">

<!-- <div class="md:col-span-2">
  <h3 class="text-2xl font-bold text-gray-800 leading-tight mb-0">Customer Sales Form</h3>
  <p class="text-gray-500 mt-0 leading-tight">Fill out the details below to log a customer sales interaction.</p>
</div> -->

     <input type="hidden" name="id" id="customerId" value="">
<input type="hidden" name="user_id" value="{{ auth()->id() }}" />

      <!-- Customer Info -->
<div class="space-y-4">
  @foreach (['name', 'email', 'phone', 'interest'] as $field)
    @php
      // Check if we should prefill from appointment
      $value = $sale->$field ?? '';
      if (isset($appointment)) {
        if ($field === 'name') {
          $value = $appointment->customer_name ?? $value;
        } elseif ($field === 'phone') {
          $value = $appointment->customer_phone ?? $value;
        }
      }
    @endphp

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">
        {{ ucfirst($field) }}
      
      </label>

      <input
        id="{{ $field === 'name' ? 'nameInput' : $field . 'Input' }}"
        name="{{ $field }}"
        type="{{ $field === 'email' ? 'email' : 'text' }}"
        class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full"
        value="{{ $value }}"
        
      />
    </div>
  @endforeach
</div>


      <!-- Sales Details -->
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
          <textarea name="notes" rows="6"
            class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full">{{ $sale->notes ?? '' }}</textarea>
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
              <button type="submit"
                class="bg-gray-800 text-white px-3 py-1.5 rounded ">
                Save
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Trigger -->
      <div class="md:col-span-2 text-right mt-4">
       <button id="openModalBtn"  type="button"
          class="bg-gray-800 text-white px-3 py-1.5 rounded">
          Close
        </button>
<button 
  type="button"
  id="toBtn"
  class=" relative bg-gray-800 text-white px-4 py-1.5 rounded"
>
  <span class="btn-label">T/O</span>
  <div class="toSpinner hidden absolute inset-0 bg-black/50 flex items-center justify-center z-10 rounded">
    <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
  </div>
</button>






      </div>
    </form>
  </div>
                </div>

                <!-- Customer Cards -->
                <div class="md:col-span-3">
                    <div id="customer-list">
                        @include('partials.customer-list', ['customers' => $customers])
                    </div>
                </div>

            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('salesForm');
  const idInput = form.querySelector('input[name="id"]');
  const nameInput = form.querySelector('input[name="name"]');
  const emailInput = form.querySelector('input[name="email"]');
  const phoneInput = form.querySelector('input[name="phone"]');
  const interestInput = form.querySelector('input[name="interest"]');
  const appointmentInput = form.querySelector('input[name="appointment_id"]');
  const newCustomerBtn = document.getElementById('newCustomerBtn');
  const addCustomerBtn = document.getElementById('addCustomerBtn');

  let debounceTimeout;
  let customerSavedThisTurn = false;
  let autosaveEnabled = false;
  let loadedFromAppointment = false;

  setInterval(() => {
    customerSavedThisTurn = false;
  }, 3000);

  const attachFieldListeners = () => {
    const fields = form.querySelectorAll('input, textarea, select');
    fields.forEach(field => {
      if (field._autoSaveHandlerInput)
        field.removeEventListener('input', field._autoSaveHandlerInput);
      if (field._autoSaveHandlerChange)
        field.removeEventListener('change', field._autoSaveHandlerChange);

      field._autoSaveHandlerInput = () => {
        if (!autosaveEnabled) return;
        if (loadedFromAppointment && ['email', 'name', 'phone', 'interest'].includes(field.name)) {
          idInput.value = '';
          loadedFromAppointment = false;
        }
        customerSavedThisTurn = false;
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
          autoSaveForm();
        }, 700);
      };

      field._autoSaveHandlerChange = () => {
        if (!autosaveEnabled) return;
        if (loadedFromAppointment && ['email', 'name', 'phone', 'interest'].includes(field.name)) {
          idInput.value = '';
          loadedFromAppointment = false;
        }
        customerSavedThisTurn = false;
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
          autoSaveForm();
        }, 300);
      };

      field.addEventListener('input', field._autoSaveHandlerInput);
      field.addEventListener('change', field._autoSaveHandlerChange);
    });
  };

  nameInput.addEventListener('input', () => {
    if (autosaveEnabled) return;
    customerSavedThisTurn = false;
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
      if (!nameInput.value.trim()) return;
    }, 300);
  });

  async function autoSaveForm() {
    const formData = new FormData(form);
    try {
      await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        }
      });
    } catch (err) {
      console.error('Auto-save failed:', err);
    }
  }

  async function loadCustomers() {
    try {
      const resp = await fetch('{{ route('customer.index') }}?partial=1', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });

      const html = await resp.text();
      document.getElementById('customer-list').innerHTML = html;

      bindCardClickEvents();
      bindAppointmentCardClick();
      applyActiveCard();
    } catch (err) {
      console.error('Failed to load customers', err);
    }
  }

  function bindAppointmentCardClick() {
    const appointmentCard = document.querySelector('#appointment-card');
    if (!appointmentCard) return;

    appointmentCard.addEventListener('click', async () => {
      if (appointmentCard.classList.contains('hidden')) return;
      if (appointmentCard.dataset.used === 'true') return;

      const customerId = appointmentCard.dataset.customerId;

      clearFormFields();

      idInput.value = '';
      nameInput.value = appointmentCard.dataset.name || '';
      emailInput.value = appointmentCard.dataset.email ?? '';
      phoneInput.value = appointmentCard.dataset.phone ?? '';
      interestInput.value = appointmentCard.dataset.interest ?? '';
      appointmentInput.value = appointmentCard.dataset.appointmentId ?? '';

      if (appointmentCard.dataset.process) {
        appointmentCard.dataset.process.split(',').forEach(proc => {
          const checkbox = [...form.querySelectorAll('input[name="process[]"]')]
            .find(cb => cb.value.trim() === proc.trim());
          if (checkbox) checkbox.checked = true;
        });
      }

      document.querySelectorAll('.customer-card').forEach(c => {
        c.classList.remove('active-card');
        c.classList.remove('pause-animation');
      });

      appointmentCard.classList.add('active-card');
      appointmentCard.dataset.used = 'true';

      localStorage.setItem('activeCustomerId', customerId);
      loadedFromAppointment = true;
      autosaveEnabled = true;
      attachFieldListeners();

      await autoSaveForm();
      appointmentCard.classList.add('hidden');
    });
  }

  function bindCardClickEvents() {
  document.querySelectorAll('.customer-card').forEach(card => {
    if (card.id === 'appointment-card') return;

    card.addEventListener('click', async () => {
      // Clear all cards' animation
      document.querySelectorAll('.customer-card').forEach(c => {
        c.classList.remove('active-card');
        c.classList.add('pause-animation');
      });

      // Form fill logic
      clearFormFields();
      idInput.value = card.dataset.customerId;
      nameInput.value = card.dataset.name || '';
      emailInput.value = card.dataset.email ?? '';
      phoneInput.value = card.dataset.phone ?? '';
      interestInput.value = card.dataset.interest ?? '';

      if (card.dataset.process) {
        card.dataset.process.split(',').forEach(proc => {
          const checkbox = [...form.querySelectorAll('input[name="process[]"]')]
            .find(cb => cb.value.trim() === proc.trim());
          if (checkbox) checkbox.checked = true;
        });
      }

      // Enable autosave and reattach listeners
      autosaveEnabled = true;
      attachFieldListeners();

      // Update localStorage
      localStorage.setItem('activeCustomerId', card.dataset.customerId);

      // ❗ Restart animation class manually
      card.classList.remove('pause-animation');

      // Force animation restart 👇
      card.classList.remove('active-card'); // remove temporarily
      void card.offsetWidth;                // force reflow
      card.classList.add('active-card');    // re-add class to restart animation
    });
  });
}

  function clearFormFields() {
    form.reset();
    form.querySelectorAll('input[type="hidden"]').forEach(el => {
      if (!['id', 'user_id', 'appointment_id'].includes(el.name)) {
        el.value = '';
      }
    });
  }

 function applyActiveCard() {
    const savedId = localStorage.getItem('activeCustomerId');
    const savedCard = document.querySelector(`.customer-card[data-customer-id="${savedId}"]`);
    if (!savedCard || savedCard.id === 'appointment-card') return;

    savedCard.classList.add('active-card');
    if (!idInput.value || idInput.value === savedId) {
      clearFormFields();

      idInput.value = savedId;
      nameInput.value = savedCard.dataset.name || '';
      emailInput.value = savedCard.dataset.email ?? '';
      phoneInput.value = savedCard.dataset.phone ?? '';
      interestInput.value = savedCard.dataset.interest ?? '';

      if (savedCard.dataset.process) {
        savedCard.dataset.process.split(',').forEach(proc => {
          const checkbox = [...form.querySelectorAll('input[name="process[]"]')]
            .find(cb => cb.value.trim() === proc.trim());
          if (checkbox) checkbox.checked = true;
        });
      }

      autosaveEnabled = true;
      attachFieldListeners();
    }
  }

  if (addCustomerBtn) {
    addCustomerBtn.addEventListener('click', () => {
      const activeCard = document.querySelector('.active-card');
      if (activeCard) {
        activeCard.classList.add('pause-animation');
      }
    });
  }

  // Init
  bindCardClickEvents();
  bindAppointmentCardClick();
  applyActiveCard();
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const salespeople = @json($salespeople);
    const currentUserId = @json(auth()->id());

    document.body.addEventListener('click', function (e) {
      if (!e.target.classList.contains('transfer-btn')) return;

      const button = e.target;
      const customerId = button.dataset.customerId;
      const customerName = button.dataset.customerName;

      let options = '<option disabled selected value="">Choose a sales person</option>';
      salespeople.forEach(sales => {
        if (sales.id !== currentUserId) {
          options += `<option value="${sales.id}">${sales.name}</option>`;
        }
      });

      Swal.fire({
        title: `<div class="text-xl font-bold text-[#111827] mb-2">Transfer Customer</div>`,
        html: `
          <div class="text-sm text-[#111827] mb-4">
            You are about to transfer
            <span class="font-semibold text-indigo-600">${customerName}</span>
            to another sales person.
          </div>
          <label class="block text-sm font-medium mb-1 text-[#111827]">Select Sales Person:</label>
          <select id="salespersonSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-[#111827] text-[#111827]">
            ${options}
          </select>
        `,
        showCancelButton: true,
        confirmButtonText: 'Confirm Transfer',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
          const val = document.getElementById('salespersonSelect').value;
          if (!val) {
            Swal.showValidationMessage('Please select a sales person.');
          }
          return val;
        },
        customClass: {
          popup: 'rounded-2xl p-6 shadow-xl',
          confirmButton: 'bg-[#111827] text-white px-5 py-2 mt-4 rounded-lg font-semibold',
          cancelButton: 'mx-3 bg-[#111827] text-white px-5 py-2 mt-4 rounded-lg font-semibold',
        }
      }).then(result => {
        if (!result.isConfirmed) return;

        const selectedSalesId = result.value;

        fetch(`/customers/${customerId}/transfer`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            new_user_id: selectedSalesId
          })
        })
        .then(response => response.json())
        .then(data => {
          Swal.fire({
            icon: 'success',
            title: 'Customer Transferred',
            text: data.message,
            timer: 1500,
            showConfirmButton: true
          });

          // 2 second ke baad page reload
          setTimeout(() => {
            location.reload();
          }, 2000);
        })
        .catch(error => {
          console.error(error);
          Swal.fire('Error!', 'Transfer failed. Please try again.', 'error');
        });
      });
    });
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const toBtn   = document.getElementById('toBtn');
  const spinner = toBtn.querySelector('.toSpinner');
  const label   = toBtn.querySelector('.btn-label');
  const form    = document.getElementById('salesForm');

  async function forwardCard() {
    const customer_id = form.querySelector('input[name="id"]')?.value.trim();

    if (!customer_id) {
      Swal.fire('Error', 'No customer selected.', 'error');
      return;
    }

    spinner.classList.remove('hidden');
    label.classList.add('opacity-0');
    toBtn.disabled = true;

    try {
      const response = await fetch("{{ route('customer.forward') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id: customer_id }) // ✅ 'id', not 'customer_id'
      });

      if (!response.ok) {
        const errRes = await response.text();
        throw new Error(`Server error: ${errRes}`);
      }

      const result = await response.json();

      localStorage.setItem('manager_notification', 'T/O Customer forwarded to Sales Manager.');

      Swal.fire({
        icon: 'success',
        title: 'Transferred!',
        text: result.message || 'Card moved to Sales Manager.',
        timer: 2000,
        showConfirmButton: true
      }).then(() => {
        // ✅ Remove specific card instead of full list
       
      });

    } catch (error) {
      console.error('Forward error:', error); // ✅ Add logging
      Swal.fire('Error', error.message || 'Something went wrong.', 'error');
    } finally {
      spinner.classList.add('hidden');
      label.classList.remove('opacity-0');
      toBtn.disabled = false;
    }
  }

  toBtn.addEventListener('click', forwardCard);
});
</script> 


 <script>
    document.getElementById('salesForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);

      // Show processing alert
      Swal.fire({
        title: 'Processing...',
        text: 'Please wait while we save your data.',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      try {
        const response = await fetch("{{ route('customer.sales.store') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: formData
        });

        const result = await response.json();

        if (response.ok) {
          // Show success message, then redirect
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: result.message || 'Form submitted successfully',
            timer: 2000,
            showConfirmButton: true,
            willClose: () => {
              // 👇 Redirect after SweetAlert closes
              window.location.href = result.redirect;
            }
          });

          form.reset(); // Optional
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: result.message || 'Something went wrong!',
          });
        }

      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Request failed. Please try again.'
        });
      }
    });
  </script>
<script>
    const modal = document.getElementById('customerModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');

    // Open modal
    openBtn.addEventListener('click', () => {
      modal.classList.remove('hidden');
    });

    // Close modal
    closeBtn.addEventListener('click', () => {
      modal.classList.add('hidden');
    });

    // Click outside to close
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.add('hidden');
      }
    });
  </script>

</x-app-layout>