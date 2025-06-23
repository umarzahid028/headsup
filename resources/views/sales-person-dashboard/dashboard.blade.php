<x-app-layout>
  <x-slot name="header">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div style="display:flex; justify-content: space-between;">
      <div class="px-2">
        <h1 class="text-xl font-semibold text-gray-800">Welcome, {{ Auth::user()->name }}</h1>
        <p class="text-sm text-gray-500">Manage your check-in and token activity.</p>
      </div>
      <div>
        <p id="turn-status" style="text-align:center;" class="text-sm text-gray-700 font-medium my-2 animate-pulse-text">
          Checking status...
        </p>
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
   <div class="xl:col-span-3 overflow-visible">
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
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">
        {{ ucfirst($field) }}
        @if($field === 'name')
          <span class="text-red-600">*</span>
        @endif
      </label>

      <input
        id="{{ $field === 'name' ? 'nameInput' : $field . 'Input' }}"
        name="{{ $field }}"
        type="{{ $field === 'email' ? 'email' : 'text' }}"
        class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full"
        value="{{ $sale->$field ?? '' }}"
        @if($field === 'name') required @endif
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
              <button type="submit" style="background-color: #111827;"
                class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-3 rounded-xl">
                Save
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Trigger -->
      <div class="md:col-span-2 text-right mt-4">
       <button id="openModalBtn" style="background-color: #111827;" type="button"
          class="bg-indigo-600 text-white font-semibold px-6 py-3 rounded-xl">
          Close
        </button>
<button id="toBtn" type="button" style="background-color: #111827;"
  class=" text-white font-semibold px-6 py-3 rounded-xl gap-2">
  
  <span class="btn-label">T/O</span>

  <!-- Spinner inside a button or container -->
<div id="toSpinner" class="hidden absolute inset-0 bg-black/50 flex items-center justify-center z-50 rounded-xl">
  <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
</div>

</button>
      </div>
    </form>
  </div>
</div>


    <!-- RIGHT SIDE -->
    <div class="xl:col-span-1 flex flex-col h-[calc(100vh-10rem)]">
      <div class="bg-white rounded-xl shadow p-3 w-full max-w-md mx-auto space-y-4 border mb-4">

        <!-- Status + Button -->
        <div class="flex items-center justify-between">
          <span class="status-text text-sm font-semibold px-3 py-1 rounded-full
            {{ $isCheckedIn ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">
            {{ $isCheckedIn ? '✅ Checked In' : '❌ Checked Out' }}
          </span>

          <form id="checkToggleForm" action="{{ route('sales.person.store') }}" method="POST">
            @csrf
            <button type="submit"
              id="checkToggleButton"
              class="check-toggle-btn px-6 py-2 text-sm font-semibold flex items-center gap-2 rounded-full text-white shadow-md
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
<button
  id="newCustomerBtn"
  type="button"
  disabled
  class="w-full bg-gray-400 text-white font-semibold px-6 py-2 rounded-xl mb-4">
  Take Customer
</button>

        </div>
      </div>

      <!-- Scrollable Customers -->
      <div class="flex-1 overflow-y-auto pr-2" id="customerCards">
        @include('partials.customers', ['customers' => $customers])

@if($appointment && $appointment->status !== 'completed')
  <div id="customer-list">
    <div
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

     
</div>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function toggleForm() {
      const form = document.getElementById('formContainer');
      form.classList.toggle('hidden');
    }
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
          showConfirmButton: false,
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


<!-- T/O buttons -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const toBtn   = document.getElementById('toBtn');
  const spinner = document.getElementById('toSpinner');
  const label   = toBtn.querySelector('.btn-label');
  const form    = document.getElementById('salesForm');

  async function forwardCard() {
    const id = form.querySelector('input[name="id"]').value;
    if (!id) {
      Swal.fire('Error', 'No customer selected.', 'error');
      return;
    }

    // 🔄 Show spinner & disable button
    spinner.classList.remove('hidden');
    label.classList.add('opacity-0');
    toBtn.disabled = true;

    try {
      const response = await fetch("{{ route('customer.forward') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ id })
      });

      if (!response.ok) throw new Error('Server error');
      const result = await response.json();

      // ✅ Send notification for next page
      localStorage.setItem('manager_notification', 'T/O Customer forwarded to Sales Manager.');

      Swal.fire({
        icon: 'success',
        title: 'Transferred!',
        text: result.message || 'Card moved to Sales Manager.',
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        window.location.reload(); // 🔄 refresh
      });

    } catch (error) {
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

  <!-- Turn Status -->
<script>
  let currentTurnUserId = null;

  // Enable/Disable "Take Customer" button based on name input
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

  // Check whose turn it is
  function updateTurnStatus() {
    $.get('/next-turn-status')
      .done(res => {
        const isMyTurn        = res.is_your_turn;
        const anyOneElse      = res.any_one_else;
        const newTurnUserId   = res.current_turn_user_id;
        const userName        = res.user_name || '';

        if (!anyOneElse && !isMyTurn) {
          $('#turn-status').text('');
          $('#newCustomerBtn').prop('disabled', true);
          currentTurnUserId = newTurnUserId;
          return;
        }

        if (isMyTurn) {
          $('#turn-status').text('🟢 It’s your turn now!');
          $('#newCustomerBtn').prop('disabled', false);

          if (currentTurnUserId !== newTurnUserId) {
            const msg = `It's your turn now${userName ? ', ' + userName : ''}!`;
            speechSynthesis.speak(new SpeechSynthesisUtterance(msg));

            // ✅ Reset the form
            const salesForm = document.getElementById('salesForm');
            if (salesForm) {
              salesForm.reset();
            }

            // Clear hidden ID field and local storage
            document.getElementById('customerId').value = '';
            localStorage.removeItem('activeCustomerId');

            // Reset process checkboxes
            document.querySelectorAll('input[name="process[]"]').forEach(cb => cb.checked = false);

            // Reset button styling
            const btn = document.getElementById('newCustomerBtn');
            btn.classList.remove('bg-[#111827]');
            btn.classList.add('bg-gray-400');
            btn.disabled = true;

            currentTurnUserId = newTurnUserId;
          }
        } else if (anyOneElse) {
          $('#turn-status').text('⏳ Waiting for the other salesperson to finish turn…');
          $('#newCustomerBtn').prop('disabled', true);
        } else {
          $('#turn-status').text('');
          $('#newCustomerBtn').prop('disabled', true);
        }

        currentTurnUserId = newTurnUserId;
      })
      .fail(() => {
        $('#turn-status').text('⚠️ Error checking turn status.');
        $('#newCustomerBtn').prop('disabled', true);
      });
  }

  // Handle "Take Customer" button click
  $('#newCustomerBtn').on('click', function () {
    const nameInput = $('#nameInput');
    const nameVal = nameInput.val().trim();

   if (!nameVal) {
  Swal.fire({
    icon: 'warning',
    title: 'Name field required!',
    text: 'Please fill in the customer name before proceeding.',
    showConfirmButton: false,   
    timer: 2000                  
  });
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
          title: 'Done!',
          timer: 1500,
          showConfirmButton: false
        });

        $('#nameInput').val(nameVal);
        updateTurnStatus();
      },
      error: () => {
        Swal.fire({
          icon: 'error',
          title: 'Error occurred!'
        });
      }
    });
  });

  // Start polling for turn status
  $(document).ready(() => {
    updateTurnStatus();
    setInterval(updateTurnStatus, 10000); // Every 10 seconds
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
            showConfirmButton: false
          });

          // 🔄 Page will reload after 1.5 seconds
          setTimeout(() => {
            location.reload();
          }, 1500);
        })
        .catch(error => {
          console.error(error);
          Swal.fire('Error!', 'Transfer failed. Please try again.', 'error');
        });
      });
    });
  });
