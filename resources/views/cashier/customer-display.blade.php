<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Pelanggan — SMEGABIZ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff6b6b;
            --primary-dark: #ee5253;
            --bg-dark: #f0f2f5;
            --bg-card: #ffffff;
            --bg-item: #f8f9fa;
            --text-primary: #1a1a2e;
            --text-secondary: rgba(0, 0, 0, 0.55);
            --accent-green: #27ae60;
            --accent-yellow: #f39c12;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            overflow: hidden;
            cursor: none;
        }

        .display-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 1.5rem;
        }

        /* ===== HEADER ===== */
        .display-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 30px rgba(255, 107, 107, 0.3);
        }

        .display-header .brand {
            font-size: 1.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .display-header .brand i {
            font-size: 1.8rem;
        }

        .display-header .clock {
            font-size: 1.3rem;
            font-weight: 600;
            opacity: 0.9;
        }

        /* ===== MAIN CONTENT ===== */
        .display-main {
            flex: 1;
            display: flex;
            gap: 1.5rem;
            overflow: hidden;
        }

        /* ===== CART LIST (Left Side) ===== */
        .cart-panel {
            flex: 1;
            background: var(--bg-card);
            border-radius: 1rem;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .cart-panel-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-panel-header h3 {
            font-weight: 700;
            font-size: 1.2rem;
            margin: 0;
        }

        .cart-panel-header .item-count {
            background: var(--primary);
            padding: 0.3rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .cart-list {
            flex: 1;
            overflow-y: auto;
            padding: 0.75rem 1rem;
        }

        .cart-list::-webkit-scrollbar {
            width: 4px;
        }
        .cart-list::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.85rem 1rem;
            background: var(--bg-item);
            border-radius: 0.75rem;
            margin-bottom: 0.5rem;
            animation: slideIn 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 0.2rem;
        }

        .cart-item-price {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .cart-item-qty {
            background: var(--primary);
            color: white;
            font-weight: 700;
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin: 0 1rem;
            flex-shrink: 0;
        }

        .cart-item-subtotal {
            font-weight: 700;
            font-size: 1.1rem;
            text-align: right;
            min-width: 130px;
        }

        /* ===== TOTAL PANEL (Right Side) ===== */
        .total-panel {
            width: 340px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            flex-shrink: 0;
        }

        .total-card {
            background: var(--bg-card);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .total-card .label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .total-card .value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--accent-green);
        }

        .total-card.grand-total {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 8px 30px rgba(255, 107, 107, 0.3);
        }

        .total-card.grand-total .label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
        }

        .total-card.grand-total .value {
            color: white;
            font-size: 3rem;
        }

        /* ===== STATUS OVERLAY ===== */
        .status-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.92);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
            gap: 1.5rem;
        }

        .status-overlay.active {
            display: flex;
        }

        .status-overlay .status-icon {
            font-size: 5rem;
            animation: pulse 1.5s infinite;
        }

        .status-overlay .status-text {
            font-size: 2.5rem;
            font-weight: 800;
            text-align: center;
        }

        .status-overlay .status-sub {
            font-size: 1.2rem;
            color: var(--text-secondary);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--text-secondary);
            gap: 1rem;
        }

        .empty-state i {
            font-size: 5rem;
            opacity: 0.3;
        }

        .empty-state p {
            font-size: 1.3rem;
            font-weight: 500;
        }

        /* ===== THANK YOU SCREEN ===== */
        .thank-you {
            color: var(--accent-green);
        }

        .thank-you .status-icon {
            animation: bounceIn 0.6s ease;
        }

        @keyframes bounceIn {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }

        /* ===== RESPONSIVE ===== */

        /* Tablet Landscape */
        @media (max-width: 992px) {
            .display-container {
                padding: 1rem;
            }
            .total-panel {
                width: 280px;
            }
            .total-card .value {
                font-size: 2rem;
            }
            .total-card.grand-total .value {
                font-size: 2.5rem;
            }
            .cart-item-name {
                font-size: 0.95rem;
            }
            .cart-item-subtotal {
                font-size: 1rem;
                min-width: 110px;
            }
        }

        /* Tablet Portrait */
        @media (max-width: 768px) {
            body {
                overflow: auto;
                cursor: default;
            }
            .display-container {
                height: auto;
                min-height: 100vh;
                padding: 0.75rem;
            }
            .display-header {
                padding: 0.75rem 1rem;
                border-radius: 0.75rem;
                margin-bottom: 0.75rem;
            }
            .display-header .brand {
                font-size: 1.15rem;
            }
            .display-header .brand i {
                font-size: 1.4rem;
            }
            .display-header .clock {
                font-size: 1rem;
            }

            /* Stack layout vertically */
            .display-main {
                flex-direction: column;
                overflow: visible;
            }
            .total-panel {
                width: 100%;
                flex-direction: row;
                gap: 0.75rem;
                order: -1; /* Total on top */
            }
            .total-card {
                flex: 1;
                padding: 1rem;
                border-radius: 0.75rem;
            }
            .total-card .label {
                font-size: 0.75rem;
                margin-bottom: 0.25rem;
            }
            .total-card .value {
                font-size: 1.5rem;
            }
            .total-card.grand-total {
                flex: 2;
            }
            .total-card.grand-total .value {
                font-size: 1.8rem;
            }
            .total-card.grand-total .label {
                font-size: 0.8rem;
            }

            /* Cart panel */
            .cart-panel {
                border-radius: 0.75rem;
                min-height: 300px;
            }
            .cart-panel-header {
                padding: 0.75rem 1rem;
            }
            .cart-panel-header h3 {
                font-size: 1rem;
            }
            .cart-item {
                padding: 0.65rem 0.75rem;
            }
            .cart-item-name {
                font-size: 0.88rem;
            }
            .cart-item-price {
                font-size: 0.75rem;
            }
            .cart-item-qty {
                width: 34px;
                height: 34px;
                font-size: 0.95rem;
                margin: 0 0.6rem;
            }
            .cart-item-subtotal {
                font-size: 0.9rem;
                min-width: 100px;
            }

            /* Empty state */
            .empty-state i {
                font-size: 3.5rem;
            }
            .empty-state p {
                font-size: 1.1rem;
            }

            /* Status overlay */
            .status-overlay .status-icon {
                font-size: 3.5rem;
            }
            .status-overlay .status-text {
                font-size: 1.8rem;
            }
            .status-overlay .status-sub {
                font-size: 1rem;
            }
        }

        /* Mobile */
        @media (max-width: 576px) {
            .display-container {
                padding: 0.5rem;
            }
            .display-header {
                padding: 0.6rem 0.75rem;
                border-radius: 0.6rem;
                margin-bottom: 0.5rem;
            }
            .display-header .brand {
                font-size: 1rem;
                gap: 0.5rem;
            }
            .display-header .brand i {
                font-size: 1.2rem;
            }
            .display-header .clock {
                font-size: 0.85rem;
            }

            .display-main {
                gap: 0.5rem;
            }

            /* Total panel compact row */
            .total-panel {
                gap: 0.5rem;
            }
            .total-card {
                padding: 0.6rem;
            }
            .total-card .label {
                font-size: 0.65rem;
                letter-spacing: 0.5px;
            }
            .total-card .value {
                font-size: 1.2rem;
            }
            .total-card.grand-total .value {
                font-size: 1.4rem;
            }
            .total-card.grand-total .label {
                font-size: 0.7rem;
            }

            /* Cart items */
            .cart-panel {
                border-radius: 0.6rem;
                min-height: 250px;
            }
            .cart-panel-header {
                padding: 0.6rem 0.75rem;
            }
            .cart-panel-header h3 {
                font-size: 0.88rem;
            }
            .cart-panel-header .item-count {
                font-size: 0.7rem;
                padding: 0.2rem 0.6rem;
            }
            .cart-item {
                padding: 0.5rem 0.6rem;
                border-radius: 0.5rem;
                margin-bottom: 0.35rem;
            }
            .cart-item-name {
                font-size: 0.8rem;
            }
            .cart-item-price {
                font-size: 0.68rem;
            }
            .cart-item-qty {
                width: 28px;
                height: 28px;
                font-size: 0.8rem;
                margin: 0 0.4rem;
                border-radius: 0.35rem;
            }
            .cart-item-subtotal {
                font-size: 0.8rem;
                min-width: 80px;
            }

            /* Empty state */
            .empty-state i {
                font-size: 2.5rem;
            }
            .empty-state p {
                font-size: 0.95rem;
            }

            /* Status overlay */
            .status-overlay .status-icon {
                font-size: 2.5rem;
            }
            .status-overlay .status-text {
                font-size: 1.3rem;
                padding: 0 1rem;
            }
            .status-overlay .status-sub {
                font-size: 0.8rem;
                padding: 0 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="display-container">
        <!-- Header -->
        <div class="display-header">
            <div class="brand">
                <i class="bi bi-shop"></i>
                SMEGABIZ
            </div>
            <div class="clock" id="clock">--:--:--</div>
        </div>

        <!-- Main Content -->
        <div class="display-main" id="mainDisplay">
            <!-- Cart List -->
            <div class="cart-panel">
                <div class="cart-panel-header">
                    <h3><i class="bi bi-cart3 me-2"></i>Keranjang Belanja</h3>
                    <span class="item-count" id="itemCount">0 item</span>
                </div>
                <div class="cart-list" id="cartList">
                    <div class="empty-state" id="emptyState">
                        <i class="bi bi-cart-x"></i>
                        <p>Belum ada barang</p>
                        <span style="font-size: 0.9rem;">Silakan serahkan barang belanjaan Anda ke kasir</span>
                    </div>
                </div>
            </div>

            <!-- Total Panel -->
            <div class="total-panel">
                <div class="total-card">
                    <div class="label">Total Item</div>
                    <div class="value" id="totalItems">0</div>
                </div>
                <div class="total-card grand-total">
                    <div class="label">Total Belanja</div>
                    <div class="value" id="grandTotal">Rp 0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Overlay (Processing / Thank You) -->
    <div class="status-overlay" id="statusOverlay">
        <div class="status-icon" id="statusIcon"></div>
        <div class="status-text" id="statusText"></div>
        <div class="status-sub" id="statusSub"></div>
    </div>

    <script>
        // ===== Clock =====
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ===== Format Currency =====
        function formatRupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }

        // ===== Render Cart =====
        function renderCart(data) {
            const cartList = document.getElementById('cartList');
            const emptyState = document.getElementById('emptyState');
            const itemCount = document.getElementById('itemCount');
            const totalItems = document.getElementById('totalItems');
            const grandTotal = document.getElementById('grandTotal');

            const items = data.items || [];
            const total = data.total || 0;

            if (items.length === 0) {
                cartList.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-cart-x"></i>
                        <p>Belum ada barang</p>
                        <span style="font-size: 0.9rem;">Silakan serahkan barang belanjaan Anda ke kasir</span>
                    </div>
                `;
                itemCount.textContent = '0 item';
                totalItems.textContent = '0';
                grandTotal.textContent = 'Rp 0';
                return;
            }

            let totalQty = 0;
            let html = '';

            items.forEach((item, index) => {
                totalQty += item.qty;
                const subtotal = item.price * item.qty;
                html += `
                    <div class="cart-item" style="animation-delay: ${index * 0.05}s">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">${formatRupiah(item.price)} / pcs</div>
                        </div>
                        <div class="cart-item-qty">${item.qty}</div>
                        <div class="cart-item-subtotal">${formatRupiah(subtotal)}</div>
                    </div>
                `;
            });

            cartList.innerHTML = html;
            itemCount.textContent = items.length + ' item';
            totalItems.textContent = totalQty;
            grandTotal.textContent = formatRupiah(total);

            // Auto-scroll to bottom
            cartList.scrollTop = cartList.scrollHeight;
        }

        // ===== Show Status Overlay =====
        function showStatus(type, persistent = false) {
            const overlay = document.getElementById('statusOverlay');
            const icon = document.getElementById('statusIcon');
            const text = document.getElementById('statusText');
            const sub = document.getElementById('statusSub');

            if (type === 'processing') {
                overlay.className = 'status-overlay active';
                icon.innerHTML = '<i class="bi bi-hourglass-split text-warning"></i>';
                text.textContent = 'Memproses Pembayaran...';
                sub.textContent = 'Mohon tunggu sebentar';
            } else if (type === 'success') {
                overlay.className = 'status-overlay active thank-you';
                icon.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
                text.textContent = 'Terima Kasih!';
                sub.textContent = 'Pembayaran berhasil • Selamat berbelanja kembali';

                if (!persistent) {
                    // Auto-hide after 5 seconds
                    setTimeout(() => {
                        overlay.className = 'status-overlay';
                        renderCart({ items: [], total: 0 }); // Clear cart after success
                    }, 5000);
                }
            } else {
                overlay.className = 'status-overlay';
            }
        }

        // ===== BroadcastChannel Receiver =====
        const channel = new BroadcastChannel('kasirku-customer-display');

        channel.onmessage = function(event) {
            const data = event.data;

            if (data.type === 'cart-update') {
                showStatus(''); // hide any overlay
                renderCart(data);
            } else if (data.type === 'checkout-processing') {
                showStatus('processing');
            } else if (data.type === 'checkout-success') {
                // Initial success message (auto-hide)
                showStatus('success'); 
            } else if (data.type === 'show-thank-you') {
                // Persistent success message (when receipt is shown)
                showStatus('success', true);
            } else if (data.type === 'reset-display') {
                // Hide overlay and clear cart
                showStatus('');
                renderCart({ items: [], total: 0 });
            }
        };

        // ===== Initial Render from Server Data =====
        const initialCart = @json($cart ?? []);
        const initialTotal = {{ $total ?? 0 }};

        const initialItems = Object.values(initialCart).map(item => ({
            name: item.name,
            price: item.price,
            qty: item.qty
        }));

        renderCart({ items: initialItems, total: initialTotal });
    </script>
</body>
</html>
