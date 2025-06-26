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
        <h2 class="text-2xl font-bold text-gray-800 mb-1 px-4">
            Create Appointment
        </h2>
        <p class="text-gray-500 text-sm px-4">
            Schedule a new appointment by filling out the form below.
        </p>

    </x-slot>

    <div class="">
        <!-- Page Heading -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form id="appointment-form">
                    @csrf

                    <!-- Section: Customer Information -->
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Customer Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Customer Name</label>
                            <input name="customer_name" placeholder="Customer Name" required
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input name="customer_phone" placeholder="Phone Number" required
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                    </div>

                    <!-- Section: Appointment Schedule -->
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Appointment Schedule</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" id="date" required
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" name="time" id="time" required
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>
                    </div>

                    @if (!auth()->user()->hasRole('Sales person'))
                        <!-- Section: Salesperson -->
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Salesperson</h3>
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700">Select Salesperson</label>
                            <select name="salesperson_id"
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach ($salespersons as $person)
                                    <option value="{{ $person->id }}"
                                        {{ old('salesperson_id') == $person->id ? 'selected' : '' }}>
                                        {{ $person->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif


                    <!-- Section: Notes -->
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Additional Notes</h3>
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" placeholder="Notes"
                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div style="display: flex; justify-content:end;">
                        <button type="submit" style="background-color: #111827; margin-top: auto;"
                            class="py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Book Appointment
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
             window.onload = function () {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const today = `${year}-${month}-${day}`;

        const dateInput = document.getElementById('date');
        dateInput.setAttribute('min', today);
    };
        </script>
    @endpush
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('appointment-form');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch('/appointments', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) return response.json().then(err => Promise.reject(err));
                return response.json();
            })
          .then(data => {
  Swal.fire({
    icon: 'success',
    title: `Success!`,
    html: `${data.message}`,
    showConfirmButton: true,
});




                setTimeout(() => {
                    window.location.href = '/appointments';
                }, 2000);
            })
            .catch(error => {
                let errorMsg = "Something went wrong.";
                if (error?.errors) {
                    errorMsg = Object.values(error.errors).flat().join("<br>");
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorMsg
                });
            });
        });
    });
</script>

</x-app-layout>
