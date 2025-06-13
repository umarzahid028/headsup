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
      background-color: #000;
      font-family: 'Digital', monospace;
      color: #fff;
      height: 100vh;
    }

    #container {
      display: flex;
      gap: 2rem;
      width: 90%;
      max-width: 1280px;
      margin: auto;
      padding: 1rem 0;
      height: 100vh;
      box-sizing: border-box;
    }

    section {
      background-color: #0d0d0d;
      border-radius: 1.5rem;
      border-width: 4px;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      overflow: hidden;
    }

    #active {
      border-color: #ccc;
      width: 80%;
    }

    #pending {
      border-color: #777;
      width: 20%;
    }

    h1 {
      user-select: none;
      margin-bottom: 2rem;
    }

    #active h1 {
      color: #fff;
      font-size: 5rem;
      font-weight: 800;
      text-shadow: 0 0 10px #fff, 0 0 20px #999;
    }

    #pending h1 {
      color: #ccc;
      font-size: 2rem;
      font-weight: 800;
      text-shadow: 0 0 10px #999, 0 0 20px #666;
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
      font-weight: 900;
      font-size: 3rem;
      margin-bottom: 1.2rem;
      user-select: none;
      animation: fadeSlide 0.5s ease forwards;
    }

    .pending-token-row {
      color: #ddd;
      font-weight: 900;
      font-size: 2.25rem;
      text-align: center;
      margin-bottom: 1rem;
      user-select: none;
      animation: fadeSlide 0.5s ease forwards;
    }

    @keyframes fadeSlide {
      0% { opacity: 0; transform: translateY(10px) scale(0.95); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
    }
  </style>
</head>
<body>

<div id="container">
  <section id="active">
    <h1>Active Customers</h1>
    <div class="grid grid-cols-3 gap-4 text-center text-red-400 font-bold uppercase text-3xl py-2">
      <div>Counter</div>
      <div>→</div>
      <div>Name</div>
    </div>
    <div id="tokenList" class="scrollable"></div>
  </section>

  <section id="pending">
    <h1>Waiting</h1>
    <div class="scrollable" id="pendingList"></div>
  </section>
</div>

<script>
  let announcedTokens = new Set(JSON.parse(localStorage.getItem('announcedTokens') || '[]'));
  let audioEnabled = false;

  function enableAudio() {
    audioEnabled = true;
    let utterance = new SpeechSynthesisUtterance("Announcements enabled");
    speechSynthesis.speak(utterance);
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
      const response = await fetch('/queue-list', {
        headers: { 'Accept': 'application/json' }
      });
      const data = await response.json();

      const tokenList = document.getElementById('tokenList');
      tokenList.innerHTML = '';

      if (data.active.length === 0) {
        tokenList.innerHTML = `<div class="text-center text-white text-3xl">No active customers</div>`;
      } else {
        data.active.forEach((token, index) => {
          const row = document.createElement('div');
          row.className = 'active-token-row';
          row.style.animationDelay = `${index * 150}ms`;
          row.innerHTML = `
            <div>${token.counter_number}</div>
            <div>→</div>
            <div>Mr. ${token.customer_name.charAt(0).toUpperCase() + token.customer_name.slice(1)}</div>

          `;
          tokenList.appendChild(row);

          if (!announcedTokens.has(token.customer_name)) {
           speak(`Mr. ${token.customer_name}, please proceed to counter number ${token.counter_number}`);
            announcedTokens.add(token.customer_name);
            saveAnnouncedTokens();
          }
        });
      }

      const pendingList = document.getElementById('pendingList');
      pendingList.innerHTML = '';

      if (data.pending.length === 0) {
        pendingList.innerHTML = `<div class="text-yellow-400 text-xl text-center">No waiting</div>`;
      } else {
        data.pending.forEach((token, index) => {
          const row = document.createElement('div');
          row.className = 'pending-token-row';
          row.style.animationDelay = `${index * 150}ms`;
          row.textContent = token.customer_name;
          pendingList.appendChild(row);
        });
      }

    } catch (error) {
      console.error('Error fetching tokens:', error);
    }
  }

  window.onload = enableAudio;
</script>

</body>
</html>
