<x-app-layout>
  <x-slot name="header">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <h1 class="text-xl font-semibold text-gray-800">Welcome, {{ Auth::user()->name }}</h1>
    <p class="text-sm text-gray-500">Manage your check-in and token activity.</p>
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
    <div class="xl:col-span-3">
      <!-- Form Container -->
      <div id="formContainer" class="hidden">
        <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-lg">
          <h3 class="text-2xl font-bold text-gray-800 mb-2">Customer Sales Form</h3>
          <p class="text-gray-500 mb-6">Fill out the details below to log a customer sales interaction.</p>
          <form id="salesForm" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @csrf
            <input type="hidden" name="user_id" value="{{ auth()->id() }}" />

            <!-- Customer Info -->
            <div class="space-y-4">
              @foreach (['name', 'email', 'phone', 'interest'] as $field)
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">{{ ucfirst($field) }}</label>
                <input name="{{ $field }}" type="{{ $field == 'email' ? 'email' : 'text' }}" required
                  class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full" />
              </div>
              @endforeach
            </div>

            <!-- Sales Details -->
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="6"
                  class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full"></textarea>
              </div>

              <!-- Sales Process -->
              <fieldset class="border border-gray-300 rounded-xl p-4">
                <legend class="text-sm font-semibold text-gray-700 mb-3">Sales Process</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                  @foreach(['Investigating','Test Driving','Desking','Credit Application','Penciling','F&I'] as $process)
                  <label class="flex items-center space-x-2">
                    <input type="checkbox" name="process[]" value="{{ $process }}"
                      class="form-checkbox h-5 w-5 text-indigo-600">
                    <span class="text-gray-700 text-sm">{{ $process }}</span>
                  </label>
                  @endforeach
                </div>
              </fieldset>

              <!-- Disposition -->
              <div id="customerModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
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
                        <input type="checkbox" name="disposition[]" value="{{ $disposition }}"
                          class="form-checkbox h-5 w-5 text-indigo-600">
                        <span class="text-gray-700 text-sm">{{ $disposition }}</span>
                      </label>
                      @endforeach
                    </div>
                  </fieldset>

                  <div class="text-right mt-4">
                    <button type="submit"
                      class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-3 rounded-xl">
                      Save
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="md:col-span-2 text-right mt-4">
              <button id="openModalBtn" type="button" class="bg-indigo-600 text-white font-semibold px-6 py-3 rounded-xl">
                Close
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="">
      <div class="bg-white rounded-xl shadow p-3 w-full max-w-md mx-auto space-y-4 border">

        <!-- Status + Button Side by Side -->
        <div class="flex items-center justify-between">
          <!-- Status Badge -->
          <span class="status-text text-sm font-semibold px-3 py-1 rounded-full
        {{ $isCheckedIn ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700' }}">
            {{ $isCheckedIn ? '✅ Checked In' : '❌ Checked Out' }}
          </span>

          <!-- Toggle Button Form -->
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

        <!-- Timestamps -->
        <div class="text-right space-y-1" style="text-align: left;">
          <p class="text-xs text-gray-600">
            <strong>Check In:</strong> <span id="check-in-time">{{ $checkInTimeFormatted }}</span>
          </p>
          <p class="text-xs text-gray-600">
            <strong>Check Out:</strong> <span id="check-out-time">{{ $checkOutTimeFormatted }}</span>
          </p>
          <p class="text-xs text-gray-600 {{ $isCheckedIn ? '' : 'hidden' }}" id="duration-wrapper">
            <strong>Duration:</strong> <span id="duration">Loading...</span>
          </p>
        </div>
        <button onclick="toggleForm()" style="background-color: #111827;"
          class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-xl mb-4">
          New Customer
        </button>

      </div>
      <!-- Customers -->
      @foreach ($customers as $customer)
      @php
        $firstProcess = 'N/A';
        if (is_array($customer->process) && isset($customer->process[0])) {
          $firstProcess = $customer->process[0];
        } elseif (is_string($customer->process)) {
          $firstProcess = $customer->process;
        }
      @endphp
      <div class="customer-card max-w-sm mx-auto bg-white shadow-md rounded-2xl p-6 border border-gray-200 mt-6 cursor-pointer"
        data-name="{{ $customer->name }}"
        data-email="{{ $customer->email }}"
        data-phone="{{ $customer->phone ?? '' }}"
        data-interest="{{ $customer->interest ?? '' }}"
        data-process="{{ is_array($customer->process) ? implode(',', $customer->process) : $customer->process }}"
      >
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Customer Info</h2>
        <div class="space-y-2 text-gray-500 text-sm">
          <p><span class="font-medium text-gray-400">Name:</span> {{ $customer->name }}</p>
          <p><span class="font-medium text-gray-400">Email:</span> {{ $customer->email }}</p>
          <p><span class="font-medium text-gray-400">User Name:</span> {{ $customer->user->name ?? 'Unknown' }}</p>
          <p><span class="font-medium text-gray-400">Process:</span> {{ $firstProcess }}</p>
        </div>
      </div>
      @endforeach
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

          // 🗣️ AUTO ANNOUNCE TURN
          const userName = @json(Auth::user()->name);
          const message = `${userName}, it's your turn. Please proceed.`;
          const utterance = new SpeechSynthesisUtterance(message);
          utterance.lang = 'en-US';
          speechSynthesis.speak(utterance);

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

<!-- Auto fill form -->
 <script>
    function toggleForm() {
      document.getElementById('formContainer').classList.remove('hidden');
    }

    // Autofill on card click
    document.querySelectorAll('.customer-card').forEach(card => {
      card.addEventListener('click', function () {
        const data = this.dataset;

        toggleForm();

        document.querySelector('input[name="name"]').value = data.name || '';
        document.querySelector('input[name="email"]').value = data.email || '';
        document.querySelector('input[name="phone"]').value = data.phone || '';
        document.querySelector('input[name="interest"]').value = data.interest || '';

        // Reset all process checkboxes
        document.querySelectorAll('input[name="process[]"]').forEach(cb => cb.checked = false);

        // Check the ones included in data-process
        if (data.process) {
          const processes = data.process.split(',');
          processes.forEach(proc => {
            let checkbox = [...document.querySelectorAll('input[name="process[]"]')].find(cb => cb.value.trim() === proc.trim());
            if (checkbox) checkbox.checked = true;
          });
        }
      });
    });

    // Modal close
    document.getElementById('closeModalBtn').addEventListener('click', function () {
      document.getElementById('customerModal').classList.add('hidden');
    });

    // Modal open
    document.getElementById('openModalBtn').addEventListener('click', function () {
      document.getElementById('customerModal').classList.remove('hidden');
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



    <!-- form auto save -->
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("salesForm");
        const fields = form.querySelectorAll("input, textarea");

        fields.forEach(field => {
          field.addEventListener("change", function() {
            const formData = new FormData(form);

            fetch("{{ route('customer.sales.store') }}", {
                method: "POST",
                headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
              })
              .then(response => response.json())
              .then(data => {
                console.log("Auto-saved:", data);
              })
              .catch(async error => {
                const errorText = await error.text?.() ?? error.message;
                console.error("Save failed:", errorText);
              });
          });
        });
      });
    </script>
    <!-- Form Show  -->
  <script>
  function toggleForm() {
    const formContainer = document.getElementById('formContainer');
    if (formContainer) {
      formContainer.classList.remove('hidden');
      formContainer.scrollIntoView({ behavior: 'smooth' });
    }
  }
</script>


    <script>
      window.completeToken = function(tokenId) {
        const btn = document.getElementById(`complete-btn-${tokenId}`);
        if (!btn) return;

        const btnText = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.spinner');

        btn.disabled = true;
        btnText.classList.add('hidden');
        spinner.classList.remove('hidden');

        axios.post('/tokens/' + tokenId + '/complete')
          .then(response => {
            if (response.data.status === 'success') {
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: response.data.message,
                timer: 2000,
                showConfirmButton: false,
              });

              const tokenCard = document.getElementById('token-card-' + tokenId);
              if (tokenCard) {
                tokenCard.classList.add('opacity-0', 'translate-x-4', 'duration-300');
                setTimeout(() => tokenCard.remove(), 300);
              }
            } else {
              throw new Error(response.data.message || 'Something went wrong');
            }
          })
          .catch(error => {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: error.response?.data?.message || error.message || 'Something went wrong!',
            });

            btn.disabled = false;
            btnText.classList.remove('hidden');
            spinner.classList.add('hidden');
          });
      };
    </script>

    <div id="current-token-container"></div>

    <script>
      async function fetchCurrentToken() {
        try {
          const response = await fetch('/tokens/current-assigned');
          if (!response.ok) throw new Error('Network response was not ok');

          const html = await response.text();
          document.getElementById('current-token-container').innerHTML = html;
        } catch (error) {
          console.error('Fetch error:', error);
        }
      }

      fetchCurrentToken(); // On page load
      setInterval(fetchCurrentToken, 5000); // Every 5 sec
    </script>


    <script>
      async function fetchCurrentUserToken() {
        try {
          const response = await fetch('/token/current', {
            headers: {
              'Accept': 'application/json'
            },
            cache: 'no-store'
          });

          const data = await response.json();
          console.log("Token data received:", data); // 

          if (data.token) {
            const customerName = data.token.customer_name || 'Customer'; // fallback if null
            const counterNumber = data.token.counter_number || 'unknown'; // fallback if null

            const announcement = `${customerName}, please proceed to counter number ${counterNumber}`;
            console.log("Announcement:", announcement); // 
            speak(announcement);
          } else {
            speak("You currently have no assigned tokens.");
          }
        } catch (error) {
          console.error('Error fetching current token:', error);
          speak("Error fetching your token information.");
        }
      }

      function speak(text) {
        if ('speechSynthesis' in window) {
          window.speechSynthesis.cancel();
          const utterance = new SpeechSynthesisUtterance(text);
          utterance.lang = 'en-US';
          speechSynthesis.speak(utterance);
        } else {
          alert('Speech not supported in this browser.');
        }
      }

      const announceButton = document.getElementById('announceButton');
      announceButton?.addEventListener('click', fetchCurrentUserToken);
    </script>

    <!-- skip tokken -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', async function(e) {
          // Check if clicked element or its parent has id starting with "skipButton-"
          const target = e.target.closest('button[id^="skipButton-"]');
          if (!target) return;

          e.preventDefault();

          const btn = target;
          btn.disabled = true;
          btn.querySelector('span').textContent = 'Skipping...';

          // Extract token id
          const tokenId = btn.id.split('-')[1];

          try {
            const response = await fetch(`/tokens/${tokenId}/skip`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            });

            if (!response.ok) throw new Error('Failed to skip token');

            const result = await response.json();

            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: result.message || 'Token skipped successfully.',
              timer: 2000,
              showConfirmButton: false
            });

            btn.style.display = 'none'; // Hide button after skip

          } catch (error) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: error.message || 'Something went wrong!'
            });
          } finally {
            btn.disabled = false;
            btn.querySelector('span').textContent = 'Skip Token';
          }
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


    <script>
      function assignNextToken(currentTokenId, counterNumber) {
        fetch(`/tokens/next/${currentTokenId}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success' && data.token) {
              const tokenNumber = data.token.customer_name;
              makeVoiceAnnouncement(tokenNumber, counterNumber);
            } else {
              alert('No pending token found or something went wrong.');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Failed to call next token.');
          });
      }

      function makeVoiceAnnouncement(tokenNumber, counterNumber) {
        const message = `${tokenNumber}, please proceed to counter number ${counterNumber}`;
        const utterance = new SpeechSynthesisUtterance(message);
        utterance.lang = 'en-US';
        window.speechSynthesis.speak(utterance);
      }
    </script>

    {{-- Auto-Refresh Script --}}
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        console.log("✅ JS Loaded");

        const forms = document.querySelectorAll('.auto-refresh-form');

        forms.forEach(form => {
          form.addEventListener('submit', function(e) {
            e.preventDefault(); // Stop immediate form submit

            console.log("🚀 Form Submitted - will submit via JS");

            // Actually submit using JS
            fetch(form.action, {
              method: "POST",
              body: new FormData(form),
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            }).then(response => {
              console.log("✅ Submitted, refreshing soon...");
              setTimeout(() => {
                location.reload();
              }, 1500);
            }).catch(error => {
              console.error("❌ Fetch failed:", error);
            });
          });
        });
      });
    </script>

    @endpush
</x-app-layout>