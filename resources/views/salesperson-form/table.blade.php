<x-app-layout>
    <x-slot name="header">
        <!-- Header Optional -->
        <h1 class="text-2xl font-semibold text-gray-800 px-4">Sales Person List</h1>
        <p class="text-sm text-gray-500 mt-1 px-4">
            View and manage all active sales team members below.
        </p>

    </x-slot>

    <style>
        #sales-table_filter {
            margin: 10px 0px !important;
        }

        table.dataTable td,
        table.dataTable th {
            @apply px-4 py-2 text-sm text-gray-700;
        }
    </style>

    <div class="px-6">
        <div class="flex items-center justify-end mb-4 px-6">

            <a href="{{ route('create.saleperson') }}" class="bg-black text-white px-4 py-2 rounded">
                Add Sales Person
            </a>
        </div>

        <div class="px-6 py-4">
            <table id="sales-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Counter</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery + DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        // Laravel expects this header for AJAX
        $.ajaxSetup({
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        $(function() {
            $('#sales-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('saleperson.table') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'counter_number',
                        name: 'counter_number'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                error: function(xhr, error, thrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'DataTable Error',
                        text: 'Ajax request failed. Check console/network tab for details.',
                        confirmButtonColor: '#d33',
                    });
                }
            });
        });
    </script>

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
        $(document).on('click', '.delete-user', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/salesperson/delete/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            ).then(() => {
                                location.reload(); // Optionally reload after alert
                            });
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                xhr.responseText,
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>


</x-app-layout>