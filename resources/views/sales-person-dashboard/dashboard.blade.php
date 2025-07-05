<x-app-layout>
  <x-slot name="header">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div style="display:flex; justify-content: space-between;">
      <div class="px-2">
        <h1 class="text-xl font-semibold text-gray-800">Welcome, {{ Auth::user()->name }}</h1>
        <p class="text-sm text-gray-500">Manage your check-in activity.</p>
      </div>
      <div>
        @if(auth()->user()->hasrole('Sales person'))
        <p id="turn-status" style="text-align:center;" class="text-sm text-gray-700 font-medium my-2 animate-pulse-text">
          Checking status...
        </p>
        @endif
      </div>
    </div>

    <style>
      @keyframes spin {
        to {
          transform: rotate(360deg);
        }
      }

      .spinner {
        border: 2px solid transparent;
        border-radius: 50%;
        width: 1rem;
        height: 1rem;
        animation: spin 1s linear infinite;
      }

      @keyframes pulseText {
        0%, 100% {
          opacity: 1;
          transform: scale(1);
        }
        50% {
          opacity: 0.6;
          transform: scale(1.03);
        }
      }

      .animate-pulse-text {
        animation: pulseText 1.2s ease-in-out infinite;
      }

      #customerCards::-webkit-scrollbar {
        width: 6px;
      }

      #customerCards::-webkit-scrollbar-thumb {
        background-color: rgba(100, 116, 139, 0.5);
        border-radius: 9999px;
      }

      .swal2-popup.no-scroll-popup {
        max-width: 400px;
        overflow-x: hidden !important;
      }

      .swal2-select {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        border-radius: 6px;
        border: 1px solid #ccc;
        padding: 8px;
        overflow-x: hidden;
      }

      .swal2-popup {
        overflow-x: hidden !important;
      }

      /* SweetAlert2 OK button default background */
.swal2-container button.swal2-confirm {
  background-color: #111827 !important;
  color: white !important;
  border-radius: 6px;
  transition: background-color 0.3s ease;
  box-shadow: none !important;
}

/* Hover effect for OK button */
.swal2-container button.swal2-confirm:hover {
  background-color: #0f172a !important; /* Thoda dark shade */
  color: white !important;
}

    </style>
    <style>
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
  .animate-spin {
    animation: spin 1s linear infinite;
  }
</style>

  </x-slot>

  @php
    $user = Auth::user();
    $latestQueue = $user->latestQueue;
    $isCheckedIn = $latestQueue && $latestQueue->is_checked_in;
    $checkInTimeRaw = $latestQueue?->checked_in_at;
    $checkInTimeFormatted = $checkInTimeRaw ? \Carbon\Carbon::parse($checkInTimeRaw)->format('h:i A, M d') : 'N/A';
    $checkOutTimeFormatted = $latestQueue?->checked_out_at ? \Carbon\Carbon::parse($latestQueue->checked_out_at)->format('h:i A, M d') : 'N/A';
  @endphp

  <div class="w-full grid grid-cols-1 xl:grid-cols-4 gap-6 px-4 mt-4">
    <!-- LEFT SIDE: Customer Form -->
   <div class="xl:col-span-3  overflow-visible">
  <div id="formContainer">
    <form id="salesForm" method="POST" action="{{ route('customer.sales.store') }}" class=" grid grid-cols-1 md:grid-cols-2 gap-8 bg-white rounded-2xl border border-gray-200 p-8 shadow-lg">
      @csrf
  <input type="hidden" name="appointment_id" value="{{ $appointment->id ?? '' }}">

<div class="md:col-span-2">
  <h3 class="text-2xl font-bold text-gray-800 leading-tight mb-0">Customer Sales Form</h3>
  <p class="text-gray-500 mt-0 leading-tight">Fill out the details below to log a customer sales interaction.</p>
</div>

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
  class="relative bg-gray-800 text-white px-4 py-1.5 rounded"
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

@if (Auth()->user()->hasRole('Sales person'))
  
