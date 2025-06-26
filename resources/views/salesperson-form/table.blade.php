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
</style>
<style>
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
        <h1 class="text-2xl font-semibold text-gray-800 px-4">Users</h1>
        <p class="text-sm text-gray-500 mt-1 px-4">
            View and manage all active sales team members below.
        </p>
    </x-slot>

    <div class="py-6">
        <div class="container mx-auto space-y-6 py-6 px-4">
            @role('Admin|Sales Manager')
            <div class="flex items-center justify-end mb-4 px-6">
                <a href="{{ route('create.saleperson') }}" class="text-white px-4 py-2 rounded"
                   style="background-color: #111827;">
                    Add User
                </a>
            </div>
            @endrole

            <div class="py-6">


                <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border-b px-4 py-2 text-left">Name</th>
                                <th class="border-b px-4 py-2 text-left">Email</th>
                                <th class="border-b px-4 py-2 text-left">Customer</th>
                                <th class="border-b px-4 py-2 text-left">User Type</th>
                                <th class="border-b px-4 py-2 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salespersons as $index => $person)
                            <tr class="border-t">
                                <td class="border-b px-4 py-3">{{ $person->name }}</td>
                                <td class="border-b px-4 py-3">{{ $person->email }}</td>
                                <td class="border-b px-4 py-3">{{ $person->customer_sales_count }}</td>
                                <td class="border-b px-4 py-3">
                                    {{ $person->roles->first()->name ?? 'No Role' }}
                                </td>

                                <td class="border-b px-4 py-3">
                                    <div class="flex gap-1">
                                        <a href="{{ route('edit.saleperson', $person->id) }}"
                                             class="text-white font-bold py-2 px-4 rounded"
                                                       style="background-color: #111827;">
                                            Edit
                                        </a>
                                        <button data-id="{{ $person->id }}"
    class="delete-user text-white font-bold py-2 px-4 rounded"
    style="background-color: #111827;">
    Delete
</button>

                                        <a href="{{ route('activity.report', ['user_id' => $person->id]) }}"
                                              class="text-white font-bold py-2 px-4 rounded"
                                                       style="background-color: #111827;">
                                            Activity
                                        </a>


                                        @if( isset($person->latestQueue) && $person->latestQueue->checked_in_at && is_null($person->latestQueue->checked_out_at))
                                        <form class="check-out-form"
                                            action="{{ route('sales.person.checkout', $person->latestQueue->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                  class="check-out-btn text-white font-bold py-2 px-4 rounded"
                                                       style="background-color: #111827;"
                                                >

                                                <span class="btn-text">Check Out</span>

                                                <svg class="btn-spinner hidden animate-spin h-5 w-5 text-white"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4" />
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 010 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endif

                                    </div>

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">No salespersons found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ✅ Flash Messages -->
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: @json(session('success')), 
        confirmButtonColor: '#111827',
        customClass: {
            confirmButton: 'custom-confirm-button'
        }
    });
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: @json(session('error')),
        confirmButtonColor: '#d33',
        customClass: {
            confirmButton: 'custom-confirm-button'
        }
    });
</script>
@endif


    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('submit', '.check-out-form', function(e) {
            e.preventDefault();

            const form = $(this);
            const btn = form.find('.check-out-btn');
            const btnText = btn.find('.btn-text');
            const spinner = btn.find('.btn-spinner');

            btn.prop('disabled', true);
            btnText.addClass('hidden');
            spinner.removeClass('hidden');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    btn.prop('disabled', false);
                    btnText.removeClass('hidden');
                    spinner.addClass('hidden');

                   Swal.fire({
    icon: 'success',
    title: 'Checked Out!',
    text: response.message || 'You have been checked out.',
    confirmButtonText: 'OK',
    confirmButtonColor: '#111827',
    customClass: {
        confirmButton: 'custom-confirm-button'
    }
});


                    // Optionally: Disable button or remove it
                    // btn.prop('disabled', true).addClass('opacity-50').text('Checked Out');
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


    {{-- ✅ Delete Confirmation --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', () => {
            const userId = button.getAttribute('data-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'custom-ok-button',
                    cancelButton: 'custom-cancel-button'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/salesperson/delete/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to delete user');
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'custom-ok-button'
                            }
                        }).then(() => location.reload());
                    })
                    .catch(error => {
                        Swal.fire('Error', error.message, 'error');
                    });
                }
            });
        });
    });
});
</script>


</x-app-layout>