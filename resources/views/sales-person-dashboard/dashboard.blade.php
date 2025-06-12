<x-app-layout>
  <x-slot name="header">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
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
<div class="w-full grid grid-cols-1 xl:grid-cols-4 gap-6 px-4">
  <!-- LEFT SIDE: 3/4 i.e. 75% -->
  <div class="xl:col-span-3">
    <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-lg">
      <h3 class="text-2xl font-bold text-gray-800 mb-2">Customer Sales Form</h3>
      <p class="text-gray-500 mb-6">Fill out the details below to log a customer sales interaction.</p>

      <form id="salesForm" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @csrf
        <!-- Customer Info -->
        <div class="space-y-4">
          <h4 class="text-lg font-semibold text-gray-700 mb-2">Customer Information</h4>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input name="name" required class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input name="email" type="email" required class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input name="phone" required class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Interest in Car</label>
            <input name="interest" class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="4" class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full"></textarea>
          </div>
        </div>

        <!-- Sales Details -->
        <div class="space-y-4">
          <h4 class="text-lg font-semibold text-gray-700 mb-2">Sales Details</h4>

          <fieldset class="border border-gray-300 rounded-xl p-4">
            <legend class="text-sm font-semibold text-gray-700 mb-3">Sales Process</legend>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
              @foreach(['Investigating','Test Driving','Desking','Credit Application','Penciling','F&I'] as $process)
              <label class="flex items-center space-x-2">
                <input type="checkbox" name="process[]" value="{{ $process }}" class="form-checkbox h-5 w-5 text-indigo-600">
                <span class="text-gray-700 text-sm">{{ $process }}</span>
              </label>
              @endforeach
            </div>
          </fieldset>

          <fieldset class="border border-gray-300 rounded-xl p-4">
            <legend class="text-sm font-semibold text-gray-700 mb-3">Disposition</legend>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              @foreach([
              'Sold!', 'Walked Away', 'Challenged Credit', "Didn't Like Vehicle",
              "Didn't Like Price", "Didn't Like Finance Terms", 'Insurance Expensive',
              'Wants to keep looking', 'Wants to think about it', 'Needs Co-Signer'
              ] as $disposition)
              <label class="flex items-center space-x-2">
                <input type="checkbox" name="disposition[]" value="{{ $disposition }}" class="form-checkbox h-5 w-5 text-indigo-600">
                <span class="text-gray-700 text-sm">{{ $disposition }}</span>
              </label>
              @endforeach
            </div>
          </fieldset>
        </div>

        <div class="md:col-span-2 text-right mt-6">
          <button type="submit" style="background-color: #111827;"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-xl transition duration-200">
            Submit
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- RIGHT SIDE: 1/4 i.e. 25% -->
  <div class="xl:col-span-1">
<div class="flex justify-between items-center  py-2">
  <!-- Left: Counter Text -->
  <h2 class="text-lg font-semibold text-gray-800">Counter Number : {{ Auth::user()->counter_number }}</h2>

  <!-- Right: Speaker Button -->
  <button id="announceButton" type="button" 
    class="text-white w-[40px] h-[40px] flex items-center justify-center p-4"
    style="background-color: #1f2937; border-radius: 70px;">
    <!-- Speaker Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2"
      viewBox="0 0 24 24" width="20" height="20">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M11 5L6 9H2v6h4l5 4V5zm7.5 7a3.5 3.5 0 00-2.1-3.2m0 6.4a3.5 3.5 0 002.1-3.2m2.5 0a6 6 0 00-3.6-5.5m0 11a6 6 0 003.6-5.5" />
    </svg>
  </button>
</div>




      <form id="toggleForm" action="{{ route('sales.perosn.store') }}" method="POST">
        @csrf
        @php
        $isCheckedIn = Auth::user()->latestQueue && Auth::user()->latestQueue->is_checked_in;
        @endphp
      <div class=" mb-2">
        <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl p-4 mb-4">
  <div class="flex items-center gap-2">
    <span class="text-sm font-medium text-gray-700">Status:</span>
    <span class="status-text text-sm font-semibold rounded-full px-3 py-1
      {{ $isCheckedIn ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-700' }}">
      {{ $isCheckedIn ? '✅ Checked In' : '❌ Checked Out' }}
    </span>
  </div>
</div>

<p class="text-sm text-gray-500 mb-4">Manage your check-in and tokens here.</p>


        

          <button id="toggleButton" type="submit" 
            class="px-5 py-2.5 mb-3 text-sm font-semibold w-full flex justify-center items-center rounded-xl text-white transition-all duration-200 flex items-center gap-2 rounded-[60px]
              {{ $isCheckedIn ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
            <span class="btn-text">{{ $isCheckedIn ? 'Check Out' : 'Check In' }}</span>
            <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
              fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 010 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z" />
            </svg>
          </button>
          
      </form>
<div >
   <button
          onclick="assignNextToken({{ optional($token)->id ?? 0 }}, {{ optional(optional($token)->salesperson)->counter_number ?? 1 }})"
          style="background-color: #1f2937;"
          class="text-white px-2 py-2 flex items-center gap-2 rounded-xl w-full flex justify-center items-center">
          <!-- Next Token Icon -->
          Next Token Call
        </button>
</div>
      <div id="current-token-container" class="w-full mt-5">
        @include('partials.current-token', ['token' => $token])
      </div>
    </div>
  </div>
</div>



  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    $('#toggleForm').on('submit', function(e) {
      e.preventDefault();

      const btn = $('#toggleButton');
      const btnText = btn.find('.btn-text');
      const spinner = $('#btnSpinner');

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
            btnText.text('Check Out');
            btn.removeClass('bg-green-500 hover:bg-green-600').addClass('bg-red-500 hover:bg-red-600');

            $('.status-text').text('✅ Checked In')
              .removeClass('bg-red-100 text-red-700')
              .addClass('bg-emerald-100 text-emerald-800');
          } else {
            btnText.text('Check In');
            btn.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-green-500 hover:bg-green-600');

            $('.status-text').text('❌ Checked Out')
              .removeClass('bg-emerald-100 text-emerald-800')
              .addClass('bg-red-100 text-red-700');
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
            text: 'Something went wrong!',
          });
        }
      });
    });
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

        if (data.token) {
          const tokenNumber = String(data.token.serial_number).padStart(3, '0');
          const counterNumber = data.token.counter_number;

          const announcement = `Token number ${tokenNumber}, please proceed to counter number ${counterNumber}`;
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
        window.speechSynthesis.cancel(); // cancel previous
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-US';
        speechSynthesis.speak(utterance);
      } else {
        alert('Speech not supported in this browser.');
      }
    }

    const announceButton = document.getElementById('announceButton');
    announceButton.addEventListener('click', fetchCurrentUserToken);
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
      e.preventDefault(); // Stop default form submission

      const form = e.target;
      const formData = new FormData(form);

      // 🔄 Show processing alert
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
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: result.message || 'Form submitted successfully',
            timer: 3000,
            showConfirmButton: false
          });

          form.reset(); // Optionally reset form
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
            const tokenNumber = data.token.serial_number;
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
      const message = `Token number ${tokenNumber}, please proceed to counter number ${counterNumber}`;
      const utterance = new SpeechSynthesisUtterance(message);
      utterance.lang = 'en-US';
      window.speechSynthesis.speak(utterance);
    }
  </script>

  @endpush
</x-app-layout>