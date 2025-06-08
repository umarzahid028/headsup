<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Token Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes pulseScale {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-white min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-2xl rounded-3xl p-10 w-full max-w-lg border-t-[10px] border-blue-700 text-center animate-fadeIn">
        <h1 class="text-4xl font-extrabold text-blue-700 mb-6 tracking-wide">🎫 Get Your Token</h1>

        <button id="generateTokenBtn"
            class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xl font-bold py-4 px-8 rounded-xl shadow-lg transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-200 active:scale-95">
            Generate Token
        </button>

        <div id="generatedToken"
             class="mt-8 text-5xl font-mono font-extrabold text-green-600 tracking-widest"
             style="animation: pulseScale 1.5s infinite ease-in-out;">
        </div>

        <p class="mt-4 text-gray-500 text-sm italic">Please wait for your number to appear on the screen.</p>
    </div>

    <script>
        const generateTokenBtn = document.getElementById('generateTokenBtn');
        const generatedToken = document.getElementById('generatedToken');

        generateTokenBtn.addEventListener('click', () => {
            fetch('/tokens/generate', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.token) {
                    generatedToken.textContent = String(data.token.serial_number).padStart(3, '0');
                } else if (data.message) {
                    generatedToken.textContent = data.message;
                }
            });
        });
    </script>

</body>
</html>
