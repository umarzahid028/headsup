<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Goodwill Repair Waiver</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #444;
            font-size: 24px;
            margin: 0;
            padding: 0;
        }
        .header p {
            color: #777;
            font-size: 14px;
            margin: 5px 0 0;
        }
        .details {
            margin-bottom: 30px;
        }
        .details table {
            width: 100%;
            border-collapse: collapse;
        }
        .details table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .details table td:first-child {
            font-weight: bold;
            width: 30%;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #444;
            font-size: 18px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .signature {
            margin-top: 50px;
            padding: 20px 0;
            border-top: 1px solid #eee;
        }
        .signature img {
            max-width: 300px;
            max-height: 100px;
        }
        .signature-info {
            margin-top: 10px;
            font-size: 12px;
            color: #777;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Goodwill Repair Waiver</h1>
            <p>{{ config('app.name') }} - Waiver ID: {{ $goodwillRepair->id }}</p>
        </div>
        
        <div class="details">
            <table>
                <tr>
                    <td>Customer Name:</td>
                    <td>{{ $goodwillRepair->customer_name }}</td>
                </tr>
                <tr>
                    <td>Vehicle:</td>
                    <td>{{ $goodwillRepair->vehicle->year }} {{ $goodwillRepair->vehicle->make }} {{ $goodwillRepair->vehicle->model }} {{ $goodwillRepair->vehicle->trim }}</td>
                </tr>
                <tr>
                    <td>VIN:</td>
                    <td>{{ $goodwillRepair->vehicle->vin }}</td>
                </tr>
                <tr>
                    <td>Stock Number:</td>
                    <td>{{ $goodwillRepair->vehicle->stock_number }}</td>
                </tr>
                <tr>
                    <td>Repair Title:</td>
                    <td>{{ $goodwillRepair->title }}</td>
                </tr>
                <tr>
                    <td>Description:</td>
                    <td>{{ $goodwillRepair->description }}</td>
                </tr>
                @if($goodwillRepair->cost > 0)
                <tr>
                    <td>Approved Cost:</td>
                    <td>${{ number_format($goodwillRepair->cost, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td>Date:</td>
                    <td>{{ now()->format('F d, Y') }}</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <h2>Terms and Conditions</h2>
            <p>This document serves as an agreement between {{ config('app.name') }} and the customer named above regarding the goodwill repair described herein. By signing this waiver, you acknowledge and agree to the following terms:</p>
            
            <ol>
                <li>The repair described above is being provided as a goodwill gesture by {{ config('app.name') }} and is not an admission of any obligation, liability, or warranty coverage.</li>
                <li>{{ config('app.name') }} agrees to perform or arrange for the described repair at no cost or at the reduced cost specified above to the customer.</li>
                <li>The customer acknowledges that this goodwill repair is a one-time accommodation and does not establish a precedent for future repairs or service.</li>
                <li>The customer releases {{ config('app.name') }} from any and all claims, demands, damages, actions, causes of action, or suits of any kind or nature whatsoever, known or unknown, arising from or in any way related to the vehicle's condition prior to this repair.</li>
                <li>This agreement represents the entire understanding between the parties regarding this specific repair and supersedes any prior discussions or agreements.</li>
            </ol>
        </div>
        
        <div class="signature">
            @if($goodwillRepair->signature_data)
                <h3>Customer Signature:</h3>
                <img src="{{ $goodwillRepair->signature_data }}" alt="Customer Signature">
                <div class="signature-info">
                    <p>Signed by {{ $goodwillRepair->customer_name }} on {{ $goodwillRepair->waiver_signed_at->format('F d, Y \a\t h:i A') }}</p>
                    <p>IP Address: {{ $goodwillRepair->signature_ip }}</p>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>This document was generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
            <p>{{ config('app.name') }} - {{ config('app.url') }}</p>
        </div>
    </div>
</body>
</html>
