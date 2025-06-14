@if ($token)
<div
  id="token-card-{{ $token->id }}"
  class="bg-white shadow-md rounded-2xl p-6 mb-8 transition-all duration-300 hover:shadow-xl"
  role="region"
  aria-labelledby="token-{{ $token->id }}-label"
>
  <div class="flex items-center justify-between mb-4">
    <span id="token-{{ $token->id }}-label" class="text-xl font-bold text-gray-800">
       {{ $token->customer_name }}
    </span>

    <span
      class="text-sm font-medium text-green-800 bg-green-100 px-3 py-1 rounded-full shadow-sm"
      aria-label="Status: Assigned"
    >
      ✅ Assigned
    </span>
  </div>

  <div class="text-gray-700 text-sm space-y-2">
    <p>
      <span class="font-semibold text-gray-900">Assigned to:</span>
      {{ $token->salesperson->name ?? 'Unassigned' }}
    </p>
    <p>
      <span class="font-semibold text-gray-900">Email:</span>
      {{ $token->salesperson->email ?? 'Unassigned' }}
    </p>
    <p class="text-gray-500">
      <span class="font-semibold">Assigned on:</span>
      {{ optional($token->salesperson->created_at)->format('M d, Y h:i A') ?? 'N/A' }}
    </p>
  </div>

  <div class="mt-6 flex gap-3 justify-end">
    <button
      id="skipButton-{{ $token->id }}"
      type="button"
      class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-xl flex items-center gap-2 transition"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2"
        viewBox="0 0 24 24" width="20" height="20" class="text-white">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
      <span>Skip Token</span>
    </button>

  <button
  id="complete-btn-{{ $token->id }}"
  type="button"
  onclick="completeToken({{ $token->id }})"
  class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-xl flex items-center gap-2 transition"
  aria-label="Complete Token #{{ $token->serial_number }}"
>
  <span class="btn-text text-sm font-medium">Complete</span>
  <span
    class="spinner hidden"
    aria-hidden="true"
    style="border-top-color: white; border-width: 2.5px; border-style: solid; border-radius: 9999px; width: 1rem; height: 1rem; animation: spin 1s linear infinite;"
  ></span>
</button>

  </div>
</div>

<style>
  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
</style>

@else
<div
  class="text-center text-gray-400 mt-10 italic font-light text-base"
  role="alert"
  aria-live="polite"
>
  No assigned token found.
</div>
@endif
