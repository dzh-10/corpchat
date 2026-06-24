<!DOCTYPE html>
<html lang="ar" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CorpChat - Pro UI</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Locked Tabler Icons version -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.2.0/tabler-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/chat.css'])
</head>
<body>

<div class="shell">

  <!-- Sidebar Component -->
  <x-chat.sidebar />

  <!-- Main Chat Area -->
  <section class="main" id="chat-main-section" style="display: none;">
    
    <!-- Topbar Component -->
    <x-chat.topbar />

    <div class="messages" id="msgs"></div>

    <!-- Typing Indicator -->
    <div class="typing-indicator" id="typing-indicator" style="padding: 0 20px 6px;">
      <div class="typing-dots"><span></span><span></span><span></span></div>
      <span id="typing-name">شخص ما يكتب...</span>
    </div>

    <!-- Input Area -->
    <div class="input-area" id="input-area-container" style="padding: 0;">
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
          </div>
          <div style="display: flex; align-items: center; gap: 12px;">
            <div class="input-hint" id="input-hint" style="margin-top: 0; font-size: 10px;">Press Enter to send, Shift + Enter for new line</div>
            <button class="send-btn" id="send-btn"><i class="ti ti-send"></i></button>
            <button class="gmail-send-btn" id="gmail-send-btn" style="display: none;">
              <span>إرسال البريد</span>
              <i class="ti ti-send" style="transform: rotate(180deg); font-size: 12px;"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Empty State -->
  <section class="main" id="empty-state-section" style="display: flex; align-items: center; justify-content: center; text-align: center; padding: 40px;">
    <div style="max-width: 400px; color: var(--color-text-secondary);">
      <i class="ti ti-brand-hipchat" style="font-size: 64px; color: #378ADD; margin-bottom: 20px;"></i>
      <h2 style="font-size: 20px; font-weight: 600; color: var(--color-text-primary); margin-bottom: 10px;">Welcome to CorpChat</h2>
      <p style="font-size: 13px;">Select a conversation from the sidebar or start a new one.</p>
      <button onclick="openComposePanel()" style="margin-top: 16px; padding: 10px 24px; background: #378ADD; color: #fff; border: none; border-radius: 20px; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
        <i class="ti ti-edit"></i> ابدأ محادثة جديدة
      </button>
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
    <div class="compose-header" id="composeDragHandle">
        <span>رسالة جديدة / New Message</span>
        <div class="compose-header-actions">
            <button class="compose-minimize" id="minimizeCompose" title="تصغير">—</button>
            <button class="compose-close" id="closeCompose" title="إغلاق">✕</button>
        </div>
    </div>

    <form id="composeForm" class="compose-body">
        @csrf
        <div class="compose-type-toggle">
            <button type="button" class="type-btn active" data-type="email">✉️ بريد عميل / Client Email</button>
            <button type="button" class="type-btn" data-type="internal">💬 داخلي / Internal</button>
        </div>

        <div class="compose-field" id="toField">
            <label>إلى / To</label>
            <input type="email" name="client_email" placeholder="client@example.com" autocomplete="email" />
        </div>
        <div class="compose-field" id="nameField">
            <label>الاسم / Name</label>
            <input type="text" name="client_name" placeholder="اسم العميل... / Client name..." />
        </div>
        <div class="compose-field hidden" id="toInternalField">
            <label>إلى / To</label>
            <input type="text" name="employee_search" placeholder="ابحث عن موظف... / Search employee..." id="employeeSearch" autocomplete="off" />
            <div id="employeeSuggestions" class="suggestions-dropdown hidden"></div>
        </div>
        <div class="compose-field">
            <label>الموضوع / Subject</label>
            <input type="text" name="subject" placeholder="موضوع الرسالة..." />
        </div>
        <textarea name="body" class="compose-textarea" placeholder="اكتب رسالتك هنا..."></textarea>

        <div class="compose-footer">
            <button type="submit" class="btn-send">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 5px;">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
                إرسال / Send
            </button>
            <div class="compose-footer-right">
                <button type="button" class="btn-attach" title="إرفاق ملف">📎</button>
                <button type="button" class="btn-discard" id="discardCompose" title="حذف المسودة">🗑️</button>
            </div>
        </div>
    </form>
</div>

<!-- Message Bubbles and Cards templates component -->
<x-chat.message-bubble />

<!-- Inject backend configuration for JavaScript via Meta Tag to maintain strict CSP -->
<meta id="corpchat-config" 
      data-csrf-token="{{ csrf_token() }}" 
      data-user-id="{{ auth()->id() }}" 
      data-user-name="{{ auth()->user()->name }}" 
      data-user-email="{{ auth()->user()->email }}" 
      data-reverb-key="{{ config('reverb.apps.0.key') }}">

@vite(['resources/js/chat.js'])

</body>
</html>
