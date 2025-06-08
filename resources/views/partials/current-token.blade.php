@if ($token)
  <div 
    id="token-card-{{ $token->id }}" 
    class="bg-white  shadow-md p-5 mb-6  transition-shadow duration-300 hover:shadow-lg"
    role="region"
    aria-labelledby="token-{{ $token->id }}-label"
  >
    <div class="flex items-center justify-between mb-3">
      <h2 id="token-{{ $token->id }}-label" class="text-lg font-semibold text-gray-900 tracking-wide">
        Token #{{ $token->serial_number }}
      </h2>
      
      <span 
        class="text-xs font-semibold text-green-800 bg-green-100 px-3 py-0.5 rounded-full select-none shadow-sm"
        aria-label="Status: Assigned"
      >
        ✅ Assigned
      </span>
    </div>

    <div class="mb-4 text-gray-700 space-y-1 text-sm">
      <p><span class="font-semibold text-gray-900">Assigned to:</span> {{ $token->salesperson->name ?? 'Unassigned' }}</p>
      <p><span class="font-semibold text-gray-900">Email:</span> {{ $token->salesperson->email ?? 'Unassigned' }}</p>
      <p class="text-gray-500">
        <span class="font-semibold">Assigned on:</span> {{ optional($token->salesperson->created_at)->format('M d, Y h:i A') ?? 'N/A' }}
      </p>
    </div>

    <div class="flex justify-end">
@if ($token)
  <button id="skipButton-{{ $token->id }}" type="button"  style="background-color:#1f2937; margin-right: 5px;"
    class="inline-flex items-center gap-2 hover:bg-green-700 active:bg-green-800 text-white font-semibold px-5 py-2 rounded-lg shadow-sm transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-1"
    style="height: 51px;">
    
    <!-- Skip Icon SVG -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2"
      viewBox="0 0 24 24" width="20" height="20" class="text-white">
      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
    </svg>

    <span>Skip Token</span>
  </button>
@else
  <p>No assigned token.</p>
@endif

      <button 
        id="complete-btn-{{ $token->id }}" style="background-color:#1f2937;"
        type="button" 
        onclick="completeToken({{ $token->id }})" 
        class="inline-flex items-center gap-2 hover:bg-green-700 active:bg-green-800 text-white font-semibold px-5 py-2 rounded-lg shadow-sm transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-1"
        aria-label="Complete Token #{{ $token->serial_number }}"
      >
        <span class="btn-text text-sm">Complete</span>
        <span class="spinner hidden" aria-hidden="true" style="border-top-color: white; border-width: 2.5px; border-style: solid; border-radius: 80%; width: 1rem; height: 1rem; animation: spin 1s linear infinite;"></span>
      </button>

    </div>
  </div>

  <style>
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>

@else
  <div class="text-center text-gray-400 mt-8 italic font-light text-base" role="alert" aria-live="polite">
    No assigned token found.
  </div>
@endif
