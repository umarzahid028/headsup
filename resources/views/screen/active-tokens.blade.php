<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Salesperson Status Display</title>
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
      width: 90%;
      max-width: 1600px;
      margin: auto;
      padding: 1rem 0;
      height: 100vh;
      box-sizing: border-box;
      display: flex;
      gap: 1.5rem;
    }

    section {
      background-color: #0d0d0d;
      border-radius: 1.5rem;
      border: 4px solid #ccc;
      padding: 2rem;
      display: flex;
      flex-direction: column;
      height: 100%;
      animation: fadeSlide 1s ease-out;
    }

    h1 {
      user-select: none;
      color: #fff;
      font-size: 4rem;
      font-weight: 800;
      margin-bottom: 2rem;
      text-shadow: 0 0 10px #fff, 0 0 20px #999;
      text-align: center;
    }

    .scrollable {
      overflow-y: auto;
      max-height: calc(100vh - 12rem);
      width: 100%;
      scrollbar-width: none;
    }

    .scrollable::-webkit-scrollbar {
      display: none;
    }

    .token-heading,
    .active-token-row {
      display: grid;
      grid-template-columns: 1fr 2fr 1fr;
      align-items: center;
      font-weight: 900;
      word-break: break-word;
      gap: 1rem;
      text-align: center;
    }

    .token-heading {
      color: #ff4444;
      font-size: 2.5rem;
      border-bottom: 2px solid #555;
      margin-bottom: 1.5rem;
      width: 100%;
    }

    .active-token-row div {
      font-size: 2rem;
    }

    .active-token-row div:nth-child(2) {
      font-size: 1.25rem;
    }

    .status-badge {
      background-color: #444;
      border-radius: 0.5rem;
      padding: 0.25rem 0.75rem;
      display: inline-block;
      margin: 0.25rem;
      font-size: 1rem;
    }

    .checkin-item {
      border-bottom: 1px solid #444;
      padding: 0.75rem 0;
    }

    .checkin-item div {
      font-size: 1.5rem;
    }

    .checkin-item small {
      color: #999;
      font-size: 1rem;
    }

    @keyframes fadeSlide {
      0% {
        opacity: 0;
        transform: translateY(10px) scale(0.95);
      }
      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
  </style>
</head>
<body>
  <div id="container">
    
    <!-- Left: Active Tokens -->
    <section id="active" class="flex-1">
      <h1>Status</h1>
      <div class="token-heading">
        <div>Name</div>
        <div>Customer Name</div>
        <div>Status</div>
      </div>
      <div id="tokenList" class="scrollable"></div>
    </section>

    <!-- Right: Check-In List -->
    <section id="checkin" class="w-[35%]">
      <h1>Check-In</h1>
      <div id="checkinList" class="scrollable">
        <div class="text-center text-white text-2xl">Loading check-ins...</div>
      </div>
    </section>
  </div>

  <script>
    async function fetchAndUpdateTokens() {
      try {
        const response = await fetch('{{ route('tokens.active') }}', {
          headers: { 'Accept': 'application/json' }
        });

        const data = await response.json();
        const tokenList = document.getElementById('tokenList');
        tokenList.innerHTML = '';

        if (!data.active || data.active.length === 0) {
          tokenList.innerHTML = `<div class="text-center text-white text-3xl">No active records</div>`;
          return;
        }

        data.active.forEach((token, index) => {
          const name = token.sales_person || 'Unknown';

          token.customers.forEach((customer, cIndex) => {
            const customerName = customer.customer_name || 'Unknown Customer';
            const processes = customer.process || [];

            const row = document.createElement('div');
            row.className = 'active-token-row';
            row.style.animationDelay = `${(index * 3 + cIndex) * 150}ms`;
            row.innerHTML = `
              <div><span class="whitespace-nowrap">${name}</span></div>
              <div><span class="inline-block text-lg">${customerName}</span></div>
              <div>
                ${
                  processes.length
                    ? processes.map(p => `<span class="status-badge">${p}</span>`).join('')
                    : '<span class="text-gray-500">No status</span>'
                }
              </div>
            `;
            tokenList.appendChild(row);
          });
        });

      } catch (error) {
        console.error('Error fetching tokens:', error);
        document.getElementById('tokenList').innerHTML = `<div class="text-red-500 text-center">Error loading data</div>`;
      }
    }

    async function fetchCheckins() {
      try {
        const response = await fetch('{{ route('salespersons.checkin') }}', {
          headers: { 'Accept': 'application/json' }
        });

        const data = await response.json();
        const checkinList = document.getElementById('checkinList');
        checkinList.innerHTML = '';

        if (!data || data.length === 0) {
          checkinList.innerHTML = `<div class="text-center text-white text-2xl">No check-ins</div>`;
          return;
        }

        data.forEach((person) => {
          const div = document.createElement('div');
          div.className = 'checkin-item';
          div.innerHTML = `
            <div>${person.name || 'Unnamed'}</div>
            <small>${person.time || 'Unknown time'}</small>
          `;
          checkinList.appendChild(div);
        });

      } catch (error) {
        console.error('Check-in fetch error:', error);
        document.getElementById('checkinList').innerHTML = `<div class="text-red-500 text-center">Failed to load check-ins</div>`;
      }
    }

    window.onload = () => {
      fetchAndUpdateTokens();
      fetchCheckins();
      setInterval(fetchAndUpdateTokens, 5000);
      setInterval(fetchCheckins, 10000);
    };
  </script>
</body>
</html>