<!-- RIGHT SIDE -->
<div class="xl:col-span-1 flex flex-col h-[calc(100vh-10rem)]">
  <div class="bg-white rounded-xl shadow p-3 w-full max-w-md mx-auto space-y-4 border mb-4">

    <!-- Status + Button -->
    <div class="flex items-center justify-between">
     <span class="status-text text-sm font-semibold px-3 py-1 rounded-md flex items-center gap-1
{{ $isCheckedIn ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">

@if($isCheckedIn)
<!-- Check Icon -->
<svg class="w-4 h-4 text-green-800" fill="none" stroke="currentColor" stroke-width="2"
     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
</svg>
Checked In
@else
<!-- X Icon -->
<svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" stroke-width="2"
     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
</svg>
Checked Out
@endif
</span>


      <form id="checkToggleForm" action="{{ route('sales.person.store') }}" method="POST">
        @csrf
        <button type="submit"
          id="checkToggleButton"
          class="check-toggle-btn px-6 py-2 text-sm font-semibold flex items-center gap-2 rounded-md text-white shadow-md
          {{ $isCheckedIn ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
          <span class="btn-text">{{ $isCheckedIn ? 'Check Out' : 'Check In' }}</span>
          <svg class="btn-spinner hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
              stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 010 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z" />
          </svg>
        </button>
      </form>
    </div>

    <!-- Time Info -->
    <div class="text-left space-y-1">
      <p class="text-xs text-gray-600"><strong>Check In:</strong> <span id="check-in-time">{{ $checkInTimeFormatted }}</span></p>
      <p class="text-xs text-gray-600"><strong>Check Out:</strong> <span id="check-out-time">{{ $checkOutTimeFormatted }}</span></p>
      <p class="text-xs text-gray-600 {{ $isCheckedIn ? '' : 'hidden' }}" id="duration-wrapper"><strong>Duration:</strong> <span id="duration">Loading...</span></p>
    </div>

    <div>
<!-- Take Customer Button (Initially hidden) -->
<button
id="newCustomerBtn"
type="button" 
class="w-full bg-gray-800 text-white font-semibold px-6 py-2 rounded mb-4 hidden flex items-center justify-center gap-2"
>
<span class="spinner hidden w-5 h-5 border-2 border-white border-t-transparent rounded animate-spin"></span>
<span class="btn-text">Take Customer</span>
</button>


<button
id="addCustomerBtn"
type="button" 
class="w-full bg-gray-800 text-white  px-6 py-2 rounded mb-4 hidden">
Add Customer
</button>



    </div>
  </div>
  <!-- Scrollable Customers -->
  <div class="flex-1 overflow-y-auto pr-2" id="customerCards">
    @include('partials.customers', ['customers' => $customers])
  
  <div id="appointment-wrapper">
  @include('partials.appointment-card', ['appointment' => $appointment])
  </div>
  
  
  </div>
  </div>
@endif

  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- T/O request -->

<!-- <script>
  document.addEventListener('DOMContentLoaded', () => {
    const toButton = document.querySelector('.toBtn');
    if (!toButton) return; // safety check

    const spinner = toButton.querySelector('.toSpinner');
    const customerIdInput = document.getElementById('customerId');

    toButton.addEventListener('click', async () => {
      const customerId = customerIdInput?.value;

      if (!customerId) {
        Swal.fire({
          icon: 'warning',
          title: 'No Customer Selected',
          text: 'Please select or load a customer first.'
        });
        return;
      }

      spinner.classList.remove('hidden');

      try {
        await new Promise(resolve => setTimeout(resolve, 1500)); // Optional delay

        const response = await fetch('forward-customer', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ customer_id: customerId })
        });

        const result = await response.json();

        if (result.status === 'success') {
          await Swal.fire({
            icon: 'success',
            title: 'T/O Requested',
            text: 'T/O successfully.',
            timer: 2000,
            showConfirmButton: true
          });
        } else {
          throw new Error('Forward failed.');
        }
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: err.message || 'Something went wrong.'
        });
      } finally {
        spinner.classList.add('hidden');
      }
    });
  });
</script> -->



