@extends('layouts.app')

@section('title', 'Tanya Toko AI — SMEGABIZ')

@section('content')
<style>
    .ai-chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 140px);
        max-height: 800px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    /* Header */
    .ai-chat-header {
        background: linear-gradient(135deg, #cc0000 0%, #ff2222 100%);
        color: white;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-shrink: 0;
    }

    .ai-chat-header .ai-avatar {
        width: 48px;
        height: 48px;
        background: rgba(255,255,255,0.2);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .ai-chat-header .ai-info h4 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .ai-chat-header .ai-info p {
        margin: 0;
        font-size: 0.8rem;
        opacity: 0.85;
    }

    .ai-chat-header .ai-status {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        opacity: 0.9;
    }

    .ai-chat-header .ai-status .dot {
        width: 8px;
        height: 8px;
        background: #48bb78;
        border-radius: 50%;
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* Messages Area */
    .ai-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        background: #f8f9fa;
    }

    .ai-chat-messages::-webkit-scrollbar {
        width: 5px;
    }
    .ai-chat-messages::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    /* Message Bubbles */
    .chat-msg {
        display: flex;
        gap: 10px;
        max-width: 85%;
        animation: fadeInUp 0.3s ease;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-msg.ai {
        align-self: flex-start;
    }

    .chat-msg.user {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .chat-msg .msg-avatar {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .chat-msg.ai .msg-avatar {
        background: linear-gradient(135deg, #cc0000, #ff4444);
        color: white;
    }

    .chat-msg.user .msg-avatar {
        background: #e9ecef;
        color: #495057;
    }

    .chat-msg .msg-bubble {
        padding: 12px 16px;
        border-radius: 14px;
        font-size: 0.9rem;
        line-height: 1.6;
        word-wrap: break-word;
    }

    .chat-msg.ai .msg-bubble {
        background: #ffffff;
        color: #333;
        border: 1px solid #e9ecef;
        border-top-left-radius: 4px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    .chat-msg.user .msg-bubble {
        background: linear-gradient(135deg, #cc0000, #ff2222);
        color: white;
        border-top-right-radius: 4px;
    }

    /* Markdown content in AI messages */
    .chat-msg.ai .msg-bubble h1,
    .chat-msg.ai .msg-bubble h2,
    .chat-msg.ai .msg-bubble h3 {
        font-size: 1rem;
        font-weight: 700;
        margin: 10px 0 6px 0;
        color: #222;
    }

    .chat-msg.ai .msg-bubble h1:first-child,
    .chat-msg.ai .msg-bubble h2:first-child,
    .chat-msg.ai .msg-bubble h3:first-child {
        margin-top: 0;
    }

    .chat-msg.ai .msg-bubble ul,
    .chat-msg.ai .msg-bubble ol {
        margin: 6px 0;
        padding-left: 20px;
    }

    .chat-msg.ai .msg-bubble li {
        margin-bottom: 4px;
    }

    .chat-msg.ai .msg-bubble strong {
        color: #cc0000;
    }

    .chat-msg.ai .msg-bubble code {
        background: #f1f3f4;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.85em;
        color: #d63384;
    }

    .chat-msg.ai .msg-bubble table {
        width: 100%;
        border-collapse: collapse;
        margin: 8px 0;
        font-size: 0.85rem;
    }

    .chat-msg.ai .msg-bubble table th,
    .chat-msg.ai .msg-bubble table td {
        border: 1px solid #dee2e6;
        padding: 6px 10px;
        text-align: left;
    }

    .chat-msg.ai .msg-bubble table th {
        background: #f8f9fa;
        font-weight: 600;
    }

    .chat-msg.ai .msg-bubble p {
        margin: 6px 0;
    }

    .chat-msg.ai .msg-bubble p:first-child {
        margin-top: 0;
    }

    .chat-msg.ai .msg-bubble p:last-child {
        margin-bottom: 0;
    }

    /* Typing Indicator */
    .typing-indicator {
        display: flex;
        gap: 5px;
        padding: 8px 0;
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        background: #adb5bd;
        border-radius: 50%;
        animation: bounce 1.4s infinite ease-in-out both;
    }

    .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
    .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    /* Welcome State */
    .welcome-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 30px;
        gap: 16px;
    }

    .welcome-state .welcome-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #cc0000, #ff4444);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        box-shadow: 0 6px 20px rgba(204, 0, 0, 0.25);
    }

    .welcome-state h3 {
        font-weight: 700;
        color: #333;
        margin: 0;
    }

    .welcome-state p {
        color: #6c757d;
        max-width: 400px;
        font-size: 0.9rem;
        margin: 0;
    }

    /* Quick Questions */
    .quick-questions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        max-width: 600px;
        margin-top: 8px;
    }

    .quick-q {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 0.82rem;
        color: #495057;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .quick-q:hover {
        background: #cc0000;
        color: white;
        border-color: #cc0000;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(204,0,0,0.2);
    }

    /* Input Area */
    .ai-chat-input {
        padding: 16px 20px;
        background: #ffffff;
        border-top: 1px solid #eee;
        flex-shrink: 0;
    }

    .input-wrapper {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .input-wrapper input {
        flex: 1;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .input-wrapper input:focus {
        border-color: #cc0000;
    }

    .input-wrapper input::placeholder {
        color: #adb5bd;
    }

    .send-btn {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, #cc0000, #ff2222);
        border: none;
        color: white;
        font-size: 1.1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(204,0,0,0.3);
    }

    .send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    /* Clear Chat */
    .clear-chat-btn {
        background: none;
        border: none;
        color: rgba(255,255,255,0.7);
        font-size: 0.75rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .clear-chat-btn:hover {
        color: white;
        background: rgba(255,255,255,0.15);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .ai-chat-container {
            height: calc(100vh - 100px);
            border-radius: 12px;
        }

        .ai-chat-header {
            padding: 14px 16px;
        }

        .ai-chat-header .ai-avatar {
            width: 38px;
            height: 38px;
            font-size: 1.2rem;
        }

        .ai-chat-messages {
            padding: 14px 12px;
        }

        .chat-msg {
            max-width: 92%;
        }

        .quick-q {
            font-size: 0.75rem;
            padding: 6px 12px;
        }

        .ai-chat-input {
            padding: 10px 12px;
        }
    }
</style>

<div class="ai-chat-container">
    {{-- Header --}}
    <div class="ai-chat-header">
        <div class="ai-avatar">
            <i class="bi bi-robot"></i>
        </div>
        <div class="ai-info">
            <h4>Tanya Toko AI</h4>
            <p>Asisten pintar untuk analisis bisnis Anda</p>
        </div>
        <div class="ai-status">
            <span class="dot"></span> Online
        </div>
        <button class="clear-chat-btn" id="clearChatBtn" title="Hapus Percakapan" style="display:none;">
            <i class="bi bi-trash3"></i> Hapus
        </button>
    </div>

    {{-- Messages Area --}}
    <div class="ai-chat-messages" id="chatMessages">
        {{-- Welcome State --}}
        <div class="welcome-state" id="welcomeState">
            <div class="welcome-icon">
                <i class="bi bi-robot"></i>
            </div>
            <h3>Halo, {{ auth()->user()->name }}! 👋</h3>
            <p>Saya asisten AI toko Anda. Tanyakan apa saja tentang performa penjualan, stok, atau analisis bisnis.</p>
            <div class="quick-questions">
                <button class="quick-q" data-q="Berapa total penjualan hari ini?">📊 Penjualan Hari Ini</button>
                <button class="quick-q" data-q="Apa 5 produk paling laris bulan ini?">🏆 Produk Terlaris</button>
                <button class="quick-q" data-q="Barang apa saja yang stoknya hampir habis?">📦 Stok Rendah</button>
                <button class="quick-q" data-q="Buatkan ringkasan performa toko minggu ini">📋 Ringkasan Minggu Ini</button>
                <button class="quick-q" data-q="Kategori mana yang paling menguntungkan bulan ini?">💰 Kategori Top</button>
                <button class="quick-q" data-q="Berapa laba bersih bulan ini dan bandingkan dengan minggu ini?">📈 Analisis Laba</button>
            </div>
        </div>
    </div>

    {{-- Input Area --}}
    <div class="ai-chat-input">
        <div class="input-wrapper">
            <input type="text" id="chatInput" placeholder="Tanya sesuatu tentang toko Anda..." autocomplete="off" maxlength="1000">
            <button class="send-btn" id="sendBtn" title="Kirim">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const messagesEl  = document.getElementById('chatMessages');
    const welcomeEl   = document.getElementById('welcomeState');
    const inputEl     = document.getElementById('chatInput');
    const sendBtn     = document.getElementById('sendBtn');
    const clearBtn    = document.getElementById('clearChatBtn');
    const csrfToken   = '{{ csrf_token() }}';
    const askUrl      = '{{ route("ai-chat.ask") }}';

    let conversationHistory = [];
    let isLoading = false;

    // --- Markdown Parser (Lightweight) ---
    function parseMarkdown(text) {
        // Escape HTML
        let html = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        // Code blocks
        html = html.replace(/```(\w*)\n?([\s\S]*?)```/g, '<pre><code>$2</code></pre>');

        // Inline code
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');

        // Headers
        html = html.replace(/^### (.+)$/gm, '<h3>$1</h3>');
        html = html.replace(/^## (.+)$/gm, '<h2>$1</h2>');
        html = html.replace(/^# (.+)$/gm, '<h1>$1</h1>');

        // Bold + Italic
        html = html.replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>');
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');

        // Tables
        html = html.replace(/^\|(.+)\|$/gm, function(match, content) {
            const cells = content.split('|').map(c => c.trim());
            return '<tr>' + cells.map(c => {
                if (/^[-:]+$/.test(c)) return null;
                return `<td>${c}</td>`;
            }).filter(Boolean).join('') + '</tr>';
        });
        html = html.replace(/(<tr>.*<\/tr>\n?)+/g, function(match) {
            const rows = match.trim();
            // Attempt to detect header row (first row becomes th)
            const firstRowEnd = rows.indexOf('</tr>') + 5;
            const headerRow = rows.substring(0, firstRowEnd).replace(/<td>/g, '<th>').replace(/<\/td>/g, '</th>');
            let rest = rows.substring(firstRowEnd);
            // Remove separator row if exists
            rest = rest.replace(/<tr><\/tr>/g, '');
            return `<table>${headerRow}${rest}</table>`;
        });

        // Unordered lists
        html = html.replace(/^[\-\*] (.+)$/gm, '<li>$1</li>');
        html = html.replace(/(<li>.*<\/li>\n?)+/g, '<ul>$&</ul>');

        // Ordered lists
        html = html.replace(/^\d+\. (.+)$/gm, '<li>$1</li>');

        // Horizontal rule
        html = html.replace(/^---$/gm, '<hr>');

        // Line breaks → paragraphs
        html = html.replace(/\n\n/g, '</p><p>');
        html = html.replace(/\n/g, '<br>');

        // Wrap in paragraph if not starting with a block element
        if (!html.startsWith('<')) {
            html = '<p>' + html + '</p>';
        }

        return html;
    }

    // --- Add Message to Chat ---
    function addMessage(role, text) {
        welcomeEl.style.display = 'none';
        clearBtn.style.display = 'flex';

        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-msg ${role}`;

        const avatarIcon = role === 'ai' ? 'bi-robot' : 'bi-person-fill';
        const content = role === 'ai' ? parseMarkdown(text) : text.replace(/</g, '&lt;').replace(/>/g, '&gt;');

        msgDiv.innerHTML = `
            <div class="msg-avatar"><i class="bi ${avatarIcon}"></i></div>
            <div class="msg-bubble">${content}</div>
        `;

        messagesEl.appendChild(msgDiv);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    // --- Typing Indicator ---
    function showTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-msg ai';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="msg-avatar"><i class="bi bi-robot"></i></div>
            <div class="msg-bubble">
                <div class="typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
        messagesEl.appendChild(typingDiv);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function hideTyping() {
        const el = document.getElementById('typingIndicator');
        if (el) el.remove();
    }

    // --- Send Message ---
    async function sendMessage(message) {
        if (!message.trim() || isLoading) return;

        isLoading = true;
        sendBtn.disabled = true;
        inputEl.value = '';

        addMessage('user', message);
        showTyping();

        try {
            const res = await fetch(askUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message: message,
                    history: conversationHistory,
                }),
            });

            const data = await res.json();
            hideTyping();

            const reply = data.reply || 'Maaf, terjadi kesalahan.';
            addMessage('ai', reply);

            // Save to conversation history
            conversationHistory.push({ role: 'user', text: message });
            conversationHistory.push({ role: 'model', text: reply });

            // Keep history manageable (last 10 exchanges)
            if (conversationHistory.length > 20) {
                conversationHistory = conversationHistory.slice(-20);
            }

        } catch (err) {
            hideTyping();
            addMessage('ai', '❌ Gagal mengirim pesan. Pastikan server berjalan dan coba lagi.');
            console.error('AI Chat Error:', err);
        } finally {
            isLoading = false;
            sendBtn.disabled = false;
            inputEl.focus();
        }
    }

    // --- Event Listeners ---
    sendBtn.addEventListener('click', () => sendMessage(inputEl.value));

    inputEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage(inputEl.value);
        }
    });

    // Quick Questions
    document.querySelectorAll('.quick-q').forEach(btn => {
        btn.addEventListener('click', () => {
            sendMessage(btn.dataset.q);
        });
    });

    // Clear Chat
    clearBtn.addEventListener('click', () => {
        Swal.fire({
            title: 'Hapus Percakapan?',
            text: 'Semua riwayat chat akan dihapus.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#cc0000',
            cancelButtonColor: '#adb5bd',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                conversationHistory = [];
                messagesEl.querySelectorAll('.chat-msg').forEach(el => el.remove());
                welcomeEl.style.display = 'flex';
                clearBtn.style.display = 'none';
            }
        });
    });

    // Auto-focus
    inputEl.focus();
})();
</script>
@endpush
