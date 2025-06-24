<x-app-layout>

    {{-- ✅ Force SweetAlert2 buttons to be visible always --}}
    <style>
        /* ✅ Force SweetAlert2 buttons to look correct and stay visible */
        .swal2-popup .swal2-actions button.swal2-confirm,
        .swal2-popup .swal2-actions button.swal2-cancel {
            display: inline-block !important;
            padding: 0.5rem 1.25rem !important;
            border-radius: 0.375rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            color: #fff !important;
            border: none !important;
            cursor: pointer !important;
            box-shadow: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            transition: background-color 0.2s ease-in-out !important;
        }

        /* ✅ Confirm button */
        .swal2-popup .swal2-actions .swal2-confirm {
            background-color: #d33 !important;
        }

        .swal2-popup .swal2-actions .swal2-confirm:hover {
            background-color: #b91c1c !important;
        }

        /* ✅ Cancel button */
        .swal2-popup .swal2-actions .swal2-cancel {
            background-color: #3085d6 !important;
        }

        .swal2-popup .swal2-actions .swal2-cancel:hover {
            background-color: #2563eb !important;
        }

        /* ✅ Button container layout */
        .swal2-popup .swal2-actions {
            display: flex !important;
            justify-content: center !important;
            gap: 12px !important;
            margin-top: 1.5rem !important;
        }
    </style>



    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-800 px-4">Users </h1>
        <p class="text-sm text-gray-500 mt-1 px-4">
            View and manage all active sales team members below.
        </p>
    </x-slot>

    <div class="px-6">
        @role('Admin|Sales Manager')
        <div class="flex items-center justify-end mb-4 px-6">
            <a href="{{ route('create.saleperson') }}" class="bg-black text-white px-4 py-2 rounded">
                Add User
            </a>
        </div>
        @endrole

        <div class="px-6 py-4">
            <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Customer</th>
                            <th class="px-4 py-2 text-left">User Type</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        @forelse($salespersons as $index => $person)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">{{ $person->name }}</td>
                            <td class="px-4 py-2">{{ $person->email }}</td>
                            <td class="px-4 py-2">{{ $person->customer_sales_count }}</td>
                            <td class="px-4 py-2">
                                {{ $person->roles->first()->name ?? 'No Role' }}
                            </td>

                            <td class="px-4 py-2">
                                <div class="flex gap-2">
                                    <a href="{{ route('edit.saleperson', $person->id) }}"
                                        class="px-4 py-2 text-xs font-medium text-white rounded hover:bg-yellow-600"
                                        style="background-color:#111827;">
                                        Edit
                                    </a>
                                    <button data-id="{{ $person->id }}"
                                        class="delete-user px-4 py-2 text-xs font-medium text-white rounded hover:bg-red-700"
                                        style="background-color:#111827;">
                                        Delete
                                    </button>
                                </div>
                                 <div class="flex items-center justify-between">
         
<form class="check-out-form" action="{{ route('sales.person.checkout', $person->id) }}" method="POST">
  @csrf
  <button type="submit"
    class="check-out-btn bg-red-500 hover:bg-red-600 px-6 py-2 text-sm font-semibold flex items-center gap-2 rounded-full text-white shadow-md">
    
    <span class="btn-text">Check Out</span>

    <svg class="btn-spinner hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
      fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor"
        d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 010 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z" />
    </svg>
  </button>
</form>




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

    <!-- ✅ SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ✅ Flash Messages -->
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('
            success ') }}',
            confirmButtonColor: '#111827',
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('
            error ') }}',
            confirmButtonColor: '#d33',
        });
    </script>
    @endif

<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $(document).on('submit', '.check-out-form', function (e) {
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
      success: function (response) {
        btn.prop('disabled', false);
        btnText.removeClass('hidden');
        spinner.addClass('hidden');

        Swal.fire({
          icon: 'success',
          title: 'Checked Out!',
          text: response.message || 'You have been checked out.',
          timer: 2000,
          showConfirmButton: false,
        });

        // Optionally: Disable button or remove it
        // btn.prop('disabled', true).addClass('opacity-50').text('Checked Out');
      },
      error: function () {
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

    <!-- ✅ Delete Confirmation JS -->
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
                            confirmButton: 'swal2-confirm',
                            cancelButton: 'swal2-cancel'
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
                                .then(response => response.json())
                                .then(data => {
                                    Swal.fire('Deleted!', data.message, 'success')
                                        .then(() => location.reload());
                                })
                                .catch(err => {
                                    Swal.fire('Error', 'Failed to delete user.', 'error');
                                });
                        }
                    });
                });
            });
        });
    </script>

</x-app-layout>