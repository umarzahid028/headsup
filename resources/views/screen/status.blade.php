<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="refresh" content="5">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TrevinosAuto - Salesperson Availability</title>
  <style>
    /* Reset & base */
    *,
    *::before,
    *::after {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      color: #f0f4f8;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 2rem 1rem;
      overflow-x: hidden;
      position: relative;
    }

    /* Animated abstract background circles */
    body::before,
    body::after {
      content: "";
      position: fixed;
      border-radius: 50%;
      opacity: 0.15;
      filter: blur(70px);
      z-index: 0;
      animation: moveAround 15s ease-in-out infinite;
    }

    body::before {
      width: 350px;
      height: 350px;
      background: #00bfff;
      top: 10%;
      left: -10%;
      animation-delay: 0s;
    }

    body::after {
      width: 450px;
      height: 450px;
      background: #007bff;
      bottom: 10%;
      right: -15%;
      animation-delay: 7s;
    }

    @keyframes moveAround {

      0%,
      100% {
        transform: translate(0, 0);
      }

      50% {
        transform: translate(30px, -40px);
      }
    }

    h1 {
      font-weight: 900;
      font-size: 2.8rem;
      margin-bottom: 3rem;
      letter-spacing: 3px;
      z-index: 10;
      text-shadow: 0 0 12px rgba(255 255 255 / 0.25);
      user-select: none;
    }

    /* Container */
    .container {
      width: 100%;
      max-width: 1200px;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      /* Fixed 2 columns */
      gap: 2rem;
      z-index: 10;
    }


    /* Card */
    @keyframes slideInLeft {
      0% {
        opacity: 0;
        transform: translateX(-50px);
      }

      100% {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .card {
      background: linear-gradient(145deg, #243b55, #141e30);
      border-radius: 20px;
      padding: 1.8rem 2.5rem;
      display: flex;
      align-items: center;
      gap: 1.8rem;
      box-shadow:
        0 8px 18px rgba(0, 123, 255, 0.3),
        0 0 25px rgba(0, 123, 255, 0.2);
      animation: slideInLeft 0.6s ease forwards;
      cursor: default;
      user-select: none;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: scale(1.04);
      box-shadow:
        0 20px 40px rgba(0, 123, 255, 0.7),
        0 0 60px rgba(0, 123, 255, 0.5);
    }

    /* Counter */
    .counter {
      font-weight: 900;
      font-size: 2.4rem;
      color: #00bfff;
      width: 60px;
      height: 60px;
      background: rgba(0, 191, 255, 0.15);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 0 20px rgba(0, 191, 255, 0.6);
      text-shadow: 0 0 6px rgba(0, 191, 255, 0.9);
      flex-shrink: 0;
      user-select: none;
    }

    /* Name */
    .name {
      font-size: 1.6rem;
      font-weight: 700;
      color: #e0e7ff;
      letter-spacing: 0.03em;
      flex-grow: 1;
      user-select: text;
    }

    /* Status badge with pulse + bounce */
    @keyframes pulseBounce {

      0%,
      100% {
        transform: scale(1);
        box-shadow: 0 0 12px 3px rgba(255 255 255 / 0.3);
        opacity: 1;
      }

      50% {
        transform: scale(1.08);
        box-shadow: 0 0 25px 8px rgba(255 255 255 / 0.55);
        opacity: 0.9;
      }
    }

    .status {
      font-weight: 700;
      font-size: 1.2rem;
      padding: 0.5rem 1.4rem;
      border-radius: 9999px;
      color: white;
      text-transform: uppercase;
      box-shadow: 0 0 15px;
      animation: pulseBounce 3.5s ease-in-out infinite;
      user-select: none;
      flex-shrink: 0;
      letter-spacing: 0.05em;
    }

    .available {
      background: #28a745;
      box-shadow: 0 0 20px #28a745;
    }

    .unavailable {
      background: #dc3545;
      box-shadow: 0 0 20px #dc3545;
    }

    @media (max-width: 768px) {
      .container {
        grid-template-columns: 1fr;
      }
    }

    /* Responsive tweaks */
    @media (max-width: 480px) {
      h1 {
        font-size: 2rem;
        margin-bottom: 2rem;
      }

      .container {
        grid-template-columns: 1fr;
        gap: 1.4rem;
      }

      .card {
        padding: 1.4rem 1.8rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }

      .counter {
        width: 48px;
        height: 48px;
        font-size: 1.6rem;
      }

      .name {
        font-size: 1.3rem;
        margin-bottom: 0.2rem;
      }

      .status {
        font-size: 1rem;
        padding: 0.4rem 1rem;
      }
    }
  </style>
</head>

<body>
  <h1>TrevinosAuto - Salesperson Availability</h1>
  <div class="container">
    @foreach ($users as $index => $user)
    <div class="card" role="region" aria-label="Salesperson {{ $user['name'] }}" style="animation-delay: {{ ($index * 0.2) }}s;">
      <div class="counter" aria-live="polite" aria-atomic="true">{{ $index + 1 }}</div>
      <div class="name">{{ $user['name'] }}</div>
      <div
        class="status {{ $user['status'] === 'Available' ? 'available' : 'unavailable' }}"
        aria-label="Status: {{ $user['status'] }}">
        {{ $user['status'] }}
      </div>
    </div>
    @endforeach
  </div>
</body>

</html>