<x-app-layout>
    <x-slot name="header">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <h3 class="text-2xl font-bold text-gray-800 leading-tight mb-0 px-2">Customer Sales Form</h3>
        <p class="text-gray-500 mt-0 leading-tight px-2">Fill out the details below to log a customer sales interaction.
        </p>
    </x-slot>
    <div class="py-6">
        <div class="container mx-auto space-y-6 py-6 px-4">
          <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
      
              <!-- Customer Sales Form -->
              <div class="md:col-span-8 mx-2">
                  <form id="salesForm" method="POST" action="{{ route('customer.form.store') }}"
                      class=" grid grid-cols-1 md:grid-cols-2 gap-8 bg-white rounded-2xl border border-gray-200 p-8 shadow-lg">
                      @csrf
                      <input type="hidden" name="appointment_id" value="{{ $appointment->id ?? '' }}">
      
                      <div class="md:col-span-2">
                          <h3 class="text-2xl font-bold text-gray-800 leading-tight mb-0">T/O Customers</h3>
                          <div class="text-gray-500 mt-0 leading-tight"> Customers handed off for follow-up and closing.</div>
                      </div>
      
                      <input type="hidden" name="id" id="customerId" value="">
                      <input type="hidden" name="user_id" value="{{ auth()->id() }}" />
      
                      <!-- Customer Info -->
                      <div class="space-y-4">
                          @foreach (['name', 'email', 'phone', 'interest'] as $field)
                              <div>
                                  <label class="block text-sm font-medium text-gray-700 mb-1 capitalize">
                                      {{ ucfirst($field) }}
                                      @if (in_array($field, ['name', 'phone']))
                                          <span class="text-red-600">*</span>
                                      @endif
                                  </label>
                                  <input name="{{ $field }}" type="{{ $field == 'email' ? 'email' : 'text' }}"
                                      class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full"
                                      value="{{ $sale->$field ?? '' }}" @if (in_array($field, ['name', 'phone'])) required @endif />
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
                                  @foreach (['Investigating', 'Test Driving', 'Desking', 'Credit Application', 'Penciling', 'F&I'] as $process)
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
                          <div id="customerModal"
                              class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                              <div class="bg-white p-6 rounded-xl w-full max-w-2xl relative">
                                  <button type="button" id="closeModalBtn"
                                      class="absolute top-3 right-3 text-gray-500 hover:text-black text-xl font-bold">&times;</button>
      
                                  <fieldset class="border border-gray-300 rounded-xl p-4">
                                      <legend class="text-sm font-semibold text-gray-700 mb-3">Disposition</legend>
                                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                          @foreach (['Sold!', 'Walked Away', 'Challenged Credit', "Didn't Like Vehicle", "Didn't Like Price", "Didn't Like Finance Terms", 'Insurance Expensive', 'Wants to keep looking', 'Wants to think about it', 'Needs Co-Signer'] as $disposition)
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
      
              <!-- Customer Cards -->
              <div class="md:col-span-4">
                  <div id="customer-list">
                      @include('partials.customer-list')
                  </div>
              </div>
      
          </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                const response = await fetch("{{ route('customer.form.store') }}", {
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


    <script>
        let currentCustomerIds = [];

        // Sound
        function playNotificationSound() {
            const audio = new Audio('/sounds/notification.mp3');
            audio.play().catch(e => console.error("Audio play error:", e));
        }

        // Speech
        function speak(text) {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'en-US';
                utterance.rate = 1;
                utterance.pitch = 1;
                speechSynthesis.speak(utterance);
            }
        }

        // AJAX fetch
        function fetchCustomers() {
            $.ajax({
                url: '{{ route('customers.fetch') }}',
                method: 'GET',
                success: function(data) {
                    const newDom = $('<div>').html(data);
                    const newCards = newDom.find('.customer-card');

                    const newIds = [];
                    let newCustomerDetected = false;

                    newCards.each(function() {
                        const id = $(this).data('customer-id');
                        newIds.push(id);
                        if (!currentCustomerIds.includes(id)) {
                            newCustomerDetected = true;
                        }
                    });

                    if (newCustomerDetected) {
                        $('#customer-list').html(newDom.find('#customer-list').html());
                        // playNotificationSound();
                        speak("Manager T O Requested");
                    }

                    currentCustomerIds = newIds;
                },
                error: function() {
                    console.error("Customer fetch failed.");
                }
            });
        }

        // Initial setup
        $(document).ready(function() {
            $('.customer-card').each(function() {
                currentCustomerIds.push($(this).data('customer-id'));
            });

            // Fetch every 10 seconds
            setInterval(fetchCustomers, 10000);
        });
    </script>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.customer-card');
            const form = document.getElementById('salesForm');
            const modal = document.getElementById('customerModal');

            let activeCard = null;

            cards.forEach(card => {
                card.addEventListener('click', () => {
                    // Remove previous active animation
                    if (activeCard && activeCard !== card) {
                        activeCard.classList.remove('active-card');
                    }

                    // Add active animation to clicked card
                    card.classList.add('active-card');
                    activeCard = card;

                    // Fill form fields
                    document.getElementById('customerId').value = card.dataset.customerId || '';
                    document.querySelector('input[name="name"]').value = card.dataset.name || '';
                    document.querySelector('input[name="email"]').value = card.dataset.email || '';
                    document.querySelector('input[name="phone"]').value = card.dataset.phone || '';
                    document.querySelector('input[name="interest"]').value = card.dataset
                        .interest || '';

                    // Set process checkboxes
                    const processes = (card.dataset.process || '').split(',');
                    document.querySelectorAll('input[name="process[]"]').forEach(checkbox => {
                        checkbox.checked = processes.includes(checkbox.value);
                    });

                    // Set disposition radio button
                    const disposition = card.dataset.disposition;
                    if (disposition) {
                        document.querySelectorAll('input[name="disposition"]').forEach(radio => {
                            radio.checked = disposition === radio.value;
                        });
                    }

                    form.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                });
            });

            // Close modal
            document.getElementById('closeModalBtn').addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        });
    </script>



</x-app-layout>
