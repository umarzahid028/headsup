<x-app-layout>
    <x-slot name="header">
      
    </x-slot>

<style>
    #sales-table_filter{
        margin: 10px 0px !important
    }
    table.dataTable td, table.dataTable th {
        @apply px-4 py-2 text-sm text-gray-700;
    }

</style>
<div class="px-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Sales Person List</h1>
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
                    <th class="px-4 py-2 text-left text-xs font-medium text-white uppercase tracking-wider">Action</th>
                </tr>
            </thead>
        </table>
    </div>

</div>


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables CSS & JS -->
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


<script>
    $(function () {
        $('#sales-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('saleperson.table') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'action', name: 'action' },
            ]
        });
    });
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        confirmButtonColor: '#111827',
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        confirmButtonColor: '#d33',
    });
</script>
@endif

</x-app-layout>
