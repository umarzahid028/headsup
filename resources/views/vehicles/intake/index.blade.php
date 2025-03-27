<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vehicle Intake & Dispatch') }}
            </h2>
            <div class="flex space-x-2">
                <x-button-link href="{{ route('vehicles.intake.scan') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    Scan VIN
                </x-button-link>
                <x-button data-modal-target="importModal" data-modal-toggle="importModal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Import from FTP
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Main Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Vehicles Pending Intake & Dispatch</h3>
                    
                    @if($newVehicles->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Vehicle
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            VIN
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Purchase Info
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Transport Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Documents
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($newVehicles as $vehicle)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                                    </a>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $vehicle->stock_number }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $vehicle->vin }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($vehicle->purchased_from)
                                                    <div class="text-sm text-gray-900">
                                                        From: {{ optional($vehicle->purchasedFromVendor)->name ?? 'Unknown' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $vehicle->purchase_location ?? '' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        ${{ number_format($vehicle->purchase_price, 2) ?? '0.00' }}
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Needs Info
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($vehicle->transporter_id)
                                                    <div class="text-sm text-gray-900">
                                                        {{ optional($vehicle->transporter)->name ?? 'Unknown Transporter' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        Assigned: {{ $vehicle->transport_assigned_at ? $vehicle->transport_assigned_at->format('M d, Y') : 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        Expected: {{ $vehicle->transport_expected_at ? $vehicle->transport_expected_at->format('M d, Y') : 'N/A' }}
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        No Transporter
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex space-x-2">
                                                    @if($vehicle->releaseForms()->count() > 0)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ $vehicle->releaseForms()->count() }} Release Form(s)
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            No Release Forms
                                                        </span>
                                                    @endif
                                                    
                                                    @if($vehicle->gatePasses()->count() > 0)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ $vehicle->gatePasses()->count() }} Gate Pass(es)
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            No Gate Passes
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button data-modal-target="assignTransporterModal-{{ $vehicle->id }}" data-modal-toggle="assignTransporterModal-{{ $vehicle->id }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Assign Transporter
                                                    </button>
                                                    <button data-modal-target="uploadDocModal-{{ $vehicle->id }}" data-modal-toggle="uploadDocModal-{{ $vehicle->id }}" class="text-blue-600 hover:text-blue-900">
                                                        Upload Doc
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Assign Transporter Modal -->
                                        <div id="assignTransporterModal-{{ $vehicle->id }}" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                            <div class="relative w-full max-w-2xl max-h-full">
                                                <div class="relative bg-white rounded-lg shadow">
                                                    <div class="flex items-center justify-between p-4 border-b rounded-t">
                                                        <h3 class="text-xl font-medium text-gray-900">
                                                            Assign Transporter for {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                                        </h3>
                                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="assignTransporterModal-{{ $vehicle->id }}">
                                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                            </svg>
                                                            <span class="sr-only">Close modal</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('vehicles.intake.assign-transporter', $vehicle) }}" method="POST">
                                                        @csrf
                                                        <div class="p-6 space-y-6">
                                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                                <div>
                                                                    <label for="transporter_id" class="block text-sm font-medium text-gray-700">Transporter</label>
                                                                    <select id="transporter_id" name="transporter_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                                        <option value="">Select Transporter</option>
                                                                        @foreach($transporters as $transporter)
                                                                            <option value="{{ $transporter->id }}">{{ $transporter->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                
                                                                <div>
                                                                    <label for="transport_expected_at" class="block text-sm font-medium text-gray-700">Expected Arrival Date</label>
                                                                    <input type="date" id="transport_expected_at" name="transport_expected_at" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label for="purchased_from" class="block text-sm font-medium text-gray-700">Purchased From</label>
                                                                    <select id="purchased_from" name="purchased_from" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                                        <option value="">Select Vendor</option>
                                                                        @foreach($purchaseLocations as $location)
                                                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                
                                                                <div>
                                                                    <label for="purchase_location" class="block text-sm font-medium text-gray-700">Purchase Location</label>
                                                                    <input type="text" id="purchase_location" name="purchase_location" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label for="purchase_price" class="block text-sm font-medium text-gray-700">Purchase Price</label>
                                                                    <div class="mt-1 relative rounded-md shadow-sm">
                                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                            <span class="text-gray-500 sm:text-sm">$</span>
                                                                        </div>
                                                                        <input type="number" step="0.01" id="purchase_price" name="purchase_price" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div>
                                                                    <label for="purchase_date" class="block text-sm font-medium text-gray-700">Purchase Date</label>
                                                                    <input type="date" id="purchase_date" name="purchase_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                                </div>
                                                                
                                                                <div class="flex items-center h-5 mt-5">
                                                                    <input id="is_arbitrable" name="is_arbitrable" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                                    <label for="is_arbitrable" class="ml-2 block text-sm text-gray-900">
                                                                        Vehicle is arbitrable
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b">
                                                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Assign Transporter</button>
                                                            <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10" data-modal-hide="assignTransporterModal-{{ $vehicle->id }}">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Upload Document Modal -->
                                        <div id="uploadDocModal-{{ $vehicle->id }}" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                            <div class="relative w-full max-w-md max-h-full">
                                                <div class="relative bg-white rounded-lg shadow">
                                                    <div class="flex items-center justify-between p-4 border-b rounded-t">
                                                        <h3 class="text-xl font-medium text-gray-900">
                                                            Upload Document for {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                                        </h3>
                                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="uploadDocModal-{{ $vehicle->id }}">
                                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                            </svg>
                                                            <span class="sr-only">Close modal</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('vehicles.intake.upload-document', $vehicle) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="p-6 space-y-6">
                                                            <div class="space-y-4">
                                                                <div>
                                                                    <label for="name" class="block text-sm font-medium text-gray-700">Document Name</label>
                                                                    <input type="text" id="name" name="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                                </div>
                                                                
                                                                <div>
                                                                    <label for="type" class="block text-sm font-medium text-gray-700">Document Type</label>
                                                                    <select id="type" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                                        <option value="release_form">Release Form</option>
                                                                        <option value="gate_pass">Gate Pass</option>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div>
                                                                    <label for="document" class="block text-sm font-medium text-gray-700">File</label>
                                                                    <input type="file" id="document" name="document" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b">
                                                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Upload Document</button>
                                                            <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10" data-modal-hide="uploadDocModal-{{ $vehicle->id }}">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $newVehicles->links() }}
                        </div>
                    @else
                        <div class="bg-gray-50 p-6 text-center">
                            <p class="text-gray-500">No vehicles pending intake or dispatch.</p>
                            <p class="text-gray-500 mt-2">Import vehicles from FTP or create a new vehicle to get started.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Import Modal -->
    <div id="importModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-xl font-medium text-gray-900">
                        Import Vehicles from FTP
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="importModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form action="{{ route('vehicles.intake.import-ftp') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-6">
                        <p class="text-gray-700 mb-4">
                            This will import new vehicle data from the configured FTP server.
                        </p>
                        <p class="text-sm text-gray-500">
                            Make sure your FTP settings are configured properly in the Admin settings.
                        </p>
                        <div class="flex items-center space-x-2">
                            <input id="notify_sales_managers" name="notify_sales_managers" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="notify_sales_managers" class="text-sm text-gray-700">
                                Notify sales managers about new vehicles
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Start Import</button>
                        <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10" data-modal-hide="importModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 