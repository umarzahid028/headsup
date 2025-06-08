<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Tokens Display</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @font-face {
      font-family: 'Digital';
      src: url('https://fonts.cdnfonts.com/s/15077/Digital7-rg1mL.ttf') format('truetype');
    }

    html, body {
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      background-color: black;
      height: 100vh;
      font-family: 'Digital', monospace;
      color: white;
    }

    #container {
      display: flex;
      gap: 2rem;
      width: 90%;
      max-width: 1280px;
      /* initially hidden until audio enabled */
      display: none;
      margin: auto;
      padding: 1rem 0;
      height: 100vh;
      box-sizing: border-box;
    }

    section {
      background-color: black;
      border-radius: 1.5rem;
      border-width: 4px;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow: hidden;
    }

    #active {
      border-color: #ef4444;
      width: 80%;
    }

    #pending {
      border-color: #fbbf24;
      width: 20%;
    }

    h1 {
      user-select: none;
      margin-bottom: 2rem;
    }

    #active h1 {
      color: #f87171;
      font-size: 5rem;
      font-weight: 800;
      text-shadow: 0 0 10px #f87171, 0 0 20px #ef4444;
    }

    #pending h1 {
      color: #fbbf24;
      font-size: 2rem;
      font-weight: 800;
      text-shadow: 0 0 10px #fbbf24, 0 0 20px #fbbf24;
    }

    .scrollable {
      overflow-y: auto;
      max-height: calc(100vh - 12rem);
      width: 100%;
    }

    .active-token-row {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      text-align: center;
      color: #f87171;
      font-weight: 900;
      font-size: 3rem;
      margin-bottom: 1.2rem;
      user-select: none;
      text-shadow: 0 0 10px #f87171, 0 0 20px #ef4444;
      animation: fadeSlide 0.5s ease forwards;
    }

    .pending-token-row {
      color: #fbbf24;
      font-weight: 900;
      font-size: 2.25rem;
      text-align: center;
      margin-bottom: 1rem;
      user-select: none;
      text-shadow: 0 0 10px #fbbf24, 0 0 20px #fbbf24;
      animation: fadeSlide 0.5s ease forwards;
    }

    @keyframes fadeSlide {
      0% { opacity: 0; transform: translateY(10px) scale(0.95); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Overlay for the button to block interaction with rest of page */
    #overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.85);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      user-select: none;
      flex-direction: column;
      gap: 1rem;
    }

    #audioButton {
      
      color: white;
      font-weight: 800;
      padding: 1rem 2.5rem;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(37, 99, 235, 0.5);
      font-size: 1.5rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 1rem;
      user-select: none;
      border: none;
      transition: background-color 0.3s ease;
    }

    #audioButton:hover {
      background-color: #1d4ed8; /* blue-700 */
    }

    #audioButton:active {
      background-color: #1e40af; /* blue-800 */
    }

    #audioButton span.emoji {
      font-size: 2rem;
    }
  </style>
</head>
<body>

  <!-- Overlay with centered button -->
  <div id="overlay" role="dialog" aria-modal="true" aria-label="Enable announcements overlay">
    <button id="audioButton" aria-label="Enable announcements">
      <span class="emoji">🔊</span>
      Enable Announcements
    </button>
  </div>

  <div id="container" role="main" aria-live="polite" aria-atomic="true" aria-relevant="additions">
    <section id="active">
      <h1>Active Tokens</h1>
      <div class="grid grid-cols-3 gap-4 text-center text-red-400 font-bold uppercase text-3xl py-2">
        <div>Counter</div>
        <div>→</div>
        <div>Token</div>
      </div>
      <div id="tokenList" class="scrollable"></div>
    </section>

    <section id="pending">
      <h1>Pending</h1>
      <div class="scrollable" id="pendingList">
        <!-- Laravel Blade will render pending tokens initially -->
        {{-- @forelse ($pendingtokens as $token)
          <div class="pending-token-row" style="animation-delay: {{ $loop->index * 150 }}ms;">
            {{ str_pad($token->serial_number, 3, '0', STR_PAD_LEFT) }}
          </div>
        @empty
          <div class="text-yellow-400 text-xl text-center select-none">No pending tokens</div>
        @endforelse --}}
      </div>
    </section>
  </div>

  <script>
    let announcedTokens = new Set(JSON.parse(localStorage.getItem('announcedTokens') || '[]'));
    let audioEnabled = false;

    const overlay = document.getElementById('overlay');
    const audioButton = document.getElementById('audioButton');
    const container = document.getElementById('container');

    function enableAudio() {
      audioEnabled = true;
      // Speak confirmation
      let utterance = new SpeechSynthesisUtterance("Announcements enabled");
      speechSynthesis.speak(utterance);

      // Hide overlay and show main content
      overlay.style.display = 'none';
      container.style.display = 'flex';

      // Immediately fetch tokens and start interval
      fetchAndUpdateTokens();
      setInterval(fetchAndUpdateTokens, 5000);
    }

    function speak(text) {
      if (audioEnabled && 'speechSynthesis' in window) {
        let utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-US';
        speechSynthesis.speak(utterance);
      }
    }

    function saveAnnouncedTokens() {
      localStorage.setItem('announcedTokens', JSON.stringify(Array.from(announcedTokens)));
    }

    async function fetchAndUpdateTokens() {
      try {
        const response = await fetch('/tokens/active', {
          headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();

        // Update active tokens
        const tokenList = document.getElementById('tokenList');
        tokenList.innerHTML = '';

        if(data.active.length === 0){
          tokenList.innerHTML = `<div class="text-center text-white text-3xl glow digital">No active tokens</div>`;
        } else {
          data.active.forEach((token, index) => {
            const row = document.createElement('div');
            row.className = 'active-token-row';
            row.style.animationDelay = `${index * 150}ms`;
            row.innerHTML = `
              <div>${token.counter_number}</div>
              <div>→</div>
              <div>${String(token.serial_number).padStart(3, '0')}</div>
            `;
            tokenList.appendChild(row);

            if (!announcedTokens.has(token.serial_number)) {
              speak(`Token number ${token.serial_number}, please proceed to counter number ${token.counter_number}`);
              announcedTokens.add(token.serial_number);
              saveAnnouncedTokens();
            }
          });
        }

        // Update pending tokens
        const pendingList = document.getElementById('pendingList');
        pendingList.innerHTML = '';

        if(data.pending.length === 0){
          pendingList.innerHTML = `<div class="text-yellow-400 text-xl text-center select-none">No pending tokens</div>`;
        } else {
          data.pending.forEach((token, index) => {
            const row = document.createElement('div');
            row.className = 'pending-token-row';
            row.style.animationDelay = `${index * 150}ms`;
            row.textContent = String(token.serial_number).padStart(3, '0');
            pendingList.appendChild(row);
          });
        }

      } catch(error) {
        console.error('Error fetching tokens:', error);
      }
    }

    audioButton.addEventListener('click', enableAudio);
  </script>

</body>
</html>
