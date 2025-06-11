<x-app-layout>
    <x-slot name="header">
      
    </x-slot>


<div class="px-6">
    <h1 class="text-2xl font-semibold mb-2">Create User</h1>
    <p class="text-gray-600 mb-6">Add a new Sale Person to the system.</p>

    <form action="{{ route('store.saleperson') }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div class="relative">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M5.121 17.804A9 9 0 1118.88 6.196 9 9 0 015.121 17.804z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="name" id="name"
                           class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                           placeholder="Enter name" required />
                </div>
            </div>

            <!-- Email -->
            <div class="relative">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16 12H8m8 0a4 4 0 11-8 0 4 4 0 018 0zM4 6h16M4 6v12h16V6M4 6l8 6 8-6"/>
                        </svg>
                    </span>
                    <input type="email" name="email" id="email"
                           class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                           placeholder="Enter email" required />
                </div>
            </div>

            <!-- Password -->
            <div class="relative">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 21H5a2 2 0 01-2-2v-2a7 7 0 0114 0v2a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <input type="password" name="password" id="password"
                           class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                           placeholder="Enter password" required />
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="relative">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 21H5a2 2 0 01-2-2v-2a7 7 0 0114 0v2a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                           placeholder="Confirm password" required />
                </div>
            </div>

            <!-- Counter Number -->
            <div class="relative">
                <label for="counter_number" class="block text-sm font-medium text-gray-700 mb-1">Counter Number</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8 16h8M8 12h8m-8-4h8M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <input type="text" name="counter_number" id="counter_number"
                           class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                           placeholder="Enter counter number" required />
                </div>
            </div>

            <!-- Phone Number -->
            <div class="relative">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 5a2 2 0 012-2h3l2 3h6l2-3h3a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                        </svg>
                    </span>
                    <input type="text" name="phone" id="phone"
                           class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                           placeholder="Enter phone number" required />
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6" style="display:flex; justify-content: end;">
            <button type="submit" style="background-color: #111827;"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 shadow">
                Create User
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
