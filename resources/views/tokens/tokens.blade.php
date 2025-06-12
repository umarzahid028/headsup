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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(255, 255, 255, 0.1);
        }

        .token-number {
            font-size: 4rem;
            font-weight: 900;
            letter-spacing: 0.15em;
            animation: glow 1.5s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                /* text-shadow: 0 0 10px white, 0 0 20px white; */
            }

            to {
                /* text-shadow: 0 0 20px #ccc, 0 0 30px #ccc; */
            }
        }

        .typing {
            overflow: hidden;
            white-space: nowrap;
            font-size: 1.2rem;
            display: inline-block;
        }

        @keyframes blink-caret {
            from,
            to {
                border-color: transparent
            }

            50% {
                border-color: white
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 bg-gray-900">

    <div class="glass w-full max-w-md p-8 text-center">
        <h1 class="text-3xl font-extrabold mb-6 text-white">🎫 Token Generator</h1>

        
        <div class="mt-10">
            <p class="typing text-gray-300">Your Token Number</p>
            <div id="generatedToken" class="token-number mt-4 text-white"></div>
            <p id="dateTime" class="mt-3 text-sm text-gray-500"></p>
        </div>
        <button id="generateTokenBtn"
            class="w-full text-xl bg-gradient-to-r from-gray-800 to-gray-600 hover:from-gray-900 hover:to-gray-800 text-white font-bold py-3 px-8 rounded-full shadow-xl transition-all duration-300 focus:outline-none">
            Generate Token
        </button>

        <p class="mt-6 text-xs text-white italic">Please wait for your token to appear.</p>
    </div>

    <script>
        const btn = document.getElementById('generateTokenBtn');
        const tokenDiv = document.getElementById('generatedToken');
        const dateTimeDiv = document.getElementById('dateTime');

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
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.token) {
                    const now = new Date();
                    const time = now.toLocaleTimeString();
                    const date = now.toLocaleDateString();

                    tokenDiv.textContent = String(data.token.serial_number).padStart(3, '0');
                    dateTimeDiv.textContent = `📅 ${date} | 🕒 ${time}`;
                } else {
                    tokenDiv.textContent = "All salespersons are currently unavailable. Please wait.";
                    dateTimeDiv.textContent = "";
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
