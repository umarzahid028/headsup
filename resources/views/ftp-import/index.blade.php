<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('FTP Import Settings') }}
            </h2>
            <div class="flex space-x-2">
                <form action="{{ route('ftp-import.run') }}" method="POST">
                    @csrf
                    <x-button type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Run Import Now
                    </x-button>
                </form>
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
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- FTP Settings Form -->
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium mb-4">FTP Connection Settings</h3>
                        
                        <form action="{{ route('ftp-import.settings') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="ftp_host" class="block text-sm font-medium text-gray-700">FTP Host</label>
                                    <div class="mt-1">
                                        <input type="text" name="ftp_host" id="ftp_host" value="{{ $settings['ftp_host'] }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="ftp_username" class="block text-sm font-medium text-gray-700">FTP Username</label>
                                    <div class="mt-1">
                                        <input type="text" name="ftp_username" id="ftp_username" value="{{ $settings['ftp_username'] }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="ftp_password" class="block text-sm font-medium text-gray-700">FTP Password</label>
                                    <div class="mt-1">
                                        <input type="password" name="ftp_password" id="ftp_password" placeholder="••••••••" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="ftp_directory" class="block text-sm font-medium text-gray-700">FTP Directory</label>
                                    <div class="mt-1">
                                        <input type="text" name="ftp_directory" id="ftp_directory" value="{{ $settings['ftp_directory'] }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label for="file_pattern" class="block text-sm font-medium text-gray-700">File Pattern</label>
                                    <div class="mt-1">
                                        <input type="text" name="file_pattern" id="file_pattern" value="{{ $settings['file_pattern'] }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <p class="mt-1 text-xs text-gray-500">Example: *.csv, export-*.xml</p>
                                    </div>
                                </div>

                                <div class="sm:col-span-6">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="auto_import" name="auto_import" type="checkbox" {{ $settings['auto_import'] ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="auto_import" class="font-medium text-gray-700">Enable automatic imports</label>
                                            <p class="text-gray-500">Automatically import vehicles on a schedule</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="sm:col-span-3" id="frequency-container" style="{{ $settings['auto_import'] ? '' : 'display: none;' }}">
                                    <label for="auto_import_frequency" class="block text-sm font-medium text-gray-700">Import Frequency</label>
                                    <select id="auto_import_frequency" name="auto_import_frequency" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="hourly" {{ $settings['auto_import_frequency'] === 'hourly' ? 'selected' : '' }}>Hourly</option>
                                        <option value="daily" {{ $settings['auto_import_frequency'] === 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ $settings['auto_import_frequency'] === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    </select>
                                </div>
                            </div>

                            <div class="pt-5">
                                <div class="flex justify-end">
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Save Settings
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recent Import Logs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium mb-4">Recent Import Logs</h3>
                        
                        <div class="h-64 overflow-y-auto bg-gray-50 rounded p-3 text-xs font-mono">
                            @if(count($logs) > 0)
                                @foreach($logs as $log)
                                    <div class="mb-1 {{ strpos($log, 'ERROR') !== false ? 'text-red-600' : 'text-gray-700' }}">
                                        {{ $log }}
                                    </div>
                                @endforeach
                            @else
                                <div class="text-gray-500">No import logs available.</div>
                            @endif
                        </div>
                        
                        <div class="mt-4">
                            <h4 class="text-md font-medium mb-2">Instructions</h4>
                            <ul class="text-sm text-gray-600 list-disc pl-5 space-y-1">
                                <li>Configure the FTP settings to connect to your data provider.</li>
                                <li>Use "Run Import Now" to manually import vehicle data.</li>
                                <li>Enable automatic imports to keep your inventory up to date.</li>
                                <li>Imported vehicles will appear in the Intake & Dispatch screen.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const autoImportCheckbox = document.getElementById('auto_import');
            const frequencyContainer = document.getElementById('frequency-container');
            
            autoImportCheckbox.addEventListener('change', function() {
                frequencyContainer.style.display = this.checked ? 'block' : 'none';
            });
        });
    </script>
</x-app-layout> 