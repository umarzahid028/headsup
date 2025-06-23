<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Status Display</title>
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
      display: flex;
      gap: 1.5rem;
    }

    section {
      background-color: #0d0d0d;
      border-radius: 1.5rem;
      border: 4px solid #ccc;
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    h1 {
      user-select: none;
      font-size: 3.5rem;
      font-weight: bold;
      color: #fff;
      text-shadow: 0 0 10px #fff, 0 0 20px #999;
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .scrollable {
      overflow-y: auto;
      max-height: calc(100vh - 10rem);
      width: 100%;
      scrollbar-width: none;
    }

    .scrollable::-webkit-scrollbar {
      display: none;
    }

    .token-heading,
    .active-token-row {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  align-items: center;
  font-weight: 700;
  gap: 1rem;
  text-align: center;
  margin-bottom: 1rem; 
  padding: 0.5rem 0;
  border-bottom: 1px solid #333;
}


    .token-heading {
      font-size: 1.75rem;
      color: #ff4444;
      border-bottom: 2px solid #555;
      margin-bottom: 1rem;
    }

    .active-token-row div {
      font-size: 1.5rem;
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

    .checkin-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 1.25rem;
      padding: 0.5rem 0;
      border-bottom: 1px solid #444;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .checkin-name {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
      padding-right: 1rem;
      font-size: 1.25rem;
    }

    .checkin-time {
      white-space: nowrap;
      font-size: 0.9rem;
    }

    .active-token-row.bg-yellow-900 {
  animation: pulse-glow 2s infinite;
}

@keyframes pulse-glow {
  0% {
    box-shadow: 0 0 0px #facc15;
  }
  50% {
    box-shadow: 0 0 20px #facc15;
  }
  100% {
    box-shadow: 0 0 0px #facc15;
  }
}

  </style>
</head>
<body>
  <div id="container">

    <!-- Left Section: Active Tokens -->
    <section class="flex-1">
      <h1>Status</h1>
      <div class="token-heading">
        <div>Salesperson</div>
        <div>Customer</div>
        <div>Status</div>
      </div>
      <div id="tokenList" class="scrollable">
        <div class="text-center text-white text-xl">Loading...</div>
      </div>
    </section>

    <!-- Right Section: Check-Ins -->
    <section class="w-[25%]">
      <h1>Check-In</h1>
      <div class="token-heading" style="grid-template-columns: 1fr;">
        <div>Name & Time</div>
      </div>
      <div id="checkinList" class="scrollable">
        <div class="text-center text-white text-xl">Loading check-ins...</div>
      </div>
    </section>

  </div>

<script>
  function formatTime(dateString) {
    if (!dateString) return 'Unknown';
    const date = new Date(dateString);
    if (isNaN(date)) return 'Invalid date';

    const options = {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true,
      month: 'short',
      day: '2-digit'
    };
    return date.toLocaleString('en-US', options);
  }

  async function fetchAndUpdateTokens() {
    try {
      const response = await fetch('{{ route('tokens.active') }}', {
        headers: { 'Accept': 'application/json' }
      });

      const data = await response.json();
      const tokenList = document.getElementById('tokenList');
      tokenList.innerHTML = '';

      if (!data.active || data.active.length === 0) {
        tokenList.innerHTML = `<div class="text-center text-white text-xl">No active records</div>`;
        return;
      }

      data.active.forEach((token) => {
        const name = token.sales_person || 'Unknown';

        token.customers.forEach((customer) => {
          const customerName = customer.customer_name || 'Unknown Customer';
          const processes = customer.process || [];

          const forwardedAt = new Date(customer.forwarded_at);
          const now = new Date();
          const isForwarded = customer.forwarded === true && (now - forwardedAt) < (5 * 60 * 1000); // 5 minutes

          const row = document.createElement('div');
          row.className = 'active-token-row';
          if (isForwarded) {
            row.style.backgroundColor = '#222d44'; // 💡 highlight style
            row.style.boxShadow = '0 0 15px #44f';  // optional
          }

          row.innerHTML = `
            <div><span class="whitespace-nowrap">${name}</span></div>
            <div><span class="inline-block text-base">${customerName}</span></div>
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
        checkinList.innerHTML = `<div class="text-center text-white text-xl">No check-ins</div>`;
        return;
      }

      data.forEach((person) => {
        const row = document.createElement('div');
        row.className = 'checkin-row';
        row.innerHTML = `
          <div class="flex flex-col w-full overflow-hidden">
            <div class="checkin-name font-semibold text-white truncate">${person.name || 'Unnamed'}</div>
            <div class="checkin-time text-gray-400 mt-1">${formatTime(person.time)}</div>
          </div>
        `;
        checkinList.appendChild(row);
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
    setInterval(fetchCheckins, 5000);
  };
</script>

</body>
</html>
