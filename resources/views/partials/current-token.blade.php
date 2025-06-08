@if ($token)
<div id="token-card-{{ $token->id }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6 mx-4 transition hover:shadow-md">
  <div class="flex items-center justify-between mb-2">
    <h2 class="text-xl font-semibold text-gray-800">Token #{{ $token->serial_number }}</h2>
    <span class="inline-block text-sm text-green-700 bg-green-100 px-3 py-1 rounded-full font-medium">
      Assigned
    </span>
  </div>

  <p class="text-gray-600 mb-4">
    <span class="font-medium text-gray-700">Assigned to:</span>
    {{ $token->salesperson->name ?? 'Unassigned' }}
  </p>

  <div class="flex justify-end">
    <button 
      type="button" 
      onclick="completeToken({{ $token->id }})" 
      class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg transition-all duration-200">
      ✅ Complete Customer
    </button>
  </div>
</div>
@else
<div class="text-center text-gray-500 mt-6">
  No assigned token found.
</div>
@endif
