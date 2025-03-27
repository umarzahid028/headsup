<div x-data="{ open: true }" class="min-h-screen h-full flex flex-col flex-shrink-0 w-64 bg-white border-r border-gray-200">
    <!-- Sidebar header -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            <span class="ml-2 text-lg font-medium">TrevinosAuto</span>
        </a>
        <button @click="open = !open" class="lg:hidden text-gray-500 hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Sidebar content -->
    <div class="flex-1 overflow-y-auto p-4">
        <nav class="space-y-1">
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- Vehicle Management Section -->
            <div class="pt-5">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Vehicle Management
                </p>

                <!-- Vehicle Intake & Dispatch -->
                <a href="{{ route('vehicles.intake') }}" class="{{ request()->routeIs('vehicles.intake*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    Intake & Dispatch
                </a>
                
                <!-- Test Link (temporary) -->
                <a href="{{ route('test.vehicles.intake') }}" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Test Vehicles Intake
                </a>

                <!-- All Vehicles -->
                <a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.index') || request()->routeIs('vehicles.show') || request()->routeIs('vehicles.edit') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    All Vehicles
                </a>

                <!-- Frontline Ready -->
                <a href="{{ route('vehicles.index', ['stage' => 'frontline']) }}" class="{{ request()->is('vehicles') && request()->query('stage') == 'frontline' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Frontline Ready
                </a>
                
                <!-- Tasks -->
                <a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Tasks
                </a>
                
                <!-- We-Owe & Goodwill -->
                <a href="{{ route('we-owe.index') }}" class="{{ request()->routeIs('we-owe.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                    </svg>
                    We-Owe & Goodwill
                </a>
                
                <!-- 2Recon Workflow -->
                <a href="{{ route('recon.workflows.index') }}" class="{{ request()->routeIs('recon.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M15 13l-3 3m0 0l-3-3m3 3V8" />
                    </svg>
                    2Recon Workflow
                </a>
                
                <!-- Post-Sale Management -->
                <a href="{{ route('post-sale.index') }}" class="{{ request()->routeIs('post-sale.*') || request()->routeIs('goodwill-repairs.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Post-Sale Management
                </a>
                
                <!-- Post-Sale Dropdown Links -->
                <div class="pl-7 space-y-1">
                    <a href="{{ route('post-sale.index') }}" class="{{ request()->routeIs('post-sale.index') ? 'text-indigo-600' : 'text-gray-600 hover:text-gray-900' }} group flex items-center px-3 py-1 text-sm font-medium">
                        <span class="truncate">Archived Vehicles</span>
                    </a>
                    <a href="{{ route('post-sale.we-owe-items') }}" class="{{ request()->routeIs('post-sale.we-owe-items') ? 'text-indigo-600' : 'text-gray-600 hover:text-gray-900' }} group flex items-center px-3 py-1 text-sm font-medium">
                        <span class="truncate">We-Owe Items</span>
                    </a>
                    <a href="{{ route('post-sale.goodwill-repairs') }}" class="{{ request()->routeIs('post-sale.goodwill-repairs') ? 'text-indigo-600' : 'text-gray-600 hover:text-gray-900' }} group flex items-center px-3 py-1 text-sm font-medium">
                        <span class="truncate">Goodwill Repairs</span>
                    </a>
                    <a href="{{ route('goodwill-repairs.create') }}" class="{{ request()->routeIs('goodwill-repairs.create') ? 'text-indigo-600' : 'text-gray-600 hover:text-gray-900' }} group flex items-center px-3 py-1 text-sm font-medium">
                        <span class="truncate">New Goodwill Repair</span>
                    </a>
                </div>
            </div>
            
            <!-- Vehicle Features Section -->
            <div class="pt-5">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Vehicle Features
                </p>
                
                <!-- Tags -->
                <a href="{{ route('tags.index') }}" class="{{ request()->routeIs('tags.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    Tags
                </a>
                
                <!-- Checklists -->
                <a href="{{ route('checklists.index') }}" class="{{ request()->routeIs('checklists.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Ready-to-Post Checklists
                </a>
                
                <!-- Photos -->
                <a href="{{ route('photos.index') }}" class="{{ request()->routeIs('photos.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Vehicle Photos
                </a>
                
                <!-- Alerts -->
                <a href="{{ route('alerts.index') }}" class="{{ request()->routeIs('alerts.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Time-Based Alerts
                </a>
            </div>
            
            <!-- Admin Section -->
            <div class="pt-5">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Admin Tools
                </p>
                
                <!-- Vendors & Transporters -->
                <a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.*') || request()->routeIs('transporters.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Vendors & Transporters
                </a>
                
                <!-- FTP Import -->
                <a href="{{ route('ftp-import.index') }}" class="{{ request()->routeIs('ftp-import.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    FTP Import
                </a>
                
                <!-- Reports -->
                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Reports
                </a>
                
                <!-- Settings -->
                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
                
                <!-- User Management -->
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Users
                </a>
                
                <!-- Roles -->
                <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    Roles
                </a>
                
                <!-- Permissions -->
                <a href="{{ route('admin.permissions.index') }}" class="{{ request()->routeIs('admin.permissions.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Permissions
                </a>
                
                <!-- Route Diagnostics (for debugging) -->
                <a href="{{ route('route.diagnostics') }}" class="{{ request()->routeIs('route.diagnostics') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M15 13l-3 3m0 0l-3-3m3 3V8" />
                    </svg>
                    Route Diagnostics
                </a>
            </div>
        </nav>
    </div>

    <!-- Sidebar footer -->
    <div class="border-t border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 rounded-full bg-gray-100 p-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                <div class="flex mt-1">
                    <a href="{{ route('profile.edit') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700">Profile</a>
                    <span class="mx-1 text-gray-500">|</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-gray-500 hover:text-gray-700">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 