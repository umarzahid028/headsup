<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Queues List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <style>
    @font-face {
      font-family: 'Digital';
      src: url('https://fonts.cdnfonts.com/s/15077/Digital7-rg1mL.ttf') format('truetype');
    }
    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Digital', monospace;
      color: #fff;
      height: 100vh;
      background: linear-gradient(270deg, #000000, #111111, #1a1a1a);
      background-size: 600% 600%;
      animation: gradientBackground 15s ease infinite;
    }
    @keyframes gradientBackground {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
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
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
      animation: sectionGlow 3s ease-in-out infinite alternate;
    }
    @keyframes sectionGlow {
      0% { box-shadow: 0 0 10px rgba(255, 255, 255, 0.1); }
      100% { box-shadow: 0 0 20px rgba(255, 255, 255, 0.3); }
    }
    h1 {
      user-select: none;
      font-size: 3.5rem;
      font-weight: bold;
      text-align: center;
      margin-bottom: 1.5rem;
      background: linear-gradient(90deg, #fff, #999, #fff);
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent;
      animation: shimmerText 4s infinite linear;
    }
    @keyframes shimmerText {
      0% { background-position: -100% 0; }
      100% { background-position: 100% 0; }
    }
    .scrollable {
      overflow-y: auto;
      max-height: calc(100vh - 10rem);
      width: 100%;
      scrollbar-width: none;
    }
    .scrollable::-webkit-scrollbar { display: none; }
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
    .highlight-turn {
      background-color: #222d44 !important;
      font-weight: bold;
      box-shadow: 0 0 15px #44f;
    }
  </style>
</head>
<body>
  <div id="container">
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

    <section class="w-[25%]">
      <h1>Check-In</h1>
      <div class="token-heading" style="grid-template-columns: 1fr;">
        <div>Name & Time</div>
      </div>
      <div id="checkinList" class="scrollable">
        <div class="text-center text-white text-xl">No Activity</div>
      </div>
    </section>
  </div>
<script>
let currentTurnUser = '';
let previousTurnUser = '';
let synthReady = false;
let lastForwardedCustomerIds = [];
const highlightTimers = {}; // Track highlight timeouts

function prepareVoiceEngine() {
  if (synthReady) return;
  const utter = new SpeechSynthesisUtterance('');
  speechSynthesis.speak(utter);
  synthReady = true;
}

function formatTime(dateString) {
  if (!dateString) return 'Unknown';
  const date = new Date(dateString);
  if (isNaN(date)) return 'Invalid date';
  return date.toLocaleString('en-US', {
    hour: '2-digit', minute: '2-digit', hour12: true, month: 'short', day: '2-digit'
  });
}

function speak(text) {
  if ('speechSynthesis' in window && synthReady) {
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'en-US';
    utterance.rate = 1;
    speechSynthesis.cancel();
    speechSynthesis.speak(utterance);
  }
}

async function fetchCurrentTurnUser() {
  try {
    const res = await fetch('/next-turn-status');
    const data = await res.json();

    const userName = data?.user_name ?? '';
    previousTurnUser = currentTurnUser;
    currentTurnUser = userName.toLowerCase();

    const isNewTurn = currentTurnUser !== previousTurnUser;
    const isValidName = currentTurnUser && userName;

    if (synthReady && isValidName && isNewTurn) {
      speak(`It's your turn now, ${userName}`);
    }

    fetchCheckins();
  } catch (err) {
    console.error('Error fetching current turn user:', err);
    currentTurnUser = '';
  }
}

async function fetchAndUpdateTokens() {
  try {
    const res = await fetch('{{ route('tokens.active') }}', {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    const tokenList = document.getElementById('tokenList');
    tokenList.innerHTML = '';

    if (!data.active || data.active.length === 0) {
      tokenList.innerHTML = `<div class="text-center text-white text-xl py-10">No active customers found</div>`;
      return;
    }

    const highlightCustomerIds = JSON.parse(localStorage.getItem('highlightCustomerIds') || '[]');
    let forwardedIds = [];
    let hasCustomer = false;
    const now = Date.now();

    data.active.forEach((token) => {
      const name = token.sales_person || 'Unknown';

      (token.customers || []).forEach((customer) => {
        if (!customer || !customer.id) return;

        hasCustomer = true;
        const customerId = String(customer.id);
        const customerName = customer.customer_name || 'Unknown Customer';
        const processes = customer.process || [];
        const isForwarded = customer.forwarded;
        const forwardedAt = new Date(customer.forwarded_at).getTime();
        const isLocalHighlight = customer.forwarded_at;

        const row = document.createElement('div');
        row.className = 'active-token-row';
        row.dataset.customerId = customerId;

        const shouldHighlight = (isForwarded && now - forwardedAt < 5 * 60 * 1000) || isLocalHighlight;
        const highlightExpiresAt = highlightTimers[customerId] || (forwardedAt + 5 * 60 * 1000);
        const isExpired = now > highlightExpiresAt;

        if ((shouldHighlight || !isExpired) && !isExpired) {
          row.classList.add('highlight-turn');
          forwardedIds.push(customerId);

          if (!highlightTimers[customerId]) {
            highlightTimers[customerId] = forwardedAt + 5 * 60 * 1000;

            setTimeout(() => {
              delete highlightTimers[customerId];
              const highlightedRow = document.querySelector(`.active-token-row[data-customer-id="${customerId}"]`);
              if (highlightedRow) {
                highlightedRow.classList.remove('highlight-turn');
              }
            }, highlightTimers[customerId] - now);
          }

          const alreadySpoken = lastForwardedCustomerIds.includes(customerId);
          if (!alreadySpoken) {
            const audio = new Audio('/sounds/manager.mp3');
            audio.play().catch(err => console.error('Audio play failed:', err));
          }

          const remaining = highlightCustomerIds.filter(id => id !== customerId);
          localStorage.setItem('highlightCustomerIds', JSON.stringify(remaining));
        } else {
          // Remove expired highlight if still present
          row.classList.remove('highlight-turn');
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

    lastForwardedCustomerIds = forwardedIds;

    if (!hasCustomer) {
      tokenList.innerHTML = `<div class="text-center text-white text-xl py-10">No active customers at the moment</div>`;
    }
  } catch (err) {
    console.error('Error fetching tokens:', err);
    document.getElementById('tokenList').innerHTML = `<div class="text-red-500 text-center">Error loading customer data</div>`;
  }
}

async function fetchCheckins() {
  try {
    const res = await fetch('{{ route('salespersons.checkin') }}', {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    const checkinList = document.getElementById('checkinList');
    checkinList.innerHTML = '';

    if (!data || data.length === 0) {
      checkinList.innerHTML = `<div class="text-center text-white text-xl py-10">No check-in</div>`;
      return;
    }

    data.forEach((person) => {
      const isCurrent = person.name?.toLowerCase() === currentTurnUser;
      const row = document.createElement('div');
      row.className = `checkin-row ${isCurrent ? 'highlight-turn' : ''}`;
      row.innerHTML = `
        <div class="flex flex-col w-full overflow-hidden px-4">
          <div class="checkin-name truncate">${person.name || 'Unnamed'}</div>
          <div class="checkin-time text-gray-400 mt-1">${formatTime(person.time)}</div>
        </div>
      `;
      checkinList.appendChild(row);
    });
  } catch (err) {
    console.error('Check-in fetch error:', err);
    document.getElementById('checkinList').innerHTML = `<div class="text-red-500 text-center">Failed to load check-ins</div>`;
  }
}

function refreshScreen() {
  fetchCurrentTurnUser();
  fetchAndUpdateTokens();
  prepareVoiceEngine();
}

window.onload = () => {
  refreshScreen();
  setInterval(refreshScreen, 3000);
};
</script>





</body>
</html>
