<x-app-layout>
  <x-slot name="header">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div style="display:flex; justify-content: space-between;">
      <div>
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
        <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-lg">
          <h3 class="text-2xl font-bold text-gray-800 mb-2">Customer Sales Form</h3>
          <p class="text-gray-500 mb-6">Fill out the details below to log a customer sales interaction.</p>

          <form id="salesForm" method="POST" action="{{ route('customer.sales.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @csrf
            <input type="hidden" name="id" id="customerId" value="">
            <input type="hidden" name="user_id" value="{{ auth()->id() }}" />

            <!-- Customer Info -->
            <div class="space-y-4">
              @foreach (['name', 'email', 'phone', 'interest'] as $field)
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">
                  {{ ucfirst($field) }}
                  @if(in_array($field, ['name', 'email', 'phone']))
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
            </div>
          </form>
        </div>
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
          <button id="newCustomerBtn" type="button" class="w-full bg-[#111827] text-white font-semibold px-6 py-2 rounded-xl mb-4">Take Customer</button>
        </div>
      </div>

      <!-- Scrollable Customers -->
      <div class="flex-1 overflow-y-auto pr-2" id="customerCards">
        @include('partials.customers', ['customers' => $customers])
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
            // Checked In UI
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

            // 🔊 AUTO ANNOUNCE & ANIMATION
            const userName = @json(Auth::user() -> name);
            const message = `${userName}, it's your turn. Please proceed.`;

            // Voice
            const utterance = new SpeechSynthesisUtterance(message);
            utterance.lang = 'en-US';
            speechSynthesis.speak(utterance);

            // Visual animation
            $('#turn-user-name').text(message);
            $('#turn-announcement').removeClass('hidden');

            setTimeout(() => {
              $('#turn-announcement').addClass('hidden');
            }, 5000);

          } else {
            // Checked Out UI
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

  <!-- Every Customer live time -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const TIMER_KEY = 'customer_timer_start';
      let intervalId = null;

      function formatDuration(seconds) {
        const hrs = String(Math.floor(seconds / 3600)).padStart(2, '0');
        const mins = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
        const secs = String(seconds % 60).padStart(2, '0');
        return `${hrs}:${mins}:${secs}`;
      }

      function updateTimers() {
        const start = localStorage.getItem(TIMER_KEY);
        if (!start) return;

        const startTime = new Date(parseInt(start));
        const now = new Date();
        const diff = Math.floor((now - startTime) / 1000);
        const formatted = formatDuration(diff);

        document.querySelectorAll('.live-duration').forEach(el => {
          el.textContent = formatted;
        });
      }

      function startTimer() {
        // Always reset the timer on new customer
        localStorage.setItem(TIMER_KEY, Date.now());

        if (intervalId) clearInterval(intervalId);
        intervalId = setInterval(updateTimers, 1000);
        updateTimers(); // initial update
      }

      function stopTimer() {
        if (intervalId) {
          clearInterval(intervalId);
          intervalId = null;
        }

        localStorage.removeItem(TIMER_KEY);
        document.querySelectorAll('.live-duration').forEach(el => {
          el.textContent += ' (Ended)';
        });
      }

      // Bind buttons
      const startBtn = document.getElementById('newCustomerBtn');
      const stopBtn = document.getElementById('openModalBtn');

      if (startBtn) {
        startBtn.addEventListener('click', startTimer);
      }

      if (stopBtn) {
        stopBtn.addEventListener('click', stopTimer);
      }

      // Optional: resume timer on page reload (if needed)
      if (localStorage.getItem(TIMER_KEY)) {
        startTimer();
      }
    });
  </script>

  <!-- Turn Status -->
  <script>
    function updateTurnStatus() {
      console.log("📡 Checking turn status...");

      $.get('/next-turn-status')
        .done(function(res) {
          console.log("✅ Turn status response:", res);

          if (res.is_your_turn) {
            $('#turn-status').text('🟢 It’s your turn now!');
            $('#newCustomerBtn').prop('disabled', false);
          } else {
            const waitText = res.others_pending > 0 ?
              '⏳ Waiting for the other salesperson to finish turn...' :
              '— Check into queue to participate —';
            $('#turn-status').text(waitText);
            $('#newCustomerBtn').prop('disabled', true);
          }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
          console.error("🔥 Turn check failed:", jqXHR.responseText);
          $('#turn-status').text('⚠️ Error checking turn status.');
          $('#newCustomerBtn').prop('disabled', true);
        });
    }

    $('#newCustomerBtn').on('click', function() {
      $.ajax({
        url: '{{ route("sales.person.takeTurn") }}',
        method: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          const msg = 'Your turn is complete.';
          const utter = new SpeechSynthesisUtterance(msg);
          utter.lang = 'en-US';


          Swal.fire({
            icon: 'success',
            title: 'Done!',
            text: msg,
            timer: 2000,
            showConfirmButton: false
          });

          updateTurnStatus();
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong.'
          });
        }
      });
    });

    $(document).ready(function() {
      updateTurnStatus();
      setInterval(updateTurnStatus, 10000);
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    const salespeople = @json($salespeople);
    const currentUserId = {
      {
        auth() - > id()
      }
    };

    document.querySelectorAll('.transfer-btn').forEach(button => {
      button.addEventListener('click', function() {
        const customerId = this.getAttribute('data-customer-id');
        const customerName = this.getAttribute('data-customer-name');
        const customerCard = this.closest('.customer-card');

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
                        You are about to transfer <span class="font-semibold text-indigo-600">${customerName}</span> to another sales person.
                    </div>
                    <div class="text-left w-full">
                        <label class="block text-sm font-medium mb-1 text-[#111827]">Select Sales Person:</label>
                        <div style="overflow-x: hidden;">
                            <select id="salespersonSelect"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-[#111827] text-[#111827]">
                                ${options}
                            </select>
                        </div>
                    </div>
                `,
          confirmButtonText: 'Confirm Transfer',
          cancelButtonText: 'Cancel',
          showCancelButton: true,
          buttonsStyling: false,
          customClass: {
            popup: 'rounded-2xl p-6 shadow-xl',
            confirmButton: 'bg-[#111827] hover:bg-[#0f172a] text-white px-5 py-2 mt-4 rounded-lg font-semibold',
            cancelButton: 'mx-3 bg-[#111827] hover:bg-[#0f172a] text-white px-5 py-2 mt-4 rounded-lg font-semibold',
          },
          preConfirm: () => {
            const selectedId = document.getElementById('salespersonSelect').value;
            if (!selectedId) {
              Swal.showValidationMessage('Please select a sales person first.');
            }
            return selectedId;
          }
        }).then((result) => {
          if (result.isConfirmed) {
            const newSalesId = result.value;

            fetch(`/customers/${customerId}/transfer`, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                  new_user_id: newSalesId
                })
              })
              .then(res => res.json())
              .then(data => {
                Swal.fire({
                  icon: 'success',
                  title: 'Customer Transferred',
                  text: data.message,
                  timer: 1500,
                  showConfirmButton: false
                });

                // ✅ Remove card if not current user
                if (newSalesId != currentUserId) {
                  customerCard.remove();
                }

                // ✅ Clear the form
                const salesForm = document.getElementById('salesForm');
                if (salesForm) {
                  salesForm.reset();

                  // Optional success message
                  const msg = document.createElement('p');
                  msg.innerText = "Form cleared!";
                  msg.className = "text-green-600 text-sm mt-2";
                  salesForm.appendChild(msg);
                  setTimeout(() => msg.remove(), 3000);
                }

                // ✅ Close modal if open
                const modal = document.getElementById('customerModal');
                if (modal) {
                  modal.classList.add('hidden');
                }
              })
              .catch(err => {
                Swal.fire('Error!', 'Transfer failed. Try again.', 'error');
              });
          }
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
            }
        } catch (err) {
            console.error('Auto-save failed', err);
        }
    }

    async function loadCustomers() {
        const resp = await fetch('{{ route('customer.index') }}?partial=1', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const html = await resp.text();
        document.getElementById('customer-list').innerHTML = html;
        bindCardClickEvents();
    }

    function bindCardClickEvents() {
        document.querySelectorAll('.customer-card').forEach(card => {
            card.addEventListener('click', () => {
                form.querySelector('input[name="id"]').value = card.dataset.customerId || '';
                form.querySelector('input[name="name"]').value = card.dataset.name || '';
                form.querySelector('input[name="email"]').value = card.dataset.email || '';
                form.querySelector('input[name="phone"]').value = card.dataset.phone || '';
                form.querySelector('input[name="interest"]').value = card.dataset.interest || '';

                form.querySelectorAll('input[name="process[]"]').forEach(cb => cb.checked = false);
                if (card.dataset.process) {
                    card.dataset.process.split(',').forEach(proc => {
                        const checkbox = [...form.querySelectorAll('input[name="process[]"]')]
                          .find(cb => cb.value.trim() === proc.trim());
                        if (checkbox) checkbox.checked = true;
                    });
                }
            });
        });
    }

    bindCardClickEvents();
});
</script>

  <!-- Form Show  -->
  <script>
    function toggleForm() {
      const formContainer = document.getElementById('formContainer');
      if (formContainer) {
        formContainer.classList.remove('hidden');
        formContainer.scrollIntoView({
          behavior: 'smooth'
        });
      }
    }
  </script>

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
              window.location.href = result.redirect || "{{ route('sales.perosn') }}";
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