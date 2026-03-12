<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AI Chat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:          #0c0c0e;
            --bg2:         #111114;
            --surface:     #17171b;
            --surface2:    #1e1e24;
            --border:      rgba(255,255,255,0.06);
            --border2:     rgba(255,255,255,0.1);
            --text:        #e2e2e8;
            --text-sub:    #8888a0;
            --text-dim:    #44445a;
            --accent:      #00d4aa;
            --accent-dim:  rgba(0,212,170,0.1);
            --accent-glow: rgba(0,212,170,0.2);
            --user-bubble: #1a1a24;
            --bot-bubble:  #141418;
            --font:        'Sora', sans-serif;
            --mono:        'JetBrains Mono', monospace;
            --sidebar:     240px;
        }

        html, body { height: 100%; overflow: hidden; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font);
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ═══ LAYOUT */
        .shell { display: flex; height: 100vh; }

        /* ═══ SIDEBAR */
        .sidebar {
            width: var(--sidebar);
            min-width: var(--sidebar);
            background: var(--bg2);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: width 0.25s ease, min-width 0.25s ease, border 0.25s ease;
            overflow: hidden;
            z-index: 20;
        }
        .sidebar.collapsed { width: 0; min-width: 0; border-right-color: transparent; }

        .sb-top {
            padding: 18px 14px 14px;
            display: flex; align-items: center; gap: 10px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        .brand-icon {
            width: 30px; height: 30px; flex-shrink: 0;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 18px var(--accent-glow);
        }
        .brand-icon svg { width: 15px; height: 15px; }
        .brand-name { font-size: 14px; font-weight: 600; letter-spacing: -0.2px; }

        .new-btn {
            margin: 10px;
            padding: 8px 12px;
            background: transparent;
            border: 1px solid var(--border2);
            border-radius: 8px;
            color: var(--text-sub);
            font-family: var(--font);
            font-size: 12.5px;
            font-weight: 500;
            cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            transition: all 0.15s;
            white-space: nowrap;
            width: calc(100% - 20px);
        }
        .new-btn:hover { background: var(--surface2); color: var(--text); border-color: var(--accent); }

        .hist-label {
            padding: 10px 14px 4px;
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-dim); white-space: nowrap;
        }
        .hist-list {
            flex: 1; overflow-y: auto; padding: 4px 8px 10px;
            scrollbar-width: none;
        }
        .hist-list::-webkit-scrollbar { display: none; }

        .hist-item {
            padding: 8px 10px; border-radius: 7px; cursor: pointer;
            font-size: 12.5px; color: var(--text-sub);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            display: flex; align-items: center; gap: 8px;
            transition: all 0.1s;
        }
        .hist-item:hover { background: var(--surface2); color: var(--text); }
        .hist-item.active { background: var(--surface2); color: var(--text); }
        .hist-item svg { opacity: 0.4; flex-shrink: 0; }

        .sb-footer {
            padding: 10px;
            border-top: 1px solid var(--border);
        }
        .user-row {
            display: flex; align-items: center; gap: 10px;
            padding: 7px 9px; border-radius: 7px; cursor: pointer;
            transition: background 0.1s; white-space: nowrap;
        }
        .user-row:hover { background: var(--surface2); }
        .user-av {
            width: 28px; height: 28px; flex-shrink: 0;
            border-radius: 50%;
            background: var(--accent-dim);
            border: 1px solid var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 600; color: var(--accent);
        }
        .user-meta strong { display: block; font-size: 12.5px; font-weight: 500; }
        .user-meta span   { font-size: 11px; color: var(--text-dim); }

        /* ═══ MAIN */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        /* topbar */
        .topbar {
            height: 52px; padding: 0 18px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--border); flex-shrink: 0; gap: 12px;
        }
        .tb-l { display: flex; align-items: center; gap: 10px; }

        .icon-btn {
            width: 32px; height: 32px;
            background: none; border: 1px solid var(--border); border-radius: 7px;
            cursor: pointer; color: var(--text-sub);
            display: flex; align-items: center; justify-content: center;
            transition: all 0.12s;
        }
        .icon-btn:hover { background: var(--surface2); color: var(--text); border-color: var(--border2); }

        .model-pill {
            display: flex; align-items: center; gap: 7px;
            padding: 5px 12px;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: 20px;
            font-size: 12.5px; font-weight: 500; color: var(--text);
            cursor: pointer; user-select: none;
            transition: border-color 0.15s;
        }
        .model-pill:hover { border-color: var(--accent); }
        .dot-live {
            width: 6px; height: 6px; flex-shrink: 0;
            background: var(--accent); border-radius: 50%;
            animation: blink 2s infinite;
        }
        @keyframes blink {
            0%,100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .tb-r { display: flex; gap: 6px; }

        /* messages */
        .msg-scroll {
            flex: 1; overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--surface2) transparent;
        }
        .msg-scroll::-webkit-scrollbar { width: 4px; }
        .msg-scroll::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }

        .msg-inner {
            max-width: 700px; margin: 0 auto;
            padding: 36px 22px 16px;
        }

        /* empty state */
        .empty {
            display: flex; flex-direction: column;
            align-items: center; text-align: center;
            padding: 64px 20px 40px;
            animation: rise .5s cubic-bezier(.4,0,.2,1) both;
        }
        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .empty-icon {
            width: 58px; height: 58px;
            background: var(--accent-dim);
            border: 1px solid rgba(0,212,170,0.18);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 0 40px var(--accent-glow);
        }
        .empty-icon svg { width: 26px; height: 26px; }
        .empty h1 { font-size: 21px; font-weight: 600; letter-spacing: -.4px; margin-bottom: 8px; }
        .empty p  { font-size: 13.5px; color: var(--text-sub); max-width: 300px; margin-bottom: 34px; }

        .chips {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
            width: 100%; max-width: 480px;
        }
        .chip {
            background: var(--surface); border: 1px solid var(--border2);
            border-radius: 12px; padding: 14px 15px;
            text-align: left; cursor: pointer; font-family: var(--font);
            transition: all 0.15s;
        }
        .chip:hover { border-color: var(--accent); background: var(--accent-dim); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.3); }
        .chip-ic    { font-size: 18px; margin-bottom: 7px; display: block; }
        .chip-t     { font-size: 12.5px; font-weight: 600; color: var(--text); margin-bottom: 3px; }
        .chip-d     { font-size: 11.5px; color: var(--text-sub); }

        /* message groups */
        .msg-grp { margin-bottom: 22px; animation: rise .25s ease both; }

        .msg-row { display: flex; gap: 12px; align-items: flex-start; }
        .msg-row.is-user { flex-direction: row-reverse; }

        .av {
            width: 29px; height: 29px; flex-shrink: 0;
            border-radius: 8px; margin-top: 1px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 600;
        }
        .av-bot  { background: var(--accent); color: #000; box-shadow: 0 0 12px var(--accent-glow); }
        .av-user { background: var(--surface2); border: 1px solid var(--border2); color: var(--text-sub); }

        .msg-body { flex: 1; max-width: 85%; }
        .is-user .msg-body { display: flex; flex-direction: column; align-items: flex-end; }

        .msg-who {
            font-size: 11px; font-weight: 600; color: var(--text-dim);
            margin-bottom: 5px; padding-left: 2px;
        }
        .is-user .msg-who { padding-right: 2px; }

        .bubble {
            padding: 12px 15px; border-radius: 14px;
            font-size: 14px; line-height: 1.65;
            word-break: break-word;
        }
        .b-bot  { background: var(--bot-bubble);  border: 1px solid var(--border2); border-radius: 14px 14px 14px 3px; }
        .b-user { background: var(--user-bubble); border: 1px solid var(--border2); border-radius: 14px 14px 3px 14px; }

        .bubble pre {
            background: rgba(0,0,0,.35);
            border: 1px solid var(--border2);
            border-left: 3px solid var(--accent);
            border-radius: 0 8px 8px 0;
            padding: 11px 13px; margin: 10px 0;
            overflow-x: auto;
            font-family: var(--mono); font-size: 12.5px; line-height: 1.5;
        }
        .bubble code:not(pre code) {
            background: rgba(0,212,170,.1);
            border: 1px solid rgba(0,212,170,.2);
            border-radius: 4px; padding: 1px 5px;
            font-family: var(--mono); font-size: 12.5px; color: var(--accent);
        }
        .bubble strong { color: #fff; font-weight: 600; }
        .bubble em { color: var(--text-sub); }

        .msg-acts {
            display: flex; gap: 5px; margin-top: 6px;
            opacity: 0; transition: opacity .15s;
        }
        .msg-grp:hover .msg-acts { opacity: 1; }

        .act {
            padding: 3px 8px;
            background: none; border: 1px solid var(--border); border-radius: 5px;
            font-size: 11px; color: var(--text-dim); cursor: pointer;
            font-family: var(--font);
            display: flex; align-items: center; gap: 4px;
            transition: all .1s;
        }
        .act:hover { background: var(--surface2); color: var(--text-sub); border-color: var(--border2); }

        /* typing */
        #typingRow {
            display: none; align-items: flex-start; gap: 12px;
            margin-bottom: 18px; animation: rise .25s ease both;
        }
        #typingRow.on { display: flex; }
        .typing-bub {
            background: var(--bot-bubble); border: 1px solid var(--border2);
            border-radius: 14px 14px 14px 3px;
            padding: 14px 17px; display: flex; gap: 5px; align-items: center;
        }
        .td {
            width: 7px; height: 7px;
            background: var(--text-dim); border-radius: 50%;
            animation: bob 1.2s infinite ease-in-out;
        }
        .td:nth-child(2) { animation-delay: .16s; }
        .td:nth-child(3) { animation-delay: .32s; }
        @keyframes bob {
            0%,60%,100% { transform: translateY(0); opacity: .35; }
            30% { transform: translateY(-6px); opacity: 1; }
        }

        /* input */
        .input-zone {
            flex-shrink: 0; padding: 14px 18px 18px;
            border-top: 1px solid var(--border);
        }
        .input-inner { max-width: 700px; margin: 0 auto; }

        .composer {
            background: var(--surface); border: 1px solid var(--border2);
            border-radius: 16px; padding: 10px 10px 10px 15px;
            display: flex; align-items: flex-end; gap: 10px;
            transition: border-color .2s, box-shadow .2s;
        }
        .composer:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-dim); }

        #message {
            flex: 1; background: none; border: none; outline: none;
            font-family: var(--font); font-size: 14px; color: var(--text);
            line-height: 1.6; resize: none; max-height: 160px; overflow-y: auto;
            padding: 5px 0; scrollbar-width: none;
        }
        #message::-webkit-scrollbar { display: none; }
        #message::placeholder { color: var(--text-dim); }

        .send-btn {
            width: 36px; height: 36px; flex-shrink: 0;
            background: var(--accent); border: none; border-radius: 10px;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all .15s; box-shadow: 0 2px 12px var(--accent-glow);
        }
        .send-btn:hover:not(:disabled) { filter: brightness(1.1); transform: scale(1.05); }
        .send-btn:active:not(:disabled) { transform: scale(.95); }
        .send-btn:disabled { background: var(--surface2); box-shadow: none; cursor: not-allowed; opacity: .4; }
        .send-btn svg { flex-shrink: 0; }

        .hint {
            margin-top: 9px; text-align: center;
            font-size: 11px; color: var(--text-dim);
            display: flex; align-items: center; justify-content: center; gap: 5px;
        }

        /* toast */
        .toast {
            position: fixed; bottom: 22px; left: 50%;
            transform: translateX(-50%) translateY(60px);
            background: var(--surface2); border: 1px solid var(--border2);
            border-radius: 8px; padding: 9px 18px;
            font-size: 12.5px; color: var(--text);
            z-index: 999; opacity: 0;
            transition: all .22s cubic-bezier(.4,0,.2,1);
            white-space: nowrap; pointer-events: none;
        }
        .toast.on { opacity: 1; transform: translateX(-50%) translateY(0); }

        /* responsive */
        @media (max-width: 640px) {
            .sidebar { position: fixed; inset: 0 auto 0 0; transform: translateX(-100%); transition: transform .25s ease; z-index: 50; }
            .sidebar.open { transform: translateX(0); width: var(--sidebar) !important; min-width: var(--sidebar) !important; }
            .chips { grid-template-columns: 1fr; }
            .msg-inner { padding: 20px 14px 12px; }
            .input-zone { padding: 10px 12px 14px; }
        }
    </style>