</script>
  <!-- form auto save -->

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('salesForm');
  const fields = form.querySelectorAll('input, textarea');

  let debounceTimeout;

  fields.forEach(field => {
    field.addEventListener('input', () => {
      clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(() => autoSaveForm(), 700);
    });
  });

  async function autoSaveForm() {
    const nameInput = form.querySelector('input[name="name"]');
    const idInput = form.querySelector('input[name="id"]');

    if (!nameInput.value.trim()) return; // Don't save if name is empty

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
        loadCustomers();

        // ✅ Set returned ID only if not already set
        if (result.id && !idInput.value) {
          idInput.value = result.id;
          localStorage.setItem('activeCustomerId', result.id);
        }
      } else if (result.errors) {
        console.warn('Validation failed:', result.errors);
      }
    } catch (err) {
      console.error('Auto-save failed', err);
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
      applyActiveCard();
    } catch (err) {
      console.error('Failed to load customers', err);
    }
  }

  function bindCardClickEvents() {
    document.querySelectorAll('.customer-card').forEach(card => {
      card.addEventListener('click', () => {
        const customerId = card.dataset.customerId;
        form.querySelector('input[name="id"]').value = customerId || '';
        form.querySelector('input[name="name"]').value = card.dataset.name || '';
        form.querySelector('input[name="email"]').value = card.dataset.email || '';
        form.querySelector('input[name="phone"]').value = card.dataset.phone || '';
        form.querySelector('input[name="interest"]').value = card.dataset.interest || '';

        // Clear all process checkboxes first
        form.querySelectorAll('input[name="process[]"]').forEach(cb => cb.checked = false);

        // Set checked processes if present
        if (card.dataset.process) {
          card.dataset.process.split(',').forEach(proc => {
            const checkbox = Array.from(form.querySelectorAll('input[name="process[]"]'))
              .find(cb => cb.value.trim() === proc.trim());
            if (checkbox) checkbox.checked = true;
          });
        }

        // Highlight selected card
        document.querySelectorAll('.customer-card').forEach(c => c.classList.remove('active-card'));
        card.classList.add('active-card');
        localStorage.setItem('activeCustomerId', customerId);
      });
    });
  }

  function applyActiveCard() {
    const savedId = localStorage.getItem('activeCustomerId');
    const savedCard = document.querySelector(`.customer-card[data-customer-id="${savedId}"]`);

    if (savedCard) {
      document.querySelectorAll('.customer-card').forEach(c => c.classList.remove('active-card'));
      savedCard.classList.add('active-card');
    } else {
      const first = document.querySelector('.customer-card');
      if (first) {
        first.classList.add('active-card');
        localStorage.setItem('activeCustomerId', first.dataset.customerId);
      }
    }
  }

  // Initial bindings
  bindCardClickEvents();
  applyActiveCard();
});
</script>

  <!-- Form Show  -->
 <!-- <script>
  document.getElementById('newCustomerBtn').addEventListener('click', function () {
    document.getElementById('salesForm').classList.remove('hidden');
  });
</script> -->




  <!-- Form Reset -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const newCustomerBtn = document.getElementById('newCustomerBtn');
      const form = document.getElementById('salesForm');

      newCustomerBtn.addEventListener('click', function() {
        form.reset();

        form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

        // form.querySelector('input[name="user_id"]').value = "{{ auth()->id() }}";

        document.getElementById('formContainer')?.classList.remove('hidden');

        document.getElementById('customerModal')?.classList.add('hidden');
      });
    });
  </script>



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
            showConfirmButton: false,
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