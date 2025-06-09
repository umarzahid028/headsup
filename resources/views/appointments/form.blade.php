<x-app-layout>
  <x-slot name="header">
  </x-slot>
  <!-- Customer Sales Form with branding below -->
<div class="px-4">
  <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-lg max-w-7xl mx-auto">
  <h3 class="text-2xl font-bold text-gray-800 mb-2">Customer Sales Form</h3>
  <p class="text-gray-500 mb-6">Fill out the details below to log a customer sales interaction.</p>

  <form id="salesForm" action="{{ route('customer.appointment.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8">
    @csrf

    <!-- Left Column -->
    <div class="space-y-4">
      <h4 class="text-lg font-semibold text-gray-700 mb-2">Customer Information</h4>

      <!-- Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input id="name" name="name" type="text" required placeholder="Enter name"
          class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full" />
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input id="email" name="email" type="email" required placeholder="Enter email"
          class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full" />
      </div>

      <!-- Phone -->
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
        <input id="phone" name="phone" type="text" required placeholder="Enter phone number"
          class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full" />
      </div>

      <!-- Interest -->
      <div>
        <label for="interest" class="block text-sm font-medium text-gray-700 mb-1">Interest in Car</label>
        <input id="interest" name="interest" type="text" placeholder="e.g. Toyota Corolla"
          class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full" />
      </div>

      <!-- Notes -->
      <div>
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
        <textarea id="notes" name="notes" rows="4" placeholder="Any notes"
          class="border border-gray-300 rounded-xl px-4 py-3 text-base w-full resize-none"></textarea>
      </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-4">
      <h4 class="text-lg font-semibold text-gray-700 mb-2">Sales Details</h4>

      <!-- Sales Process -->
      <fieldset class="border border-gray-300 rounded-xl p-4">
        <legend class="text-sm font-semibold text-gray-700 mb-3">Sales Process</legend>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
          @foreach(['Investigating','Test Driving','Desking','Credit Application','Penciling','F&I'] as $process)
          <label class="flex items-center space-x-2">
            <input type="checkbox" name="process[]" value="{{ $process }}" class="form-checkbox h-5 w-5 text-indigo-600">
            <span class="text-gray-700 text-sm">{{ $process }}</span>
          </label>
          @endforeach
        </div>
      </fieldset>

      <!-- Disposition -->
      <fieldset class="border border-gray-300 rounded-xl p-4">
        <legend class="text-sm font-semibold text-gray-700 mb-3">Disposition</legend>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
          @foreach([
            'Sold!', 'Walked Away', 'Challenged Credit', "Didn't Like Vehicle", 
            "Didn't Like Price", "Didn't Like Finance Terms", 'Insurance Expensive', 
            'Wants to keep looking', 'Wants to think about it', 'Needs Co-Signer'
          ] as $disposition)
          <label class="flex items-center space-x-2">
            <input type="checkbox" name="disposition[]" value="{{ $disposition }}" class="form-checkbox h-5 w-5 text-indigo-600">
            <span class="text-gray-700 text-sm">{{ $disposition }}</span>
          </label>
          @endforeach
        </div>
      </fieldset>
    </div>

    <!-- Submit Button -->
    <div class="md:col-span-2 text-right mt-6">
      <button type="submit" style="background-color: #111827;"
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-xl transition duration-200">
        Submit
      </button>
    </div>
  </form>
</div>
</div>

</x-app-layout>