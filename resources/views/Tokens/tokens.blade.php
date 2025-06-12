<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Token Generator</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .glass {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 8px 32px rgba(255, 255, 255, 0.1);
    }
    .token-number {
      font-size: 4rem;
      font-weight: 900;
      letter-spacing: 0.15em;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-900 px-4">

  <div class="glass w-full max-w-md p-8 text-center text-white">
    <h1 class="text-3xl font-bold mb-6">🎫 Token Generator</h1>

    <p class="text-gray-300">Your Token Number</p>
    <div id="generatedToken" class="token-number mt-4"></div>
    <p id="dateTime" class="mt-3 text-sm text-gray-500"></p>

    <!-- Hidden error message -->
    <div id="errorMessage" class="hidden mt-4 text-red-400 font-semibold">
      All salespersons are currently unavailable. Please wait...
    </div>

    <button id="generateTokenBtn"
      class="mt-6 bg-gradient-to-r from-gray-800 to-gray-600 hover:from-gray-900 hover:to-gray-800 text-white font-bold py-3 px-6 rounded-full w-full transition-all duration-300">
      Generate Token
    </button>

    <p class="mt-6 text-xs italic">Please wait for your token to appear.</p>
  </div>

  <script>
    const btn = document.getElementById('generateTokenBtn');
    const tokenDiv = document.getElementById('generatedToken');
    const dateTimeDiv = document.getElementById('dateTime');
    const errorMessage = document.getElementById('errorMessage');

   btn.addEventListener('click', () => {
  Swal.fire({
    title: 'Processing...',
    background: '#1e293b',
    color: '#fff',
    showConfirmButton: false,
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  fetch('/tokens/generate', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })
  .then(async res => {
    Swal.close();

    const data = await res.json();

    if (res.status === 422 && data.message === 'No available salespersons found') {
      // Show only this if NO one is checked in
      errorMessage.classList.remove('hidden');
      errorMessage.textContent = "All salespersons are currently unavailable. Please wait...";
      setTimeout(() => errorMessage.classList.add('hidden'), 5000);
      tokenDiv.textContent = '';
      dateTimeDiv.textContent = '';
      return;
    }

    // ✅ Token created, show token
    if (data.token) {
      tokenDiv.textContent = String(data.token.serial_number).padStart(3, '0');
      const now = new Date();
      dateTimeDiv.textContent = `📅 ${now.toLocaleDateString()} | 🕒 ${now.toLocaleTimeString()}`;

      // ❌ DO NOT show message if busy — no need for error here
      errorMessage.classList.add('hidden');
    }
  })
  .catch(() => {
    Swal.close();
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Something went wrong. Try again later!',
      background: '#1e293b',
      color: '#fff'
    });
  });
});

  </script>
</body>
</html>