</head>
<body>
<div class="shell">

    <!-- ════ SIDEBAR ════ -->
    <aside class="sidebar" id="sidebar">
        <div class="sb-top">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                </svg>
            </div>
            <span class="brand-name">AI Assistant</span>
        </div>

        <button class="new-btn" onclick="newChat()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            New conversation
        </button>

        <div class="hist-label">Recent</div>
        <div class="hist-list" id="histList">
            <div class="hist-item active">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                New conversation
            </div>
        </div>

        <div class="sb-footer">
            <div class="user-row">
                <div class="user-av">U</div>
                <div class="user-meta">
                    <strong>User</strong>
                    <span>Free plan</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- ════ MAIN ════ -->
    <main class="main">

        <header class="topbar">
            <div class="tb-l">
                <button class="icon-btn" onclick="toggleSidebar()">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <div class="model-pill">
                    <div class="dot-live"></div>
                    <span>AI Model v1.0</span>
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
            </div>
            <div class="tb-r">
                <button class="icon-btn" onclick="newChat()" title="Clear chat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4h6v2"/>
                    </svg>
                </button>
                <button class="icon-btn" onclick="showToast('Share coming soon')" title="Share">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                    </svg>
                </button>
            </div>
        </header>

        <div class="msg-scroll" id="msgScroll">
            <div class="msg-inner" id="msgInner">

                <!-- Empty state -->
                <div class="empty" id="emptyState">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                    </div>
                    <h1>What can I help with?</h1>
                    <p>Ask me anything — write, code, analyse, explain, and more.</p>
                    <div class="chips">
                        <button class="chip" onclick="suggest('Explain how neural networks learn from data')">
                            <span class="chip-ic">🧠</span>
                            <div class="chip-t">Explain a concept</div>
                            <div class="chip-d">How do neural networks learn?</div>
                        </button>
                        <button class="chip" onclick="suggest('Write a Python function to validate an email address')">
                            <span class="chip-ic">💻</span>
                            <div class="chip-t">Write code</div>
                            <div class="chip-d">Python email validation</div>
                        </button>
                        <button class="chip" onclick="suggest('Write a polite follow-up email after a job interview')">
                            <span class="chip-ic">✉️</span>
                            <div class="chip-t">Draft an email</div>
                            <div class="chip-d">Professional follow-up</div>
                        </button>
                        <button class="chip" onclick="suggest('Key differences between SQL and NoSQL databases')">
                            <span class="chip-ic">📊</span>
                            <div class="chip-t">Compare & summarise</div>
                            <div class="chip-d">SQL vs NoSQL</div>
                        </button>
                    </div>
                </div>

                <!-- Typing indicator (moved to bottom by JS) -->
                <div id="typingRow">
                    <div class="av av-bot">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                        </svg>
                    </div>
                    <div class="typing-bub">
                        <div class="td"></div><div class="td"></div><div class="td"></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Input -->
        <div class="input-zone">
            <div class="input-inner">
                <div class="composer">
                    <textarea
                        id="message"
                        placeholder="Message AI Assistant…"
                        rows="1"
                        onkeydown="onKey(event)"
                        oninput="resize(this); refreshBtn()"
                    ></textarea>
                    <button class="send-btn" id="sendBtn" onclick="sendMessage()" disabled>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                    </button>
                </div>
                <div class="hint">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Enter to send &nbsp;·&nbsp; Shift+Enter for new line
                </div>
            </div>
        </div>

    </main>
