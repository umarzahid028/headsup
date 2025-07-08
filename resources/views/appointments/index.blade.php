<x-app-layout>
        {{-- ✅ SweetAlert2 Buttons Styling --}}
<style>
/* Common confirm button style (used for both alerts) */
.swal2-confirm.custom-ok-button {
    background-color: #111827 !important;
    color: white !important;
    border: none !important;
    box-shadow: none !important;
    padding: 10px 20px !important;
    border-radius: 5px !important;
    font-weight: bold !important;
    font-size: 14px !important;
}

/* Only for confirmation cancel button */
.swal2-cancel.custom-cancel-button {
    background-color: #111827 !important;
    color: white !important;
    padding: 10px 20px !important;
    border-radius: 5px !important;
    font-weight: bold !important;
    font-size: 14px !important;
}

/* Button spacing */
.swal2-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
}
   
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
 
/* Custom confirm button */
.swal2-confirm.custom-confirm-button {
    background-color: #111827 !important;
    color: white !important;
    padding: 10px 20px !important;
    border-radius: 5px !important;
    font-weight: bold !important;
    font-size: 14px !important;
    border: none !important;
    box-shadow: none !important;
}

/* Optional: Prevent hover color flickering */
.swal2-confirm.custom-confirm-button:hover {
    background-color: #0f172a !important;
    color: #fff !important;
    opacity: 0.95;
    transition: 0.2s ease-in-out;
}
</style>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 mb-1">
            Appointments
        </h2>
        <p class="text-gray-500 text-sm">
            Manage and view all your appointments here.
        </p>
    </x-slot>

    <div class="py-6">
        <div class="container mx-auto space-y-6 py-6 px-4">
            <div class="flex items-center justify-end mb-4 px-6">
                <a href="{{ route('appointment.create') }}" class="text-white px-3 bg-gray-800 py-1.5 rounded"
                   >
                    Add Appointments
                </a>
            </div>

            <div id="appointmentsTable">
                <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border-b px-4 py-2 text-left">Customer</th>
                                <th class="border-b px-4 py-2 text-left">Sale Person</th>
                                <th class="border-b px-4 py-2 text-left">Schedule At</th>
                                <th class="border-b px-4 py-2 text-left">Status</th>
                                <th class="border-b px-4 py-2 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $index => $appt)
                                <tr>
                                    <td class="border-b px-4 py-3">{{ $appt->customer_name }}</td>
                                    <td class="border-b px-4 py-3">{{ $appt->salesperson->name }}</td>
                                    <td class="border-b px-4 py-3">
                                        {{ \Carbon\Carbon::parse($appt->date . ' ' . $appt->time)->format('d F Y, h:i A') }}
                                    </td>
                                    <td class="border-b px-4 py-3">
                                        @php
                                            $statusClasses = [
                                                'Scheduled' => 'bg-yellow-200 text-yellow-800',
                                                'completed' => 'bg-green-200 text-green-800',
                                                'canceled' => 'bg-red-200 text-red-800',
                                            ];
                                            $statusClass = $statusClasses[$appt->status] ?? 'bg-gray-200 text-gray-800';
                                        @endphp
                                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucfirst($appt->status) }}
                                        </span>
                                    </td>
                                    <td class="border-b px-4 py-3">
                                        <div class="flex flex-wrap gap-3 items-center">
                                            @if (!in_array($appt->status, ['completed', 'canceled']))
                                                @if (Auth::user()->hasRole('Sales person') && Auth::id() == $appt->salesperson_id)
                                                   <form action="{{ route('appointment.arrive') }}" method="POST" style="display: inline;">
    @csrf
    <input type="hidden" name="appointment_id" value="{{ $appt->id }}">
    <button type="submit"
            class="bg-gray-800 text-white px-3 py-1.5 rounded check-in-required"
            >
        Customer Arrive
    </button>
</form>


                                                    <a href="{{ route('appointments.edit', ['appointment' => $appt->id]) }}"
                                                         class="bg-gray-800 text-white px-3 py-1.5 rounded"
                                                       >
                                                        Edit
                                                    </a>
                                                @endif

                                                @if (Auth::user()->hasRole(['Admin', 'Sales Manager']))
                                                    <a href="{{ route('sales.perosn', ['id' => $appt->id]) }}"
                                                       class="bg-gray-800 text-white px-3 py-1.5 rounded rounded check-in-required"
                                                       >
                                                        Customer Arrive
                                                    </a>

                                                    <a href="{{ route('appointments.edit', ['appointment' => $appt->id]) }}"
                                                       class="bg-gray-800 text-white px-3 py-1.5 rounded"
                                                      >
                                                        Edit
                                                    </a>
                                                   
                                                @endif
                                               
                                            @endif
                                               @if($appt->status !== 'canceled')
                                                @if (Auth::user()->hasRole('Sales person') && Auth::id() == $appt->salesperson_id)
                                              <a href="{{ route('appointment.view', ['appointment' => $appt->id]) }}"
                                                       class="bg-gray-800 text-white px-3 py-1.5 rounded">
                                                        View
                                                    </a>
                                                    @endif
                                                    @endif
                                                    @if($appt->status !== 'canceled')
                                                     @if (Auth::user()->hasRole(['Admin', 'Sales Manager']) )
                                                      <a href="{{ route('appointment.view', ['appointment' => $appt->id]) }}"
                                                       class="bg-gray-800 text-white px-3 py-1.5 rounded">
                                                        View
                                                    </a>
                                                    @endif
                                                    @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-gray-500 text-base italic">
                                        No appointments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                   @if ($appointments->total() >= 10)
    <div class="mt-4 px-4 mb-2">
        {{ $appointments->links() }}
    </div>
@endif

                </div>
            </div>
        </div>
    </div>

    {{-- CSRF token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @push('scripts')
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.querySelectorAll('.check-in-required').forEach(button => {
    button.addEventListener('click', async function (e) {
        e.preventDefault();

        try {
            const response = await fetch("{{ route('check.user.checkin') }}", {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.checked_in) {
                // ✅ User is checked in, proceed to route
                window.location.href = button.dataset.url;
            } else {
                // ❌ User is NOT checked in
                Swal.fire({
                    icon: 'warning',
                    title: 'Not Checked In',
                    text: 'You must check in before proceeding to this customer.',
                });
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Could not verify check-in status. Try again.',
            });
        }
    });
});
</script>


    @endpush
</x-app-layout>
