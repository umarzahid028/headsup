<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Scan Vehicle VIN') }}
            </h2>
            <x-button-link href="{{ route('vehicles.intake') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Intake
            </x-button-link>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Main Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="max-w-md mx-auto">
                        <h3 class="text-lg font-medium mb-4 text-center">Scan or enter a vehicle VIN</h3>
                        
                        <form action="{{ route('vehicles.intake.scan') }}" method="GET" class="space-y-6">
                            <div>
                                <label for="vin" class="block text-sm font-medium text-gray-700">VIN</label>
                                <div class="mt-1">
                                    <input id="vin" name="vin" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Scan or type VIN" autofocus autocomplete="off">
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Find Vehicle
                                </button>
                            </div>
                        </form>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h4 class="text-md font-medium mb-4">Instructions:</h4>
                            <ul class="text-sm text-gray-600 list-disc pl-5 space-y-2">
                                <li>Use a barcode scanner to quickly scan the vehicle's VIN.</li>
                                <li>You can also manually enter the 17-character VIN.</li>
                                <li>This feature will search both active and archived vehicles.</li>
                                <li>If the vehicle is found, you'll be taken to its details page.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 