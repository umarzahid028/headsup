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
  <h1 class="text-3xl font-bold mb-6">🎫 Name Generator</h1>

  <p class="text-gray-300">Your Generated Name</p>
  <div id="generatedToken" class="token-number mt-4"></div>
  <p id="dateTime" class="mt-3 text-sm text-gray-500"></p>

  <!-- Hidden error message -->
  <div id="errorMessage" class="hidden mt-4 text-red-400 font-semibold">
    All salespersons are currently unavailable. Please wait...
  </div>

  <input style="margin-top: 10px;" type="text" id="customerName" placeholder="Enter your name"
    class="w-full px-4 py-2 rounded-full bg-gray-800 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 mb-4" />

  <button id="generateTokenBtn"
    class="mt-6 bg-gradient-to-r from-gray-800 to-gray-600 hover:from-gray-900 hover:to-gray-800 text-white font-bold py-3 px-6 rounded-full w-full transition-all duration-300">
    Let's Go
  </button>

  <p class="mt-6 text-xs italic">Please wait for your name to appear.</p>
</div>

<script>
  const btn = document.getElementById('generateTokenBtn');
  const nameInput = document.getElementById('customerName');
  const tokenDiv = document.getElementById('generatedToken');
  const dateTimeDiv = document.getElementById('dateTime');
  const errorMessage = document.getElementById('errorMessage');

  btn.addEventListener('click', () => {
    const name = nameInput.value.trim();

    if (!name) {
      Swal.fire({
        icon: 'warning',
        title: 'Please enter your name',
        background: '#1e293b',
        color: '#fff'
      });
      return;
    }

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
      },
      body: JSON.stringify({ customer_name: name })
    })
    .then(async res => {
      Swal.close();

      const data = await res.json();

      if (!res.ok) {
        errorMessage.classList.remove('hidden');
        errorMessage.textContent = data.message || "Failed to generate token. Try again.";
        setTimeout(() => errorMessage.classList.add('hidden'), 4000);
        tokenDiv.textContent = '';
        dateTimeDiv.textContent = '';
        return;
      }

      if (data.token) {
        tokenDiv.textContent = data.token.customer_name.toUpperCase();
        const now = new Date();
        dateTimeDiv.textContent = `📅 ${now.toLocaleDateString()} | 🕒 ${now.toLocaleTimeString()}`;
        errorMessage.classList.add('hidden');
        nameInput.value = '';
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
