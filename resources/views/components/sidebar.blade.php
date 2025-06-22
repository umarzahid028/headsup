<div x-data="{ open: true }" class="min-h-screen h-full flex flex-col flex-shrink-0 w-64 bg-white border-r border-gray-200">
    <!-- Sidebar header -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
        <a href="" class="flex items-center">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            <span class="ml-2 text-lg font-medium font-bold">HeadsUp</span>
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

            <!-- Dashboard -->
            @php
            $user = auth()->user();
            $isDashboardActive =
            ($user->hasAnyRole(['Admin', 'Sales Manager']) && request()->routeIs('dashboard')) ||
            ($user->hasRole('Sales person') && request()->routeIs('sales.perosn'));
            @endphp

          <a href="{{ $user->hasRole('Sales person') ? route('sales.perosn') : route('dashboard') }}"
                class="{{ $isDashboardActive ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">

                <svg class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>

                Dashboard
            </a>


            <!-- Appoinment  -->
            @hasrole('Sales Manager|Admin')
            <div class="pt-2">


                <a href="{{ route('appointment.records') }}" class="{{ request()->routeIs('appointment.records') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zM9 14l2 2 4-4" />
                    </svg>
                    <span class="flex-1 " style="margin-left: 5px;">Appointments</span>
                </a>
            </div>
            @endhasrole

            <!-- appointment list -->
            @hasrole('Sales person')
            <div class="pt-2">

                <a href="{{ route('appointment.records') }}" class="{{ request()->routeIs('appointment.records') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zM9 14l2 2 4-4" />
                    </svg>
                    <span class="flex-1" style="margin-left: 5px;">Appointment List</span>
                </a>
            </div>
            @endhasrole

            <!-- create Sale person -->
            @hasrole('Admin|Sales Manager')
            <div class="pt-2">

                <a href="{{ route('saleperson.table') }}" class="{{ request()->routeIs('saleperson.table') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v6m3-3h-6m-4 4a4 4 0 100-8 4 4 0 000 8zm0 0v1a4 4 0 01-4 4H5a4 4 0 01-4-4v-1a6 6 0 0112 0z" />
                    </svg>
                    <span class="flex-1" style="margin-left: 5px;">Sales Person</span>
                </a>
            </div>
            @endhasrole

            <!-- Tokens History -->
            <div class="pt-2">

                <a href="{{ route('token.history.view') }}" class="{{ request()->routeIs('token.history.view') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M9 3h6a2 2 0 012 2v1H7V5a2 2 0 012-2zm0 4h6M5 8h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2zm4 4h6m-6 4h6" />
                    </svg>
                    <span class="flex-1">Customers</span>
                </a>
            </div>

            <!-- T/O -->
            @hasrole('Sales Manager')
            <div class="pt-2">

                <a href="{{ route('to.customers') }}" class="{{ request()->routeIs('to.customers') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-black mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M4 14a8 8 0 0114-6.9M20 10a8 8 0 01-14 6.9" />
                    </svg>

                    <span class="flex-1">T/O Customers</span>
                </a>
            </div>
            @endhasrole
            <!-- Activity Records -->
            @hasrole('Sales person')
            <div class="pt-2">

                <a href="{{ route('activity.report') }}" class="{{ request()->routeIs('activity.report') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="flex-1" style="margin-left: 5px;">Activity Track</span>
                </a>
            </div>
            @endhasrole
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
                <div class="flex mt-1 items-center">
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