@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6">
    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="space-y-1">
            <h2 class="text-2xl font-semibold tracking-tight">System Settings</h2>
            <p class="text-sm text-muted-foreground">View and manage system configuration.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-4 sm:px-6 lg:px-8">
        <!-- CSV Import Settings Card -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm md:col-span-2">
            <div class="p-6 space-y-6">
                <div class="flex items-center gap-4">
                    <div class="p-2 rounded-lg bg-primary/10">
                        <x-heroicon-o-document-arrow-up class="w-6 h-6 text-primary" />
                    </div>
                    <div>
                        <h3 class="font-semibold">CSV Import Settings</h3>
                        <p class="text-sm text-muted-foreground">Configure automatic CSV file monitoring for vehicle imports</p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update-csv-settings') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <!-- Monitor Directory Settings -->
                    <div class="space-y-4">
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <label for="csv_monitor_path" class="text-sm font-medium leading-none">
                                    Monitor Directory Path
                                </label>
                                <div class="relative">
                                    <input type="text" id="csv_monitor_path" name="csv_monitor_path" 
                                        value="{{ old('csv_monitor_path', $settings['csv_monitor_path'] ?? '') }}"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        placeholder="/path/to/csv/directory">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-folder class="h-4 w-4 text-gray-400" />
                                    </div>
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    Specify the directory path where the system will monitor for new CSV files.
                                </p>
                                @error('csv_monitor_path')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid gap-2">
                                <label class="text-sm font-medium leading-none">Import Options</label>
                                <div class="space-y-3">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="auto_process_files" 
                                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-2 focus:ring-primary"
                                            {{ old('auto_process_files', $settings['auto_process_files'] ?? false) ? 'checked' : '' }}>
                                        <span class="text-sm">Automatically process new files when detected</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="archive_processed_files"
                                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-2 focus:ring-primary"
                                            {{ old('archive_processed_files', $settings['archive_processed_files'] ?? false) ? 'checked' : '' }}>
                                        <span class="text-sm">Archive files after processing</span>
                                    </label>
                                </div>
                            </div>

                            <div class="grid gap-2">
                                <label for="file_pattern" class="text-sm font-medium leading-none">
                                    File Pattern
                                </label>
                                <div class="relative">
                                    <input type="text" id="file_pattern" name="file_pattern"
                                        value="{{ old('file_pattern', $settings['file_pattern'] ?? '*.csv') }}"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                        placeholder="*.csv">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-document-text class="h-4 w-4 text-gray-400" />
                                    </div>
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    Specify the file pattern to monitor (e.g., *.csv, vehicles_*.csv)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Roles & Permissions Card -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="p-2 rounded-lg bg-primary/10">
                        <x-heroicon-o-shield-check class="w-6 h-6 text-primary" />
                    </div>
                    <div>
                        <h3 class="font-semibold">Roles & Permissions</h3>
                        <p class="text-sm text-muted-foreground">Manage user roles and access control</p>
                    </div>
                </div>
                <div class="pt-4">
                    <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                        View Roles
                        <x-heroicon-o-arrow-right class="ml-2 h-4 w-4" />
                    </a>
                </div>
            </div>
        </div>

        <!-- User Management Card -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="p-2 rounded-lg bg-primary/10">
                        <x-heroicon-o-users class="w-6 h-6 text-primary" />
                    </div>
                    <div>
                        <h3 class="font-semibold">User Management</h3>
                        <p class="text-sm text-muted-foreground">Manage system users and access</p>
                    </div>
                </div>
                <div class="pt-4">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                        Manage Users
                        <x-heroicon-o-arrow-right class="ml-2 h-4 w-4" />
                    </a>
                </div>
            </div>
        </div>

        <!-- Activity Log Card -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-4">
                    <div class="p-2 rounded-lg bg-primary/10">
                        <x-heroicon-o-clock class="w-6 h-6 text-primary" />
                    </div>
                    <div>
                        <h3 class="font-semibold">Activity Log</h3>
                        <p class="text-sm text-muted-foreground">Recent system activities</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="text-sm text-muted-foreground">
                        Recent activities will be displayed here when the activity logging is implemented.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 