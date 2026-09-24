<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABA PayWay Checkout</title>
    <!-- Tailwind CSS CDN for clean styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
        <!-- Header -->
        <div class="bg-[#005a70] px-6 py-5 text-white text-center">
            <h1 class="text-xl font-bold tracking-wide">ABA KHQR Payment</h1>
            <p class="text-xs text-teal-100 mt-1">Scan or tap to complete payment</p>
        </div>

        <div class="p-6 flex flex-col items-center">
            <!-- Amount & Info -->
            <div class="text-center mb-5">
                <span class="text-sm font-medium text-slate-500">Transaction ID</span>
                <p class="font-mono text-xs font-semibold text-slate-700 bg-slate-100 px-2 py-1 rounded mt-0.5">
                    {{ $tran_id }}
                </p>
            </div>

            <!-- QR Code Container -->
            <div class="relative bg-white p-3 rounded-xl border-2 border-dashed border-slate-300 shadow-sm">
                <img 
                    src="{{ $response['qrImage'] }}" 
                    alt="ABA KHQR" 
                    class="w-64 h-64 object-contain rounded-lg"
                />
            </div>

            <p class="text-xs text-slate-400 mt-3 text-center">
                Open your ABA Mobile App (or any Bakong app) to scan.
            </p>

            <!-- Mobile App Deep Link Button -->
            <div class="w-full mt-6">
                <a 
                    href="{{ $response['abapay_deeplink'] }}" 
                    class="w-full inline-flex items-center justify-center gap-2 bg-[#005a70] hover:bg-[#004758] text-white font-medium py-3 px-4 rounded-xl transition duration-150 shadow-md"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                    </svg>
                    <span>Open in ABA Mobile</span>
                </a>
            </div>

            <!-- Status Indicator / Spinner -->
            <div class="mt-6 flex items-center gap-2 text-xs font-medium text-slate-500" id="status-container">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                </span>
                <span id="status-text">Waiting for payment...</span>
            </div>
        </div>
    </div>

    <!-- Polling Script -->
    <script>
        const tranId = "{{ $tran_id }}";
        const checkUrl = "{{ route('payment.check-status', ':id') }}".replace(':id', tranId);

        // Poll every 3 seconds to check if customer approved payment
        const interval = setInterval(async () => {
            try {
                const res = await fetch(checkUrl);
                const data = await res.json();

                // PayWay returns status 0 on successful transaction
                if (data.status === 0 || data.status === "0" || data.status === "00") {
                    clearInterval(interval);
                    document.getElementById('status-text').innerText = "Payment Successful! Redirecting...";
                    document.getElementById('status-text').className = "text-emerald-600 font-bold";
                    
                    setTimeout(() => {
                        window.location.href = "{{ route('payment.success') }}?tran_id=" + tranId;
                    }, 1200);
                }
            } catch (err) {
                console.error("Status check failed", err);
            }
        }, 3000);
    </script>
</body>
</html>