<script>
  let selectedCardId = null;

  function bindAppointmentCardLogic() {
    const nameInput = document.getElementById('nameInput');
    const phoneInput = document.getElementById('phoneInput');
    const idInput = document.getElementById('customerId');

    document.querySelectorAll('.customer-card').forEach(card => {
      card.addEventListener('click', () => {
        const name = card.dataset.name || '';
        const phone = card.dataset.phone || '';
        const id = card.dataset.customerId || '';

        nameInput.value = name;
        phoneInput.value = phone;
        idInput.value = id;

        document.querySelectorAll('.customer-card').forEach(c => c.classList.remove('active-card'));
        card.classList.add('active-card');

        selectedCardId = card.id;
      });
    });
  }

  function checkDuplicateName() {
    const appointmentCard = document.querySelector('#appointment-card');
    if (!appointmentCard) return;

    const appointmentName = appointmentCard.dataset.name?.trim().toLowerCase();
    const customerCards = document.querySelectorAll('.customer-card');

    customerCards.forEach(card => {
      const customerName = card.dataset.name?.trim().toLowerCase();
      if (card.id !== 'appointment-card' && customerName === appointmentName) {
        appointmentCard.classList.add("border-red-500");
        appointmentCard.classList.remove("border-gray-200");
      }
    });
  }

  function refreshAppointments() {
    fetch('/appointment/section')
      .then(res => res.text())
      .then(html => {
        const wrapper = document.getElementById("appointment-wrapper");
        wrapper.innerHTML = html;

        // Bind logic to new cards
        bindAppointmentCardLogic();
        checkDuplicateName();

        // Reapply active card style
        if (selectedCardId) {
          const selectedCard = document.getElementById(selectedCardId);
          if (selectedCard) selectedCard.classList.add('active-card');
        }
      });
  }

  document.addEventListener('DOMContentLoaded', () => {
    bindAppointmentCardLogic();
    checkDuplicateName();

    setInterval(refreshAppointments, 3000);
  });
</script>



<script>
  $(document).ready(() => {
    toggleButton();
    updateTurnStatus();
    setInterval(updateTurnStatus, 10000);

    const form = document.getElementById('salesForm');

    function fillFormFromCard(card) {
      const name = card.dataset.name || '';
      const phone = card.dataset.phone || '';
      const customerId = card.dataset.customerId || '';

      // Set values to inputs
      const nameInput = document.getElementById('nameInput');
      const phoneInput = document.getElementById('phoneInput');
      const idInput = form.querySelector('input[name="id"]');

      if (nameInput) nameInput.value = name;
      if (phoneInput) phoneInput.value = phone;
      if (idInput) idInput.value = customerId;

      // Clear previous animation
      document.querySelectorAll('.customer-card').forEach(c => {
        c.classList.remove('active-card');
        c.classList.remove('pause-animation');
      });

      // Re-trigger animation
      card.classList.remove('active-card');
      void card.offsetWidth; // Force reflow to restart animation
      card.classList.add('active-card');

      toggleButton();
      updateNameInputState();
    }

    const appointmentCard = document.querySelector('#appointment-card');
    if (appointmentCard) {
      fillFormFromCard(appointmentCard);
    }

    // Click event in case card is clicked again
    document.querySelectorAll('.customer-card').forEach(card => {
      card.addEventListener('click', () => {
        fillFormFromCard(card);
      });
    });
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const nameInput = document.getElementById('nameInput');
  const newCustomerBtn = document.getElementById('newCustomerBtn');

  function toggleButton() {
    const hasValue = nameInput.value.trim().length > 0;

    newCustomerBtn.disabled = !hasValue;
    newCustomerBtn.classList.toggle('bg-gray-400', !hasValue);
    newCustomerBtn.classList.toggle('bg-[#111827]', hasValue);
  }

  nameInput.addEventListener('input', toggleButton);
  toggleButton(); // Initial check on page load
});
</script>

