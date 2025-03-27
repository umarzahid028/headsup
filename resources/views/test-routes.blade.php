<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Route Test Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg font-semibold mb-4">Available Routes</h2>
                    
                    <div class="mb-8">
                        <h3 class="text-md font-medium mb-2">Main Navigation</h3>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>
                                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a>
                            </li>
                            <li>
                                <a href="{{ route('vehicles.index') }}" class="text-blue-600 hover:underline">Vehicles</a>
                            </li>
                            <li>
                                <a href="{{ route('tasks.index') }}" class="text-blue-600 hover:underline">Tasks</a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mb-8">
                        <h3 class="text-md font-medium mb-2">Vehicle Management</h3>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>
                                <a href="{{ route('vehicles.intake') }}" class="text-blue-600 hover:underline">Intake & Dispatch</a>
                            </li>
                            <li>
                                <a href="{{ route('we-owe.index') }}" class="text-blue-600 hover:underline">We-Owe & Goodwill</a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mb-8">
                        <h3 class="text-md font-medium mb-2">Admin</h3>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>
                                <a href="{{ route('vendors.index') }}" class="text-blue-600 hover:underline">Vendors</a>
                            </li>
                            <li>
                                <a href="{{ route('transporters.index') }}" class="text-blue-600 hover:underline">Transporters</a>
                            </li>
                            <li>
                                <a href="{{ route('ftp-import.index') }}" class="text-blue-600 hover:underline">FTP Import</a>
                            </li>
                            <li>
                                <a href="{{ route('reports.index') }}" class="text-blue-600 hover:underline">Reports</a>
                            </li>
                            <li>
                                <a href="{{ route('settings.index') }}" class="text-blue-600 hover:underline">Settings</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 