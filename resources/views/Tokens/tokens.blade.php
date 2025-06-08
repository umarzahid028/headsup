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
            background: linear-gradient(to top right, #0f172a, #1e293b, #334155);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .token-number {
            font-size: 4rem;
            font-weight: 900;
            letter-spacing: 0.15em;
            animation: glow 1.5s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                text-shadow: 0 0 10px #3b82f6, 0 0 20px #3b82f6;
            }
            to {
                text-shadow: 0 0 20px #06b6d4, 0 0 30px #06b6d4;
            }
        }

        .typing {
            overflow: hidden;
            white-space: nowrap;
            animation: typing 2s steps(20, end), blink-caret 0.75s step-end infinite;
            border-right: 3px solid #3b82f6;
            font-size: 1.2rem;
            display: inline-block;
        }

        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }

        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: #3b82f6 }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">

    <div class="glass w-full max-w-md p-8 text-center">
        <h1 class="text-3xl font-extrabold mb-6 text-blue-400">🎫 Token Generator</h1>

        <button id="generateTokenBtn"
            class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-bold py-3 px-8 rounded-full shadow-xl transition-all duration-300 focus:outline-none">
            Generate Token
        </button>

        <div class="mt-10">
            <p class="typing text-gray-300">Your Token Number:</p>
            <div id="generatedToken" class="token-number mt-4 text-cyan-400"></div>
            <p id="dateTime" class="mt-3 text-sm text-gray-400"></p>
        </div>

        <p class="mt-6 text-xs text-gray-500 italic">Please wait for your token to appear.</p>
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
                    tokenDiv.textContent = "No token found";
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