<!-- Time duration  -->
<script>
function completeForm(customerId) {
    fetch(`/customer/complete-form/${customerId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Form completed');
    });
}
</script>



<script>
  let durationInterval = null;

  function startDurationTimer(startTimeIso) {
    const start = new Date(startTimeIso);

    function updateDuration() {
      const now = new Date();
      const diffMs = now - start;

      const seconds = Math.floor((diffMs / 1000) % 60);
      const minutes = Math.floor((diffMs / 1000 / 60) % 60);
      const hours = Math.floor((diffMs / 1000 / 60 / 60));

      const formatted = [
        hours > 0 ? `${hours}h` : '',
        minutes > 0 ? `${minutes}m` : '',
        `${seconds}s`
      ].filter(Boolean).join(' ');

      document.getElementById('duration').textContent = formatted;
    }

    updateDuration();
    if (durationInterval) clearInterval(durationInterval);
    durationInterval = setInterval(updateDuration, 1000);
  }

  @if($isCheckedIn && $checkInTimeRaw)
    startDurationTimer('{{ \Carbon\Carbon::parse($checkInTimeRaw)->toIso8601String() }}');
  @endif

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $('#checkToggleForm').on('submit', function(e) {
    e.preventDefault();

    const btn = $('#checkToggleButton');
    const btnText = btn.find('.btn-text');

    // ✅ Block check-out if cards exist
    if (btnText.text().trim() === 'Check Out' && $('#customer-list .customer-card').length > 0) {
      Swal.fire({
        icon: 'error',
         title: 'Pending Customer Cards',
  text: 'Please complete or transfer all customer cards before checking out.',
      });
      return;
    }

    const spinner = btn.find('.btn-spinner');
    btn.prop('disabled', true);
    btnText.addClass('hidden');
    spinner.removeClass('hidden');

    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: $(this).serialize(),
      success: function(response) {
        btn.prop('disabled', false);
        btnText.removeClass('hidden');
        spinner.addClass('hidden');

        if (response.checked_in) {
          // ✅ Checked In UI
          btnText.text('Check Out');
          btn.removeClass('bg-green-500 hover:bg-green-600')
            .addClass('bg-red-500 hover:bg-red-600');

          $('.status-text').text('✅ Checked In')
            .removeClass('bg-red-100 text-red-700')
            .addClass('bg-green-100 text-green-800');

          $('#check-in-time').text(new Date(response.checked_in_at).toLocaleString());
          $('#check-out-time').text('N/A');

          $('#duration-wrapper').removeClass('hidden');
          $('#duration').text('Loading...');
          startDurationTimer(response.checked_in_at);

        } else {
          // ❌ Checked Out UI
          btnText.text('Check In');
          btn.removeClass('bg-red-500 hover:bg-red-600')
            .addClass('bg-green-500 hover:bg-green-600');

          $('.status-text').text('❌ Checked Out')
            .removeClass('bg-green-100 text-green-800')
            .addClass('bg-red-100 text-red-700');

          $('#check-out-time').text(new Date().toLocaleString());
          $('#duration-wrapper').addClass('hidden');
          clearInterval(durationInterval);
        }

        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: response.message,
          timer: 2000,
          showConfirmButton: true,
        });
      },
      error: function() {
        btn.prop('disabled', false);
        btnText.removeClass('hidden');
        spinner.addClass('hidden');

        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Something went wrong. Please try again.',
        });
      }
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
        const card = document.querySelector(`#card-${customer_id}`);
        if (card) {
          card.classList.add('fade-out');
          setTimeout(() => card.remove(), 300);
        }

        form.reset();
        setTimeout(() => {
          window.location.reload();
        }, 2000);
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


  <!-- JavaScript for Transfer -->

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
  let currentTurnUserId = null;
  let isMyTurn = false;
  let wasTurnBefore = false;
  let cardClicked = false;
  let customerSavedThisTurn = false;

  const form = document.getElementById('salesForm');
  const nameInput = document.getElementById('nameInput');
  const addBtn = document.getElementById('addCustomerBtn');
  const takeBtn = document.getElementById('newCustomerBtn');
  const customerIdInput = document.getElementById('customerId');

  nameInput.readOnly = true;

  const inputs = form.querySelectorAll('input[type="text"], input[type="email"], textarea');

  // Toggle buttons based on input status
function toggleButtons() {
  const nameVal = nameInput.value.trim();
  const hasCustomerId = customerIdInput.value.trim() !== '';
  let otherFieldFilled = false;

  inputs.forEach(input => {
    if (input.id !== 'nameInput' && input.value.trim() !== '') {
      otherFieldFilled = true;
    }
  });

  // Show Add button ONLY when there's an ID
  if (hasCustomerId) {
    addBtn.classList.remove('hidden');
    takeBtn.classList.add('hidden');
  } else {
    addBtn.classList.add('hidden');
    takeBtn.classList.remove('hidden');
  }

  takeBtn.disabled = false;
}


  // Toggle readonly on name field
function updateNameInputState() {
  nameInput.readOnly = false;
}


  // Customer card clicked
  document.addEventListener('click', function (e) {
    const card = e.target.closest('.customer-card');
    if (!card) return;

    cardClicked = true;
    customerSavedThisTurn = false;

    nameInput.value = card.dataset.customerName || '';
    customerIdInput.value = card.dataset.customerId || '';

    toggleButtons();
    updateNameInputState();
  });

  // Inputs trigger toggle
  inputs.forEach(input => {
    input.addEventListener('input', toggleButtons);
  });

  // Reset form and hide addBtn
  addBtn.addEventListener('click', function () {
    form.reset();
    customerIdInput.value = "";
    nameInput.value = "";
    document.getElementById('emailInput').value = "";
    document.getElementById('phoneInput').value = "";
    document.getElementById('interestInput').value = "";

    cardClicked = false;
    updateNameInputState();
    toggleButtons();
  });

  // Take Customer button logic
  takeBtn.addEventListener('click', function (e) {
    e.preventDefault();

    const nameVal = nameInput.value.trim();

    $('#newCustomerBtn .spinner').removeClass('hidden');
    $('#newCustomerBtn .btn-text').text('Taking...');
    $('#newCustomerBtn').prop('disabled', true);

    $.get('/check-in-status')
      .done(res => {
        if (!res.is_checked_in) {
          Swal.fire({
            icon: 'error',
            title: 'Not checked in!',
            text: 'Please check in first.',
          });
          resetTakeButtonUI();
          return;
        }

        if (!isMyTurn) {
          Swal.fire({
            icon: 'error',
            title: 'Not your turn!',
            text: 'Please wait for your turn before taking a customer.'
          });
          resetTakeButtonUI();
          return;
        }

       

        $.ajax({
          url: '{{ route("sales.person.takeTurn") }}',
          method: 'POST',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: () => {
            Swal.fire({
              icon: 'success',
              title: 'Customer Taken!',
              text: 'You have successfully taken this customer.',
              timer: 2000,
            });

            cardClicked = false;
            customerSavedThisTurn = false;

            // Simulate assigning customerId if successful
            customerIdInput.value = Math.floor(Math.random() * 100000); // For demo purpose
            updateTurnStatus();
            updateNameInputState();
            toggleButtons();
          },
          error: () => {
            Swal.fire({
              icon: 'error',
              title: 'Error occurred!'
            });
          },
          complete: () => {
            resetTakeButtonUI();
          }
        });
      })
      .fail(() => {
        Swal.fire({
          icon: 'error',
          title: 'Check-in failed!',
          text: 'Please try again.'
        });
        resetTakeButtonUI();
      });
  });

  function resetTakeButtonUI() {
    $('#newCustomerBtn .spinner').addClass('hidden');
    $('#newCustomerBtn .btn-text').text('Take Customer');
    $('#newCustomerBtn').prop('disabled', false);
  }

  // Turn status polling
  function updateTurnStatus() {
    $.get('/next-turn-status')
      .done(res => {
        isMyTurn = res.is_your_turn;
        currentTurnUserId = res.current_turn_user_id;

        if (!res.is_checked_in) {
          $('#turn-status').text('❗ Please check in to activate your turn queue.');
        } else if (isMyTurn) {
          $('#turn-status').text(`🟢 It’s your turn now!`);
        } else {
          $('#turn-status').text('⏳ Waiting for your turn...');
        }

        wasTurnBefore = isMyTurn;
        updateNameInputState();
        toggleButtons();
      })
      .fail(() => {
        $('#turn-status').text('⚠️ Error checking turn.');
        isMyTurn = false;
        updateNameInputState();
        toggleButtons();
      });
  }

  $(document).ready(() => {
    toggleButtons();
    updateTurnStatus();
    setInterval(updateTurnStatus, 10000);
  });
</script>



<!-- form auto save -->
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
      // ❌ Remove old listeners if they exist
      if (field._autoSaveHandlerInput)
        field.removeEventListener('input', field._autoSaveHandlerInput);
      if (field._autoSaveHandlerChange)
        field.removeEventListener('change', field._autoSaveHandlerChange);

      // ✅ Create new input handler
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

      // ✅ Create new change handler
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

      // ✅ Add new listeners
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

  if (newCustomerBtn) {
    newCustomerBtn.addEventListener('click', async () => {
      const isFormDirty = !!(
        nameInput.value.trim() ||
        emailInput.value.trim() ||
        phoneInput.value.trim() ||
        interestInput.value.trim() ||
        [...form.querySelectorAll('input[name="process[]"]')].some(cb => cb.checked)
      );

      if (isFormDirty) {
        await autoSaveForm(true);
      } else {
        nameInput.value = '';
        emailInput.value = '';
        phoneInput.value = '';
        interestInput.value = '';
        [...form.querySelectorAll('input[name="process[]"]')].forEach(cb => cb.checked = false);
        await autoSaveForm(true);
      }

      // ✅ After ID is created
      if (idInput.value) {
        autosaveEnabled = true;
        attachFieldListeners(); // ✅ Re-attach listeners after ID
      }
    });
  }

async function autoSaveForm(allowWithoutId = false) {
  const hasAppointment = appointmentInput.value.trim() !== '';

  // ✅ Skip saving if not allowed and both ID and Appointment are empty
  if (!autosaveEnabled && !allowWithoutId) return;
  if (!allowWithoutId && !idInput.value.trim() && !hasAppointment) return;
  if (customerSavedThisTurn) return;

  const formData = new FormData(form);

  try {
    const response = await fetch('{{ route('customer.sales.store') }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: formData
    });

    const result = await response.json();

    if (result.status === 'success') {
      if (result.id) {
        idInput.value = result.id;
        localStorage.setItem('activeCustomerId', result.id);
      }

      customerSavedThisTurn = true;
      await loadCustomers();
    } else {
      console.error('Save failed:', result);
    }
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

    // Prevent multiple submissions
    if (appointmentCard.dataset.used === 'true') return;

    const customerId = appointmentCard.dataset.customerId;

    clearFormFields();

    idInput.value = ''; // Force new customer
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
    
    autosaveEnabled = true;            // ✅ Yeh line pehle laayi gayi hai
    attachFieldListeners();            // ✅ Yeh bhi uske sath

    await autoSaveForm();              // ✅ Yeh ab sahi tarah kaam karega

    appointmentCard.classList.add('hidden');
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
    const form = document.getElementById('salesForm');
    const appointmentCard = document.querySelector('#appointment-card');

    const nameInput = document.getElementById('nameInput');
    const phoneInput = document.getElementById('phoneInput');
    const idInput = document.getElementById('customerId');

    // 🔁 Bind click manually so every click re-applies data
    if (appointmentCard && form) {
      appointmentCard.addEventListener('click', () => {
        // Clear values first to ensure update
        nameInput.value = '';
        phoneInput.value = '';
        idInput.value = '';

        // Small delay ensures DOM update
        setTimeout(() => {
          nameInput.value = appointmentCard.dataset.name || '';
          phoneInput.value = appointmentCard.dataset.phone || '';
          idInput.value = appointmentCard.dataset.customerId || '';

          // Mark active
          document.querySelectorAll('.customer-card').forEach(card => {
            card.classList.remove('active-card');
          });
          appointmentCard.classList.add('active-card');
        }, 50); // Delay helps ensure refresh on same value
      });

      // Trigger first click automatically if needed
      appointmentCard.click();
    }
  });
</script>




  <!-- Form Show  -->
 <!-- <script>
  document.getElementById('newCustomerBtn').addEventListener('click', function () {
    document.getElementById('salesForm').classList.remove('hidden');
  });
</script> -->

  <!-- customer form -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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



  @endpush
</x-app-layout>