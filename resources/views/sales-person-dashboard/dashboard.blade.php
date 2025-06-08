<x-app-layout>
    <x-slot name="header">
      <meta name="csrf-token" content="{{ csrf_token() }}">
        <h2 class="text-xl font-semibold leading-tight text-foreground">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

<form action="{{ route('sales.perosn.store') }}" method="POST">
  @csrf
  <div class="container mx-auto p-4">
    <div class="flex items-center space-x-4">
      
      <!-- Name (readonly) -->
      <input 
        type="text" 
        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
        value="{{ Auth::user()->name }}" 
        readonly
      >

      <!-- Toggle Button -->
      @php
        $isCheckedIn = Auth::user()->latestQueue && Auth::user()->latestQueue->is_checked_in;
      @endphp

     <button 
  id="toggleButton"
  type="submit"
  class="{{ $isCheckedIn ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} px-6 py-2 text-white rounded-lg transition"
>
  {{ $isCheckedIn ? 'Check Out' : 'Check In' }}
</button>


    </div>
  </div>
</form>
<div id="current-token-container" class="max-w-md mx-auto">
  {{-- Initial load of current token --}}
  @include('partials.current-token', ['token' => $token->where('status', 'assigned')->latest('created_at')->first()])
</div>




    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

     <script>
   $('#toggleForm').submit(function(e) {
  e.preventDefault(); // Prevent reload

  $.post($(this).attr('action'), $(this).serialize(), function(response) {
    const button = $('#toggleButton');

    if (response.checked_in) {
      button.text('Check Out');
      button.removeClass('bg-green-500 hover:bg-green-600').addClass('bg-red-500 hover:bg-red-600');
    } else {
      button.text('Check In');
      button.removeClass('bg-red-500 hover:bg-red-600').addClass('bg-green-500 hover:bg-green-600');
    }
  });
});
  </script>



<script>
  function completeToken(tokenId) {
    axios.post('/tokens/' + tokenId + '/complete')
      .then(response => {
        if (response.data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: response.data.message,
            timer: 2000,
            showConfirmButton: false,
          });

          // Remove the token card from UI
          const tokenCard = document.getElementById('token-card-' + tokenId);
          if (tokenCard) {
            tokenCard.classList.add('opacity-0', 'translate-x-4', 'duration-300');
            setTimeout(() => tokenCard.remove(), 300);
          }
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: error.response?.data?.message || 'Kuch ghalat ho gaya!',
        });
      });
  }
</script>
<div id="current-token-container" class="max-w-md mx-auto">
  {{-- Placeholder before AJAX loads --}}
  <div class="text-center text-gray-400">Loading token...</div>
</div>

<script>
  async function fetchCurrentToken() {
    try {
      const response = await fetch('/tokens/current-assigned');
      if (!response.ok) throw new Error('Network response was not ok');
      const html = await response.text();
      document.getElementById('current-token-container').innerHTML = html;
      console.log('Token refreshed at', new Date().toLocaleTimeString());
    } catch (error) {
      console.error('Token fetch error:', error);
    }
  }

  fetchCurrentToken(); // First load
  setInterval(fetchCurrentToken, 5000); // Auto refresh every 5 seconds
</script>

    @endpush
</x-app-layout>