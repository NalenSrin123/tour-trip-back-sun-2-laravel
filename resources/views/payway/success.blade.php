<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --primary: #1f9d67;
            --primary-dark: #167a50;
            --primary-soft: #e9f9f1;
            --text: #122033;
            --muted: #64748b;
            --line: #e5e7eb;
            --shadow: 0 20px 45px rgba(16, 24, 40, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #edf7f2 0%, var(--bg) 100%);
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text);
        }

        .success-shell {
            width: min(100%, 560px);
            padding: 24px;
        }

        .success-card {
            background: var(--card);
            border: 1px solid rgba(31, 157, 103, 0.08);
            border-radius: 24px;
            box-shadow: var(--shadow);
            padding: 40px 32px 32px;
            text-align: center;
        }

        .badge {
            width: 96px;
            height: 96px;
            margin: 0 auto 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #2ec27d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 16px 30px rgba(31, 157, 103, 0.28);
        }

        .checkmark {
            font-size: 48px;
            font-weight: 700;
            color: #fff;
            line-height: 1;
        }

        h1 {
            margin: 0 0 10px;
            font-size: clamp(28px, 3vw, 36px);
            font-weight: 700;
            letter-spacing: -0.04em;
        }

        .subtitle {
            margin: 0 auto 28px;
            max-width: 420px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.6;
        }

        .summary {
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 18px;
            margin-bottom: 28px;
            overflow: hidden;
        }

        .summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 18px;
            font-size: 14px;
            border-bottom: 1px solid var(--line);
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row span {
            color: var(--muted);
        }

        .summary-row strong {
            color: var(--text);
            font-weight: 700;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 170px;
            padding: 14px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            box-shadow: 0 10px 20px rgba(31, 157, 103, 0.22);
        }

        .btn-secondary {
            background: var(--primary-soft);
            color: var(--primary-dark);
            border: 1px solid rgba(31, 157, 103, 0.15);
        }

        .footer-note {
            margin-top: 22px;
            color: var(--muted);
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .success-card {
                padding: 28px 20px 24px;
            }

            .summary-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="success-shell">
        <div class="success-card">
            <div class="badge" aria-label="Success icon">
                <span class="checkmark">✓</span>
            </div>

            <h1>Payment Successful</h1>
            <p class="subtitle">
                Your transaction has been completed successfully. A confirmation has been sent to your email,
                and your booking is now confirmed.
            </p>

            <div class="summary">
                <div class="summary-row">
                    <span>Order number</span>
                    <strong>{{ $orderNumber ?? '#TRIP-2025-091' }}</strong>
                </div>
                <div class="summary-row">
                    <span>Amount paid</span>
                    <strong>{{ $amount ?? '1,250,000' }} VND</strong>
                </div>
                <div class="summary-row">
                    <span>Payment method</span>
                    <strong>{{ $paymentMethod ?? 'Bank transfer' }}</strong>
                </div>
                <div class="summary-row">
                    <span>Date</span>
                    <strong>{{ $paymentDate ?? now()->format('d/m/Y') }}</strong>
                </div>
            </div>

            <div class="actions">
                <a class="btn btn-primary" href="{{ url('/') }}">Back to home</a>
                <a class="btn btn-secondary" href="{{ url('/orders') }}">View order</a>
            </div>

            <div class="footer-note">Thank you for choosing our service.</div>
        </div>
    </div>
</body>
</html>