</div>

<div class="toast" id="toastEl"></div>

<script>
    let busy = false, count = 0;

    function resize(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 160) + 'px';
    }

    function refreshBtn() {
        document.getElementById('sendBtn').disabled =
            !document.getElementById('message').value.trim() || busy;
    }

    function onKey(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!busy && document.getElementById('message').value.trim()) sendMessage();
        }
    }

    function toggleSidebar() {
        const s = document.getElementById('sidebar');
        if (window.innerWidth <= 640) {
            s.classList.toggle('open');
        } else {
            s.classList.toggle('collapsed');
        }
    }

    function scrollBot() {
        const el = document.getElementById('msgScroll');
        el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
    }

    function suggest(txt) {
        const ta = document.getElementById('message');
        ta.value = txt; resize(ta); refreshBtn(); sendMessage();
    }

    function showTyping() {
        const row = document.getElementById('typingRow');
        row.classList.add('on');
        document.getElementById('msgInner').appendChild(row);
        scrollBot();
    }
    function hideTyping() { document.getElementById('typingRow').classList.remove('on'); }

    function esc(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function fmt(text) {
        text = text.replace(/```(\w*)\n?([\s\S]*?)```/g, (_, l, c) =>
            `<pre><code>${esc(c.trim())}</code></pre>`);
        text = text.replace(/`([^`\n]+)`/g, '<code>$1</code>');
        text = text.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
        text = text.replace(/\*([^*\n]+)\*/g, '<em>$1</em>');
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    function addMsg(role, text) {
        document.getElementById('emptyState').style.display = 'none';
        const inner = document.getElementById('msgInner');
        const typing = document.getElementById('typingRow');
        const isBot = role === 'bot';

        const g = document.createElement('div');
        g.className = 'msg-grp';
        g.innerHTML = `
        <div class="msg-row ${isBot ? '' : 'is-user'}">
            <div class="av ${isBot ? 'av-bot' : 'av-user'}">
                ${isBot
                    ? `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>`
                    : `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>`
                }
            </div>
            <div class="msg-body">
                <div class="msg-who">${isBot ? 'AI Assistant' : 'You'}</div>
                <div class="bubble ${isBot ? 'b-bot' : 'b-user'}">
                    ${isBot ? fmt(text) : esc(text).replace(/\n/g,'<br>')}
                </div>
                ${isBot ? `
                <div class="msg-acts">
                    <button class="act" onclick="copyMsg(this)">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>Copy
                    </button>
                    <button class="act" onclick="showToast('Regenerating…')">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 4 23 10 17 10"/>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>Retry
                    </button>
                </div>` : ''}
            </div>
        </div>`;
        inner.insertBefore(g, typing);
        scrollBot();
    }

    function copyMsg(btn) {
        const txt = btn.closest('.msg-body').querySelector('.bubble').innerText;
        navigator.clipboard.writeText(txt).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = `<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Copied!`;
            btn.style.color = 'var(--accent)';
            setTimeout(() => { btn.innerHTML = orig; btn.style.color = ''; }, 2000);
        });
    }

    function newChat() {
        [...document.getElementById('msgInner').children]
            .forEach(c => { if (!c.id) c.remove(); });
        document.getElementById('emptyState').style.display = '';
        count = 0;
        const ta = document.getElementById('message');
        ta.value = ''; ta.style.height = 'auto'; refreshBtn();
    }

    function pushHist(label) {
        const list = document.getElementById('histList');
        list.querySelectorAll('.hist-item').forEach(i => i.classList.remove('active'));
        const el = document.createElement('div');
        el.className = 'hist-item active';
        el.innerHTML = `
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>${label}`;
        list.insertBefore(el, list.firstChild);
    }

    function showToast(msg) {
        const el = document.getElementById('toastEl');
        el.textContent = msg; el.classList.add('on');
        setTimeout(() => el.classList.remove('on'), 2600);
    }

    function sendMessage() {
        const ta  = document.getElementById('message');
        const msg = ta.value.trim();
        if (!msg || busy) return;

        busy = true; count++;
        if (count === 1) pushHist(msg.length > 38 ? msg.slice(0,38) + '…' : msg);

        ta.value = ''; ta.style.height = 'auto'; refreshBtn();
        addMsg('user', msg);
        showTyping();

        fetch('/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: msg })
        })
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(data => {
            hideTyping(); busy = false;
            addMsg('bot', data.reply || 'No response received.');
            refreshBtn();
        })
        .catch(err => {
            hideTyping(); busy = false;
            addMsg('bot', '⚠️ **Request failed** — ' + err.message + '\n\nCheck your server and try again.');
            refreshBtn();
        });
    }

    window.addEventListener('load', () => document.getElementById('message').focus());
</script>
</body>
</html>