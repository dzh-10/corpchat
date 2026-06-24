<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CorpChat - Pro UI</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    :root {
      --font-sans: 'Segoe UI', system-ui, -apple-system, sans-serif;
      --font-mono: 'Fira Code', 'Cascadia Code', 'Consolas', monospace;
      --color-background-primary: #ffffff;
      --color-background-secondary: #f5f5f7;
      --color-background-tertiary: #ebebed;
      --color-border-tertiary: #e2e2e5;
      --color-border-secondary: #d0d0d5;
      --color-text-primary: #1a1a1a;
      --color-text-secondary: #555560;
      --color-text-tertiary: #9999a8;
      --border-radius-md: 8px;
      --border-radius-lg: 14px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: var(--font-sans);
      background: var(--color-background-tertiary);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .shell {
      display: flex;
      height: 90vh;
      width: 95vw;
      max-width: 1200px;
      background: var(--color-background-primary);
      border: 0.5px solid var(--color-border-tertiary);
      border-radius: var(--border-radius-lg);
      overflow: hidden;
      box-shadow: 0 8px 40px rgba(0,0,0,0.10);
    }

    /* ── Sidebar ── */
    .sidebar {
      width: 280px;
      min-width: 280px;
      background: var(--color-background-secondary);
      border-right: 0.5px solid var(--color-border-tertiary);
      display: flex;
      flex-direction: column;
    }
    .sidebar-header {
      padding: 16px 14px 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .sidebar-header span {
      font-size: 13px;
      font-weight: 500;
      color: var(--color-text-secondary);
    }
    .new-btn {
      width: 28px; height: 28px;
      border: 0.5px solid var(--color-border-secondary);
      border-radius: var(--border-radius-md);
      background: transparent;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      color: var(--color-text-secondary);
      transition: background .15s;
    }
    .new-btn:hover { background: var(--color-background-primary); }

    .search-wrap { padding: 0 10px 10px; position: relative; }
    .search-wrap input {
      width: 100%; height: 30px;
      border: 0.5px solid var(--color-border-tertiary);
      border-radius: var(--border-radius-md);
      background: var(--color-background-primary);
      padding: 0 10px 0 30px;
      font-size: 12px;
      color: var(--color-text-primary);
      outline: none;
    }
    .search-wrap .ti-search {
      position: absolute;
      left: 18px; top: 50%;
      transform: translateY(-50%);
      font-size: 13px;
      color: var(--color-text-tertiary);
    }

    .section-label {
      padding: 6px 14px 4px;
      font-size: 10px; font-weight: 500;
      letter-spacing: .06em;
      color: var(--color-text-tertiary);
      text-transform: uppercase;
    }
    .conv-list { flex: 1; overflow-y: auto; padding: 0 6px 8px; }
    .conv-item {
      display: flex; align-items: center; gap: 8px;
      padding: 7px 8px;
      border-radius: var(--border-radius-md);
      cursor: pointer;
      transition: background .12s;
      margin-bottom: 2px;
    }
    .conv-item:hover, .conv-item.active { background: var(--color-background-primary); }
    .conv-item .dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
    .dot-blue   { background: #378ADD; }
    .dot-teal   { background: #1D9E75; }
    .dot-coral  { background: #D85A30; }
    .dot-purple { background: #7F77DD; }
    .dot-amber  { background: #BA7517; }
    .conv-title {
      font-size: 13px; color: var(--color-text-primary);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .conv-time { font-size: 10px; color: var(--color-text-tertiary); margin-left: auto; flex-shrink: 0; }

    .sidebar-footer {
      padding: 10px;
      border-top: 0.5px solid var(--color-border-tertiary);
      display: flex; align-items: center; gap: 8px;
    }
    .avatar-sm {
      width: 28px; height: 28px; border-radius: 50%;
      background: #E6F1FB;
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 500; color: #0C447C; flex-shrink: 0;
    }
    .avatar-name { font-size: 12px; font-weight: 500; color: var(--color-text-primary); }
    .avatar-role { font-size: 10px; color: var(--color-text-tertiary); }

    /* ── Main ── */
    .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

    .topbar {
      height: 52px;
      border-bottom: 0.5px solid var(--color-border-tertiary);
      display: flex; align-items: center;
      padding: 0 16px; gap: 10px;
    }
    .topbar-title { font-size: 14px; font-weight: 500; color: var(--color-text-primary); flex: 1; }
    .topbar-chip {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: 11px; padding: 3px 9px;
      border-radius: 99px;
      border: 0.5px solid var(--color-border-tertiary);
      color: var(--color-text-secondary);
      cursor: pointer; transition: background .12s;
    }
    .topbar-chip:hover { background: var(--color-background-secondary); }
    .topbar-chip .ti { font-size: 13px; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #1D9E75; }

    /* ── Messages ── */
    .messages {
      flex: 1; overflow-y: auto;
      padding: 20px 20px 10px;
      display: flex; flex-direction: column; gap: 16px;
    }
    .msg-row { display: flex; gap: 10px; align-items: flex-start; }
    .msg-row.user { flex-direction: row-reverse; }

    .msg-avatar {
      width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 500;
    }
    .msg-avatar.ai   { background: #EEEDFE; color: #3C3489; }
    .msg-avatar.user-av { background: #E1F5EE; color: #085041; }

    .bubble {
      max-width: 68%;
      padding: 10px 14px;
      border-radius: 12px;
      font-size: 14px; line-height: 1.65;
      color: var(--color-text-primary);
    }
    .bubble.ai {
      background: var(--color-background-secondary);
      border: 0.5px solid var(--color-border-tertiary);
      border-top-left-radius: 3px;
    }
    .bubble.user {
      background: #378ADD; color: #fff;
      border-top-right-radius: 3px;
    }
    .bubble.user a { color: #B5D4F4; }

    /* ── Email Thread UI ── */
    .email-card {
      background: #ffffff;
      border: 0.5px solid var(--color-border-tertiary);
      border-radius: var(--border-radius-md);
      padding: 16px;
      margin-bottom: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
      width: 100%;
      box-sizing: border-box;
    }
    .email-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 0.5px solid var(--color-border-tertiary);
      padding-bottom: 8px;
      margin-bottom: 10px;
    }
    .email-card-sender {
      font-weight: 600;
      font-size: 13px;
      color: var(--color-text-primary);
    }
    .email-card-email {
      font-size: 12px;
      color: var(--color-text-secondary);
      margin-left: 4px;
    }
    .email-card-meta {
      font-size: 11px;
      color: var(--color-text-tertiary);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .email-card-body {
      font-size: 13.5px;
      line-height: 1.6;
      color: var(--color-text-primary);
      white-space: pre-line;
      word-break: break-word;
    }

    .bubble-meta {
      font-size: 10px; color: var(--color-text-tertiary);
      margin-top: 5px;
      display: flex; gap: 8px; align-items: center;
    }
    .msg-row.user .bubble-meta { justify-content: flex-end; }

    .bubble-actions { display: flex; gap: 4px; margin-top: 6px; }
    .bact {
      background: transparent;
      border: 0.5px solid var(--color-border-tertiary);
      border-radius: var(--border-radius-md);
      padding: 3px 7px; font-size: 11px;
      color: var(--color-text-secondary);
      cursor: pointer;
      display: flex; align-items: center; gap: 4px;
      transition: background .12s;
    }
    .bact:hover { background: var(--color-background-primary); }
    .bact .ti { font-size: 12px; }

    /* ── Input Area ── */
    .input-area { padding: 12px 16px 14px; border-top: 0.5px solid var(--color-border-tertiary); }
    .input-toolbar { display: flex; gap: 6px; margin-bottom: 8px; }
    .tool-btn {
      background: transparent;
      border: 0.5px solid var(--color-border-tertiary);
      border-radius: var(--border-radius-md);
      padding: 4px 9px; font-size: 11px;
      color: var(--color-text-secondary);
      cursor: pointer;
      display: flex; align-items: center; gap: 5px;
      transition: background .12s;
    }
    .tool-btn:hover { background: var(--color-background-secondary); }
    .tool-btn .ti { font-size: 13px; }

    .input-box {
      display: flex; align-items: flex-end; gap: 8px;
      background: var(--color-background-secondary);
      border: 0.5px solid var(--color-border-tertiary);
      border-radius: var(--border-radius-lg);
      padding: 8px 10px;
    }
    .input-box textarea {
      flex: 1; background: transparent; border: none; outline: none;
      resize: none; font-size: 13px; line-height: 1.6;
      color: var(--color-text-primary);
      font-family: var(--font-sans);
      max-height: 100px; min-height: 24px;
    }
    .input-box textarea::placeholder { color: var(--color-text-tertiary); }

    .send-btn {
      width: 32px; height: 32px;
      border-radius: var(--border-radius-md);
      background: #378ADD; border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      color: #fff; flex-shrink: 0; transition: opacity .15s;
    }
    .send-btn:hover { opacity: .85; }
    .send-btn .ti { font-size: 16px; }
    
    .gmail-send-btn {
      background: #1a73e8;
      color: #ffffff;
      border: none;
      border-radius: 18px;
      padding: 0 16px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: background 0.15s, transform 0.1s;
      height: 32px;
    }
    .gmail-send-btn:hover {
      background: #1557b0;
    }
    .gmail-send-btn:active {
      transform: scale(0.97);
    }

    .input-hint { font-size: 10px; color: var(--color-text-tertiary); margin-top: 6px; text-align: center; }

    /* ── Scrollbars ── */
    .messages::-webkit-scrollbar,
    .conv-list::-webkit-scrollbar { width: 4px; }
    .messages::-webkit-scrollbar-thumb,
    .conv-list::-webkit-scrollbar-thumb { background: var(--color-border-tertiary); border-radius: 2px; }
    /* ===== Floating Compose Button (FAB) ===== */
    .compose-fab {
        position: fixed;
        bottom: 32px;
        right: 32px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 20px;
        background: #1a73e8;
        color: #fff;
        border: none;
        border-radius: 24px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(26, 115, 232, 0.4);
        transition: all 0.2s ease;
        z-index: 1000;
    }
    .compose-fab:hover {
        background: #1557b0;
        box-shadow: 0 6px 24px rgba(26, 115, 232, 0.55);
        transform: translateY(-2px);
    }

    /* ===== Compose Panel ===== */
    .compose-panel {
        position: fixed;
        bottom: 0;
        right: 32px;
        width: 480px;
        max-height: 560px;
        background: #fff;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 8px 40px rgba(0,0,0,0.25);
        display: flex;
        flex-direction: column;
        z-index: 1100;
        font-family: 'Cairo', 'Segoe UI', sans-serif;
        transition: max-height 0.25s ease;
    }
    .compose-panel.minimized {
        max-height: 48px;
        overflow: hidden;
    }
    .compose-panel.hidden {
        display: none;
    }

    /* Header */
    .compose-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #202124;
        color: #fff;
        border-radius: 12px 12px 0 0;
        cursor: grab;
        user-select: none;
        font-size: 14px;
        font-weight: 600;
    }
    .compose-header-actions {
        display: flex;
        gap: 8px;
    }
    .compose-header-actions button {
        background: none;
        border: none;
        color: #ccc;
        cursor: pointer;
        font-size: 16px;
        padding: 0 4px;
        line-height: 1;
        transition: color 0.15s;
    }
    .compose-header-actions button:hover { color: #fff; }

    /* Body */
    .compose-body {
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
    }

    /* Type Toggle */
    .compose-type-toggle {
        display: flex;
        border-bottom: 1px solid #e0e0e0;
        padding: 0;
    }
    .type-btn {
        flex: 1;
        padding: 10px;
        background: none;
        border: none;
        font-size: 13px;
        color: #666;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.15s;
        font-family: inherit;
    }
    .type-btn.active {
        color: #1a73e8;
        border-bottom-color: #1a73e8;
        font-weight: 600;
    }
    .type-btn:hover:not(.active) { background: #f5f5f5; }

    /* Fields */
    .compose-field {
        position: relative;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e0e0e0;
        padding: 0 16px;
    }
    .compose-field label {
        min-width: 70px;
        font-size: 13px;
        color: #888;
        font-family: inherit;
    }
    .compose-field input {
        flex: 1;
        border: none;
        outline: none;
        padding: 10px 0;
        font-size: 14px;
        color: #202124;
        background: transparent;
        font-family: inherit;
    }

    /* Textarea */
    .compose-textarea {
        flex: 1;
        border: none;
        outline: none;
        padding: 14px 16px;
        font-size: 14px;
        color: #202124;
        resize: none;
        min-height: 180px;
        font-family: inherit;
        line-height: 1.6;
    }

    /* Footer */
    .compose-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        border-top: 1px solid #e0e0e0;
        background: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }
    .btn-send {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #1a73e8;
        color: #fff;
        border: none;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
        font-family: inherit;
    }
    .btn-send:hover { background: #1557b0; }

    .compose-footer-right {
        display: flex;
        gap: 4px;
    }
    .compose-footer-right button {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        padding: 6px 8px;
        border-radius: 50%;
        transition: background 0.15s;
    }
    .compose-footer-right button:hover { background: #e8eaed; }

    /* Suggestions Dropdown */
    .suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0; right: 0;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 10;
        max-height: 180px;
        overflow-y: auto;
    }
    .suggestion-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        cursor: pointer;
        font-size: 13px;
        transition: background 0.1s;
    }
    .suggestion-item:hover { background: #f1f3f4; }
    .suggestion-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: #1a73e8;
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 13px;
    }

    /* Utility */
    .hidden {
        display: none !important;
    }

    /* Mobile Responsive */
    @media (max-width: 600px) {
        .compose-panel {
            right: 0; left: 0;
            width: 100%;
            border-radius: 12px 12px 0 0;
        }
        .compose-fab span { display: none; }
        .compose-fab { padding: 14px; border-radius: 50%; }
    }
  </style>
</head>
<body>

@php
  $authUser = auth()->user();
  $initials = $authUser ? strtoupper(substr($authUser->name, 0, 2)) : 'AH';
@endphp

<div class="shell">

  <!-- ── Sidebar ── -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <span>المحادثات / Conversations</span>
      <button class="new-btn" id="new-chat-btn" title="New chat"><i class="ti ti-edit"></i></button>
    </div>
    <div class="search-wrap">
      <i class="ti ti-search"></i>
      <input type="text" id="search-input" placeholder="بحث في المحادثات... / Search chats...">
    </div>

    <div class="section-label">الدردشات الداخلية / Internal Chats</div>
    <div class="conv-list" id="internal-chats-list">
      <!-- Dynamic Internal Chats -->
    </div>

    <div class="section-label" style="padding-left:8px">البريد الخارجي / Client Emails</div>
    <div class="conv-list" id="external-emails-list">
      <!-- Dynamic Client Emails -->
    </div>

    <div class="sidebar-footer">
      <div class="avatar-sm">{{ $initials }}</div>
      <div>
        <div class="avatar-name">{{ $authUser->name }}</div>
        <div class="avatar-role">{{ $authUser->is_admin ? 'Admin' : 'Employee' }}</div>
      </div>
      @if($authUser->is_admin)
        <button class="new-btn" style="margin-left:auto" title="Settings" onclick="window.location.href='/admin'"><i class="ti ti-settings"></i></button>
      @endif
      <form action="{{ route('logout') }}" method="POST" style="margin-left: {{ $authUser->is_admin ? '0' : 'auto' }}; display: inline-flex;">
        @csrf
        <button type="submit" class="new-btn" title="Logout"><i class="ti ti-logout"></i></button>
      </form>
    </div>
  </aside>

  <!-- ── Main Chat Area ── -->
  <section class="main" id="chat-main-section" style="display: none;">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">General Team</div>
      <div class="topbar-chip" id="topbar-status"><div class="status-dot"></div> Active</div>
      <div class="topbar-chip" id="topbar-type-chip"><i class="ti ti-mail"></i> Switch to Email</div>
    </div>

    <div class="messages" id="msgs">
      <!-- Dynamic Messages Load Here -->
    </div>

    <!-- Input Area -->
    <div class="input-area" id="input-area-container" style="padding: 0;">
      <!-- Email Header Fields (Visible only for external_email) -->
      <div class="email-compose-header" id="email-compose-header" style="display: none; padding: 12px 16px; border-bottom: 0.5px solid var(--color-border-tertiary); background: var(--color-background-secondary); font-size: 13px; color: var(--color-text-secondary); gap: 8px; flex-direction: column;">
        <div style="display: flex; align-items: center; gap: 6px;">
          <span style="font-weight: 600; min-width: 60px; color: var(--color-text-secondary);">إلى:</span>
          <span id="email-compose-to" style="color: var(--color-text-primary); font-weight: 500;">-</span>
        </div>
        <div style="display: flex; align-items: center; gap: 6px;">
          <span style="font-weight: 600; min-width: 60px; color: var(--color-text-secondary);">الموضوع:</span>
          <span id="email-compose-subject" style="color: var(--color-text-primary); font-weight: 500;">-</span>
        </div>
      </div>

      <div style="padding: 12px 16px;">
        <div class="input-box" style="background: transparent; border: none; padding: 0;">
          <textarea id="message-input" placeholder="Type your message here..." style="background: var(--color-background-secondary); border: 0.5px solid var(--color-border-tertiary); border-radius: var(--border-radius-lg); padding: 12px; width: 100%; min-height: 48px; max-height: 200px; font-size: 13px; outline: none;"></textarea>
        </div>
        
        <div class="input-footer" style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px;">
          <div class="input-toolbar" style="margin-bottom: 0;">
            <button class="tool-btn"><i class="ti ti-paperclip"></i> Attach</button>
            <button class="tool-btn"><i class="ti ti-microphone"></i> Audio</button>
          </div>
          <div style="display: flex; align-items: center; gap: 12px;">
            <div class="input-hint" id="input-hint" style="margin-top: 0; font-size: 10px;">Press Enter to send, Shift + Enter for new line</div>
            
            <!-- Standard Circular Send Button (for internal chat) -->
            <button class="send-btn" id="send-btn"><i class="ti ti-send"></i></button>
            
            <!-- Large Gmail Style Send Button (for email) -->
            <button class="gmail-send-btn" id="gmail-send-btn" style="display: none;">
              <span>إرسال البريد</span>
              <i class="ti ti-send" style="transform: rotate(180deg); font-size: 12px;"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── Empty State ── -->
  <section class="main" id="empty-state-section" style="display: flex; align-items: center; justify-content: center; text-align: center; padding: 40px;">
    <div style="max-width: 400px; color: var(--color-text-secondary);">
      <i class="ti ti-brand-hipchat" style="font-size: 64px; color: #378ADD; margin-bottom: 20px;"></i>
      <h2 style="font-size: 20px; font-weight: 600; color: var(--color-text-primary); margin-bottom: 10px;">Welcome to CorpChat</h2>
      <p style="font-size: 13px;">Select a conversation from the sidebar or click <i class="ti ti-edit"></i> to start a new chat.</p>
    </div>
  </section>

</div>

{{-- Floating Compose Button --}}
<button id="composeBtn" class="compose-fab" title="رسالة جديدة / New Message">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" 
         viewBox="0 0 24 24" fill="none" stroke="currentColor" 
         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
    </svg>
    <span>إنشاء / Compose</span>
</button>

{{-- Gmail-style Compose Panel --}}
<div id="composePanel" class="compose-panel hidden">
    
    {{-- Header --}}
    <div class="compose-header" id="composeDragHandle">
        <span>رسالة جديدة / New Message</span>
        <div class="compose-header-actions">
            <button class="compose-minimize" id="minimizeCompose" title="تصغير">—</button>
            <button class="compose-close" id="closeCompose" title="إغلاق">✕</button>
        </div>
    </div>

    {{-- Form --}}
    <form id="composeForm" class="compose-body">
        @csrf
        
        {{-- Type Toggle (Internal / Email) --}}
        <div class="compose-type-toggle">
            <button type="button" class="type-btn active" data-type="email">
                ✉️ بريد عميل / Client Email
            </button>
            <button type="button" class="type-btn" data-type="internal">
                💬 داخلي / Internal
            </button>
        </div>

        {{-- To Field (Email mode) --}}
        <div class="compose-field" id="toField">
            <label>إلى / To</label>
            <input type="email" name="client_email" 
                   placeholder="client@example.com" 
                   autocomplete="email" />
        </div>

        {{-- Client Name Field (Email mode) --}}
        <div class="compose-field" id="nameField">
            <label>الاسم / Name</label>
            <input type="text" name="client_name" 
                   placeholder="اسم العميل... / Client name..." />
        </div>

        {{-- To Field (Internal mode) --}}
        <div class="compose-field hidden" id="toInternalField">
            <label>إلى / To</label>
            <input type="text" name="employee_search" 
                   placeholder="ابحث عن موظف... / Search employee..."
                   id="employeeSearch"
                   autocomplete="off" />
            <div id="employeeSuggestions" class="suggestions-dropdown hidden"></div>
        </div>

        {{-- Subject --}}
        <div class="compose-field">
            <label>الموضوع / Subject</label>
            <input type="text" name="subject" 
                   placeholder="موضوع الرسالة..." />
        </div>

        {{-- Message Body --}}
        <textarea name="body" class="compose-textarea" 
                  placeholder="اكتب رسالتك هنا..."></textarea>

        {{-- Footer Actions --}}
        <div class="compose-footer">
            <button type="submit" class="btn-send">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" 
                     stroke="currentColor" stroke-width="2" style="margin-left: 5px;">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
                إرسال / Send
            </button>
            <div class="compose-footer-right">
                <button type="button" class="btn-attach" title="إرفاق ملف">
                    📎
                </button>
                <button type="button" class="btn-discard" id="discardCompose" title="حذف المسودة">
                    🗑️
                </button>
            </div>
        </div>
    </form>
</div>

<!-- ── CDN Libraries ── -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

<!-- ── Frontend Logic ── -->
<script>
  // Setup Axios with CSRF
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

  // Initialize Laravel Echo
  window.Pusher = Pusher;
  window.Echo = new Echo({
    broadcaster: 'reverb',
    key: '{{ env("REVERB_APP_KEY") }}',
    wsHost: window.location.hostname,
    wsPort: 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    auth: {
      headers: {
        'X-CSRF-TOKEN': csrfToken
      }
    }
  });

  // App State
  let conversations = [];
  let activeConversation = null;
  let currentUserId = {{ auth()->id() }};
  let currentUserEmail = "{{ auth()->user()->email }}";
  let currentSubscription = null;

  // DOM Elements
  const internalChatsList = document.getElementById('internal-chats-list');
  const externalEmailsList = document.getElementById('external-emails-list');
  const chatMainSection = document.getElementById('chat-main-section');
  const emptyStateSection = document.getElementById('empty-state-section');
  const topbarTitle = document.getElementById('topbar-title');
  const topbarTypeChip = document.getElementById('topbar-type-chip');
  const msgsContainer = document.getElementById('msgs');
  const messageInput = document.getElementById('message-input');
  const sendBtn = document.getElementById('send-btn');
  const gmailSendBtn = document.getElementById('gmail-send-btn');
  const searchInput = document.getElementById('search-input');

  // Compose Panel DOM
  const composeBtn    = document.getElementById('composeBtn');
  const sidebarNewChatBtn = document.getElementById('new-chat-btn');
  const composePanel  = document.getElementById('composePanel');
  const closeCompose  = document.getElementById('closeCompose');
  const minimizeCompose = document.getElementById('minimizeCompose');
  const discardCompose = document.getElementById('discardCompose');
  const composeForm   = document.getElementById('composeForm');
  const typeBtns      = document.querySelectorAll('.type-btn');
  const toField       = document.getElementById('toField');
  const nameField       = document.getElementById('nameField');
  const toInternalField = document.getElementById('toInternalField');
  const employeeSearch = document.getElementById('employeeSearch');
  const suggestions   = document.getElementById('employeeSuggestions');

  // Initial Load
  fetchConversations();

  // Search filter
  searchInput.addEventListener('input', (e) => {
    const query = e.target.value.toLowerCase();
    renderConversations(query);
  });

  // Fetch all conversations
  async function fetchConversations(selectId = null) {
    try {
      const response = await axios.get('/api/conversations');
      conversations = response.data;
      renderConversations();
      
      if (selectId) {
        selectConversation(selectId);
      } else if (activeConversation) {
        // Refresh active conversation data
        const updated = conversations.find(c => c.id === activeConversation.id);
        if (updated) activeConversation = updated;
      }
    } catch (err) {
      console.error('Error fetching conversations:', err);
    }
  }

  // Render conversations in sidebar
  function renderConversations(filter = '') {
    internalChatsList.innerHTML = '';
    externalEmailsList.innerHTML = '';

    const colors = ['dot-blue', 'dot-teal', 'dot-coral', 'dot-purple', 'dot-amber'];

    conversations.forEach((conv, index) => {
      const title = conv.type === 'internal_chat' 
        ? (conv.subject || 'Internal Group') 
        : `${conv.external_contact_name || 'Client'} (${conv.subject || 'No Subject'})`;

      if (filter && !title.toLowerCase().includes(filter)) {
        return;
      }

      const latestMsg = conv.messages && conv.messages.length > 0 
        ? conv.messages[0].body 
        : 'No messages yet';

      const isActive = activeConversation && activeConversation.id === conv.id;
      const colorClass = colors[index % colors.length];
      const time = conv.updated_at ? new Date(conv.updated_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';

      let itemHtml = '';
      if (conv.type === 'external_email') {
        const clientName = conv.external_contact_name || 'Client';
        const subject = conv.subject || 'No Subject';
        
        itemHtml = `
          <div class="conv-item ${isActive ? 'active' : ''}" onclick="selectConversation(${conv.id})" style="align-items: flex-start; padding: 10px 12px;">
            <div class="dot ${colorClass}" style="margin-top: 5px;"></div>
            <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px;">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 13px; font-weight: 600; color: var(--color-text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70%;">${clientName}</span>
                <span style="font-size: 10px; color: var(--color-text-tertiary);">${time}</span>
              </div>
              <div style="font-size: 11px; font-weight: 500; color: var(--color-text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                ${subject}
              </div>
              <div style="font-size: 11px; color: var(--color-text-tertiary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                ${latestMsg}
              </div>
            </div>
          </div>
        `;
      } else {
        itemHtml = `
          <div class="conv-item ${isActive ? 'active' : ''}" onclick="selectConversation(${conv.id})">
            <div class="dot ${colorClass}"></div>
            <div style="flex: 1; min-width: 0;">
              <div class="conv-title">${title}</div>
              <div style="font-size: 11px; color: var(--color-text-tertiary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                ${latestMsg}
              </div>
            </div>
          </div>
        `;
      }

      if (conv.type === 'internal_chat') {
        internalChatsList.insertAdjacentHTML('beforeend', itemHtml);
      } else {
        externalEmailsList.insertAdjacentHTML('beforeend', itemHtml);
      }
    });
  }

  // Select active conversation
  async function selectConversation(id) {
    const selected = conversations.find(c => c.id === id);
    if (!selected) return;

    activeConversation = selected;
    renderConversations();

    // Toggle main screen
    emptyStateSection.style.display = 'none';
    chatMainSection.style.display = 'flex';

    // Update Topbar
    topbarTitle.textContent = selected.type === 'internal_chat' 
      ? (selected.subject || 'Internal Group') 
      : `${selected.external_contact_name || 'Client'} (${selected.subject || 'No Subject'})`;

    topbarTypeChip.innerHTML = selected.type === 'internal_chat'
      ? `<i class="ti ti-brand-hipchat"></i> محادثة داخلية / Internal Chat`
      : `<i class="ti ti-mail"></i> بريد خارجي / Client Email`;

    // Toggle Email Compose Box UI vs standard Chat UI
    const emailHeader = document.getElementById('email-compose-header');
    const normalSendBtn = document.getElementById('send-btn');
    const inputHint = document.getElementById('input-hint');

    if (selected.type === 'external_email') {
      emailHeader.style.display = 'flex';
      document.getElementById('email-compose-to').textContent = `${selected.external_contact_name || 'Client'} <${selected.external_contact_email}>`;
      document.getElementById('email-compose-subject').textContent = selected.subject || 'No Subject';
      gmailSendBtn.style.display = 'inline-flex';
      normalSendBtn.style.display = 'none';
      inputHint.style.display = 'none';
      messageInput.placeholder = 'اكتب رسالة البريد الإلكتروني هنا...';
      messageInput.style.minHeight = '150px';
    } else {
      emailHeader.style.display = 'none';
      gmailSendBtn.style.display = 'none';
      normalSendBtn.style.display = 'flex';
      inputHint.style.display = 'block';
      messageInput.placeholder = 'Type your message here...';
      messageInput.style.minHeight = '48px';
    }

    // Fetch and render messages
    await loadMessages(id);

    // Subscribe to Reverb WebSocket Channel
    subscribeToChannel(id);
  }

  // Fetch messages from database
  async function loadMessages(convId) {
    try {
      const response = await axios.get(`/api/conversations/${convId}/messages`);
      const messages = response.data;
      msgsContainer.innerHTML = '';
      messages.forEach(msg => appendMessageMarkup(msg));
      scrollToBottom();
    } catch (err) {
      console.error('Error loading messages:', err);
    }
  }

  // Subscribe to WebSocket broadcast channel
  function subscribeToChannel(convId) {
    // Leave previous channel
    if (currentSubscription) {
      window.Echo.leave(`conversation.${currentSubscription}`);
    }

    currentSubscription = convId;
    window.Echo.private(`conversation.${convId}`)
      .listen('.message.sent', (e) => {
        console.log('Received broadcast event:', e);
        if (e.message && e.message.conversation_id === convId) {
          appendMessageMarkup(e.message);
          scrollToBottom();
          // Update sidebar preview
          fetchConversations();
        }
      });
  }

  // Append message HTML markup
  function appendMessageMarkup(msg) {
    // Check if message already exists in the DOM
    const existing = document.querySelector(`[data-msg-id="${msg.id}"]`);
    
    const isUser = msg.sender_id === currentUserId;
    const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    let statusText = '';
    if (isUser) {
      if (msg.status === 'sending') statusText = ' · Sending...';
      else if (msg.status === 'sent') statusText = ' · Sent';
      else if (msg.status === 'delivered') statusText = ' · Delivered';
      else if (msg.status === 'failed') statusText = ' · Failed';
    }

    if (existing) {
      // Update its status meta if it exists
      const meta = existing.querySelector('.bubble-meta') || existing.querySelector('.email-card-meta');
      if (meta) {
        if (activeConversation && activeConversation.type === 'external_email') {
          meta.textContent = `${time}${statusText}`;
        } else {
          meta.textContent = `${msg.sender_name} · ${time}${statusText}`;
        }
      }
      return;
    }

    const initial = msg.sender_name ? msg.sender_name.substring(0,2).toUpperCase() : 'US';

    let msgHtml = '';
    if (activeConversation && activeConversation.type === 'external_email') {
      const senderEmail = msg.sender_email || (isUser ? currentUserEmail : activeConversation.external_contact_email);
      const isFailed = msg.status === 'failed';
      msgHtml = `
        <div class="email-card" data-msg-id="${msg.id}">
          <div class="email-card-header">
            <div>
              <span class="email-card-sender">${msg.sender_name}</span>
              <span class="email-card-email">&lt;${senderEmail}&gt;</span>
            </div>
            <div class="email-card-meta" style="${isFailed ? 'color: #dc2626;' : ''}">
              ${time}${statusText}
            </div>
          </div>
          <div class="email-card-body">
            ${msg.body}
          </div>
        </div>
      `;
    } else {
      msgHtml = `
        <div class="msg-row ${isUser ? 'user' : ''}" data-msg-id="${msg.id}">
          <div class="msg-avatar ${isUser ? 'user-av' : 'ai'}">${initial}</div>
          <div style="max-width: 68%;">
            <div class="bubble ${isUser ? 'user' : 'ai'}">
              ${msg.body}
            </div>
            <div class="bubble-meta">
              ${msg.sender_name} · ${time}${statusText}
            </div>
          </div>
        </div>
      `;
    }
    
    msgsContainer.insertAdjacentHTML('beforeend', msgHtml);
  }

  // Scroll messages to bottom
  function scrollToBottom() {
    msgsContainer.scrollTop = msgsContainer.scrollHeight;
  }

  // Send Message Logic
  async function sendMessage() {
    const text = messageInput.value.trim();
    if (!text || !activeConversation) return;

    messageInput.value = '';

    const tempId = 'temp-' + Date.now();
    // Optimistically append local sending state message
    const tempMsg = {
      id: tempId,
      conversation_id: activeConversation.id,
      sender_id: currentUserId,
      sender_name: "{{ $authUser->name }}",
      sender_email: currentUserEmail,
      body: text,
      status: 'sending',
      created_at: new Date().toISOString()
    };
    appendMessageMarkup(tempMsg);
    scrollToBottom();

    try {
      const response = await axios.post(`/api/conversations/${activeConversation.id}/messages`, {
        body: text
      });
      const savedMsg = response.data.data;
      
      // Replace temp message with server message
      const tempElement = document.querySelector(`[data-msg-id="${tempId}"]`);
      if (tempElement) {
        tempElement.setAttribute('data-msg-id', savedMsg.id);
        const meta = tempElement.querySelector('.bubble-meta') || tempElement.querySelector('.email-card-meta');
        const time = new Date(savedMsg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        if (meta) {
          let statusText = savedMsg.status === 'sending' ? ' · Sending...' : ' · Delivered';
          if (activeConversation.type === 'external_email') {
            statusText = ' · Sending...';
            meta.textContent = `${time}${statusText}`;
          } else {
            meta.textContent = `${savedMsg.sender_name} · ${time}${statusText}`;
          }
        }
      }
      
      fetchConversations();
    } catch (err) {
      console.error('Error sending message:', err);
      // Mark temp message as failed
      const tempElement = document.querySelector(`[data-msg-id="${tempId}"]`);
      if (tempElement) {
        const meta = tempElement.querySelector('.bubble-meta') || tempElement.querySelector('.email-card-meta');
        if (meta) {
          meta.textContent = `Failed to send`;
          meta.style.color = '#dc2626';
        }
      }
    }
  }

  // Keyboard shortcut to send message
  messageInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });

  sendBtn.addEventListener('click', sendMessage);
  if (gmailSendBtn) {
    gmailSendBtn.addEventListener('click', sendMessage);
  }

  // === Compose Panel Logic ===
  let currentType = 'email';

  const openComposePanel = () => {
      composePanel.classList.remove('hidden', 'minimized');
      const activeInput = currentType === 'email' 
          ? composePanel.querySelector('input[name="client_email"]')
          : composePanel.querySelector('input[name="employee_search"]');
      if (activeInput) activeInput.focus();
  };

  // Open
  if (composeBtn) composeBtn.addEventListener('click', openComposePanel);
  if (sidebarNewChatBtn) sidebarNewChatBtn.addEventListener('click', (e) => {
      e.preventDefault();
      openComposePanel();
  });

  // Close
  closeCompose.addEventListener('click', () => composePanel.classList.add('hidden'));
  discardCompose.addEventListener('click', () => {
      if (confirm('هل تريد حذف المسودة؟ / Discard draft?')) {
          composeForm.reset();
          if (employeeSearch) {
              employeeSearch.dataset.selectedId = '';
          }
          composePanel.classList.add('hidden');
      }
  });

  // Minimize
  minimizeCompose.addEventListener('click', () => {
      composePanel.classList.toggle('minimized');
  });

  // Double-click header to toggle minimize
  document.getElementById('composeDragHandle').addEventListener('dblclick', () => {
      composePanel.classList.toggle('minimized');
  });

  // Type Toggle
  typeBtns.forEach(btn => {
      btn.addEventListener('click', () => {
          typeBtns.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          currentType = btn.dataset.type;

          if (currentType === 'email') {
              toField.classList.remove('hidden');
              nameField.classList.remove('hidden');
              toInternalField.classList.add('hidden');
          } else {
              toField.classList.add('hidden');
              nameField.classList.add('hidden');
              toInternalField.classList.remove('hidden');
              employeeSearch.focus();
          }
      });
  });

  // Employee Search (Internal mode)
  let searchTimeout;
  employeeSearch.addEventListener('input', () => {
      clearTimeout(searchTimeout);
      const q = employeeSearch.value.trim();
      if (q.length < 2) { suggestions.classList.add('hidden'); return; }

      searchTimeout = setTimeout(async () => {
          try {
              const res = await fetch(`/api/employees/search?q=${encodeURIComponent(q)}`, {
                  headers: { 'X-CSRF-TOKEN': csrfToken }
              });
              const employees = await res.json();

              suggestions.innerHTML = employees.map(emp => `
                  <div class="suggestion-item" data-id="${emp.id}" data-name="${emp.name}">
                      <div class="suggestion-avatar">${emp.name.charAt(0).toUpperCase()}</div>
                      <div>
                          <div style="font-weight:600">${emp.name}</div>
                          <div style="font-size:12px;color:#888">${emp.email}</div>
                      </div>
                  </div>
              `).join('');

              suggestions.classList.toggle('hidden', employees.length === 0);

              suggestions.querySelectorAll('.suggestion-item').forEach(item => {
                  item.addEventListener('click', () => {
                      employeeSearch.value = item.dataset.name;
                      employeeSearch.dataset.selectedId = item.dataset.id;
                      suggestions.classList.add('hidden');
                  });
              });
          } catch (err) {
              console.error('Error fetching employees:', err);
          }
      }, 300);
  });

  // Submit
  composeForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      // Validation
      if (currentType === 'email') {
          const clientEmail = composeForm.querySelector('input[name="client_email"]').value.trim();
          if (!clientEmail) {
              alert('يرجى إدخال البريد الإلكتروني للعميل / Please enter client email.');
              return;
          }
      } else {
          const selectedId = employeeSearch.dataset.selectedId;
          if (!selectedId) {
              alert('يرجى تحديد موظف من القائمة / Please select an employee.');
              return;
          }
      }

      const bodyText = composeForm.querySelector('textarea[name="body"]').value.trim();
      if (!bodyText) {
          alert('يرجى كتابة نص الرسالة / Please enter message body.');
          return;
      }

      const sendBtn = composeForm.querySelector('.btn-send');
      sendBtn.disabled = true;
      sendBtn.innerHTML = '⏳ جاري الإرسال...';

      const formData = new FormData(composeForm);
      formData.append('type', currentType);
      if (currentType === 'internal' && employeeSearch.dataset.selectedId) {
          formData.append('recipient_id', employeeSearch.dataset.selectedId);
      }

      try {
          const res = await fetch('/conversations', {
              method: 'POST',
              body: formData,
              headers: { 'X-CSRF-TOKEN': csrfToken }
          });
          const data = await res.json();

          if (data.success) {
              composeForm.reset();
              employeeSearch.dataset.selectedId = '';
              composePanel.classList.add('hidden');
              // Select the new conversation dynamically
              if (data.conversation_id) {
                  await fetchConversations(data.conversation_id);
              }
          } else {
              alert('حدث خطأ: ' + (data.message || 'Unknown error'));
          }
      } catch (err) {
          alert('فشل الاتصال بالخادم');
      } finally {
          sendBtn.disabled = false;
          sendBtn.innerHTML = `
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" 
                   stroke="currentColor" stroke-width="2" style="margin-left: 5px;">
                  <line x1="22" y1="2" x2="11" y2="13"/>
                  <polygon points="22 2 15 22 11 13 2 9 22 2"/>
              </svg>
              إرسال / Send
          `;
      }
  });

  // Close suggestions when clicking outside
  document.addEventListener('click', (e) => {
      if (!toInternalField.contains(e.target)) {
          suggestions.classList.add('hidden');
      }
  });

  // ESC to close
  document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') composePanel.classList.add('hidden');
  });

  // Drag and drop logic for compose panel
  let isDragging = false;
  let startX, startY, initialRight, initialBottom;
  const dragHandle = document.getElementById('composeDragHandle');

  dragHandle.addEventListener('mousedown', (e) => {
      if (e.target.tagName === 'BUTTON') return;
      isDragging = true;
      dragHandle.style.cursor = 'grabbing';
      startX = e.clientX;
      startY = e.clientY;
      
      const rect = composePanel.getBoundingClientRect();
      initialRight = window.innerWidth - rect.right;
      initialBottom = window.innerHeight - rect.bottom;
      
      e.preventDefault();
  });

  document.addEventListener('mousemove', (e) => {
      if (!isDragging) return;
      const deltaX = e.clientX - startX;
      const deltaY = e.clientY - startY;
      composePanel.style.right = `${initialRight - deltaX}px`;
      composePanel.style.bottom = `${initialBottom - deltaY}px`;
  });

  document.addEventListener('mouseup', () => {
      if (isDragging) {
          isDragging = false;
          dragHandle.style.cursor = 'grab';
      }
  });
</script>

</body>
</html>
