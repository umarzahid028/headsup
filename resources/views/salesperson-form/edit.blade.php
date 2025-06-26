<x-app-layout>
    <style>
        :root {
  --swal2-confirm-button-background-color: #111827;
  --swal2-confirm-button-color: #fff;
  --swal2-confirm-button-border-radius: 0.25em;
  --swal2-action-button-focus-box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.5);
  --swal2-width: 32em;
  --swal2-border-radius: 0.3125rem;
  --swal2-background: #fff;
  --swal2-color: #545454;
}

div.swal2-container button.swal2-styled:not([disabled]) {
  cursor: pointer;
}

div.swal2-container button.swal2-confirm {
  background-color: var(--swal2-confirm-button-background-color);
  color: var(--swal2-confirm-button-color);
  border-radius: var(--swal2-confirm-button-border-radius);
  box-shadow: var(--swal2-confirm-button-box-shadow);
}

div.swal2-container button.swal2-styled {
  margin: 0.3125em;
  padding: 0.625em 1.1em;
  border: none;
  font-weight: 500;
  transition: background-color 0.2s, box-shadow 0.2s;
}

div.swal2-container div.swal2-popup {
  display: flex;
  flex-direction: column;
  box-sizing: border-box;
  width: var(--swal2-width);
  max-width: 100%;
  padding: 1.25em;
  background: var(--swal2-background);
  color: var(--swal2-color);
  border-radius: var(--swal2-border-radius);
}

    </style>
    <x-slot name="header">
       <h1 class="text-2xl font-semibold px-3">Edit User</h1>
    <p class="text-gray-600 px-3">Add a new Sale Person to the system.</p>
    </x-slot>


<div class="px-6">
   

 <form id="updateUserForm" method="POST" class="bg-white shadow rounded-lg p-6" data-id="{{ $edit->id }}">
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
                           placeholder="Enter name" required value="{{ $edit->name }}" />
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
                           placeholder="Enter email" required  value="{{ $edit->email }}" />
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
                           placeholder="Enter password"   />
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
                           placeholder="Confirm password"  />
                </div>
            </div>
 <div class="relative">
    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
    <div class="relative">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </span>
       <select name="role" id="role" required
        class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
    <option value="manager" {{ $edit->hasRole('Sales Manager') ? 'selected' : '' }}>Sales Manager</option>
    <option value="admin" {{ $edit->hasRole('Sales person') ? 'selected' : '' }}>Sales Person</option>
</select>

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
               value="{{ old('phone', $edit->phone ?? '') }}"
               class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
               placeholder="Enter phone number"/>
    </div>
</div>

        </div>

       

        <!-- Submit Button -->
        <div class="mt-6" style="display:flex; justify-content: end;">
            <button type="submit" style="background-color: #111827;"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 shadow">
                Update
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('updateUserForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const userId = form.getAttribute('data-id');
        const actionUrl = `/edit/sales/${userId}`;

        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.message || 'User updated successfully.',
                    confirmButtonColor: '#111827'
                }).then(() => {
                    window.location.href = `{{ route('saleperson.table') }}`;
                });
            } else {
                let msg = result.message || 'Something went wrong.';
                if (result.errors) {
                    msg = Object.values(result.errors).flat().join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
                    confirmButtonColor: '#d33'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: error.message || 'Unexpected error occurred.',
                confirmButtonColor: '#d33'
            });
        }
    });
</script>


</x-app-layout>
