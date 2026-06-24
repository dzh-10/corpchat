import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Expose axios globally
window.axios = axios;
window.Pusher = Pusher;

// App configuration and state parsed from secure meta tag (strict CSP)
const configEl = document.getElementById('corpchat-config');
const config = {
  csrfToken: configEl ? configEl.getAttribute('data-csrf-token') : '',
  userId: configEl ? parseInt(configEl.getAttribute('data-user-id')) : 0,
  userName: configEl ? configEl.getAttribute('data-user-name') : '',
  userEmail: configEl ? configEl.getAttribute('data-user-email') : '',
  reverbKey: configEl ? configEl.getAttribute('data-reverb-key') : ''
};
axios.defaults.headers.common['X-CSRF-TOKEN'] = config.csrfToken;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

// Initialize Echo
window.Echo = new Echo({
  broadcaster: 'reverb',
  key: config.reverbKey,
  wsHost: window.location.hostname,
  wsPort: 8080,
  forceTLS: false,
  enabledTransports: ['ws', 'wss'],
  auth: { headers: { 'X-CSRF-TOKEN': config.csrfToken } }
});

// App State
let conversations = [];
let activeConversation = null;
const currentUserId = config.userId;
const currentUserEmail = config.userEmail;
let currentSubscription = null;
let typingTimer = null;

// Message Pagination State
let messagesCurrentPage = 1;
let messagesHasMore = true;
let messagesLoading = false;

// Sidebar Pagination State
let sidebarCurrentPage = 1;
let sidebarHasMore = true;
let sidebarLoading = false;

// DOM Elements
const internalChatsList  = document.getElementById('internal-chats-list');
const externalEmailsList = document.getElementById('external-emails-list');
const chatMainSection    = document.getElementById('chat-main-section');
const emptyStateSection  = document.getElementById('empty-state-section');
const topbarTitle        = document.getElementById('topbar-title');
const topbarTypeChip     = document.getElementById('topbar-type-chip');
const msgsContainer      = document.getElementById('msgs');
const messageInput       = document.getElementById('message-input');
const sendBtn            = document.getElementById('send-btn');
const gmailSendBtn       = document.getElementById('gmail-send-btn');
const searchInput        = document.getElementById('search-input');
const typingIndicator    = document.getElementById('typing-indicator');
const typingName         = document.getElementById('typing-name');

const composeBtn         = document.getElementById('composeBtn');
const sidebarNewChatBtn  = document.getElementById('new-chat-btn');
const composePanel       = document.getElementById('composePanel');
const closeCompose       = document.getElementById('closeCompose');
const minimizeCompose    = document.getElementById('minimizeCompose');
const discardCompose     = document.getElementById('discardCompose');
const composeForm        = document.getElementById('composeForm');
const typeBtns           = document.querySelectorAll('.type-btn');
const toField            = document.getElementById('toField');
const nameField          = document.getElementById('nameField');
const toInternalField    = document.getElementById('toInternalField');
const employeeSearch     = document.getElementById('employeeSearch');
const suggestions        = document.getElementById('employeeSuggestions');

// ── Toast Notifications System ──
function showToast(message, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `
    <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'}"></i>
    <span>${escapeHtml(message)}</span>
  `;
  container.appendChild(toast);
  setTimeout(() => toast.classList.add('show'), 10);
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ── Helper: Escape HTML ──
function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

// ── Helper: Debounce ──
function debounce(func, wait) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}

// ── Theme Manager (Dark Mode) ──
function initTheme() {
  const toggleBtn = document.getElementById('theme-toggle-btn');
  if (!toggleBtn) return;
  const sunIcon = document.getElementById('sun-icon');
  const moonIcon = document.getElementById('moon-icon');

  const savedTheme = localStorage.getItem('theme') || 'light';
  if (savedTheme === 'dark') {
    document.documentElement.classList.add('dark');
    sunIcon.style.display = 'block';
    moonIcon.style.display = 'none';
  } else {
    document.documentElement.classList.remove('dark');
    sunIcon.style.display = 'none';
    moonIcon.style.display = 'block';
  }

  toggleBtn.addEventListener('click', () => {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    if (isDark) {
      sunIcon.style.display = 'block';
      moonIcon.style.display = 'none';
      showToast('تم تفعيل الوضع الليلي', 'success');
    } else {
      sunIcon.style.display = 'none';
      moonIcon.style.display = 'block';
      showToast('تم تفعيل الوضع المضيء', 'success');
    }
  });
}

// ── Emoji Picker System ──
function initEmojiPickers() {
  const emojis = ['👍', '❤️', '😂', '😮', '😢', '😡', '👏', '🎉', '🔥', '🤔', '👀', '💡', '✅', '❌', '🚀', '💬'];
  
  // Emojis for Chat input
  const chatInputToolbar = document.querySelector('.input-toolbar');
  if (chatInputToolbar) {
    const emojiBtn = document.createElement('button');
    emojiBtn.className = 'tool-btn';
    emojiBtn.type = 'button';
    emojiBtn.innerHTML = '<i class="ti ti-mood-smile"></i> Emoji';
    chatInputToolbar.appendChild(emojiBtn);

    const picker = document.createElement('div');
    picker.className = 'emoji-picker-popover hidden';
    picker.innerHTML = emojis.map(e => `<button class="emoji-btn" type="button">${e}</button>`).join('');
    emojiBtn.style.position = 'relative';
    emojiBtn.appendChild(picker);

    emojiBtn.addEventListener('click', (e) => {
      if (e.target === emojiBtn || emojiBtn.contains(e.target) && !e.target.classList.contains('emoji-btn')) {
        picker.classList.toggle('hidden');
      }
    });

    picker.querySelectorAll('.emoji-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const start = messageInput.selectionStart;
        const end = messageInput.selectionEnd;
        const text = messageInput.value;
        messageInput.value = text.substring(0, start) + btn.textContent + text.substring(end);
        messageInput.focus();
        messageInput.selectionStart = messageInput.selectionEnd = start + btn.textContent.length;
        picker.classList.add('hidden');
      });
    });

    document.addEventListener('click', (e) => {
      if (!emojiBtn.contains(e.target)) picker.classList.add('hidden');
    });
  }

  // Emojis for Compose Window
  const composeFooterRight = document.querySelector('.compose-footer-right');
  if (composeFooterRight) {
    const emojiBtn = document.createElement('button');
    emojiBtn.type = 'button';
    emojiBtn.title = 'إضافة رمز تعبيري';
    emojiBtn.innerHTML = '😀';
    composeFooterRight.prepend(emojiBtn);

    const picker = document.createElement('div');
    picker.className = 'emoji-picker-popover hidden';
    picker.style.right = '0';
    picker.style.bottom = '100%';
    picker.innerHTML = emojis.map(e => `<button class="emoji-btn" type="button">${e}</button>`).join('');
    emojiBtn.style.position = 'relative';
    emojiBtn.appendChild(picker);

    emojiBtn.addEventListener('click', (e) => {
      if (e.target === emojiBtn) {
        picker.classList.toggle('hidden');
      }
    });

    picker.querySelectorAll('.emoji-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const textarea = composeForm.querySelector('textarea[name="body"]');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + btn.textContent + text.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + btn.textContent.length;
        picker.classList.add('hidden');
      });
    });

    document.addEventListener('click', (e) => {
      if (!emojiBtn.contains(e.target)) picker.classList.add('hidden');
    });
  }
}

// ── Mobile Navigation System ──
function initMobileNavigation() {
  const backBtn = document.createElement('button');
  backBtn.className = 'back-mobile-btn';
  backBtn.innerHTML = '<i class="ti ti-arrow-right"></i>';
  
  const topbar = document.querySelector('.topbar');
  if (topbar) {
    topbar.prepend(backBtn);
  }

  backBtn.addEventListener('click', () => {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.remove('hidden-mobile');
    chatMainSection.classList.remove('active-mobile');
  });
}

function selectConversationMobile() {
  if (window.innerWidth <= 768) {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.add('hidden-mobile');
    chatMainSection.classList.add('active-mobile');
  }
}

// ── Fetch Conversations (Cached on backend + paginated) ──
async function fetchConversations(selectId = null, page = 1, append = false) {
  if (sidebarLoading) return;
  sidebarLoading = true;
  try {
    const response = await axios.get(`/api/conversations?page=${page}`);
    const data = response.data.data;
    
    if (append) {
      conversations = conversations.concat(data);
    } else {
      conversations = data;
    }
    
    sidebarHasMore = response.data.meta.current_page < response.data.meta.last_page;
    sidebarCurrentPage = response.data.meta.current_page;

    renderConversations();

    if (selectId) {
      selectConversation(selectId);
    } else if (activeConversation) {
      const updated = conversations.find(c => c.id === activeConversation.id);
      if (updated) activeConversation = updated;
    }
  } catch (err) {
    console.error('Error fetching conversations:', err);
    showToast('فشل تحميل المحادثات', 'error');
  } finally {
    sidebarLoading = false;
  }
}

// ── Lazy load sidebar conversations on scroll ──
function setupSidebarScroll() {
  const lists = [internalChatsList, externalEmailsList];
  lists.forEach(list => {
    list.addEventListener('scroll', () => {
      if (list.scrollTop + list.clientHeight >= list.scrollHeight - 10 && sidebarHasMore && !sidebarLoading) {
        fetchConversations(null, sidebarCurrentPage + 1, true);
      }
    });
  });
}

// ── Render sidebar list ──
function renderConversations(filter = '') {
  internalChatsList.innerHTML = '';
  externalEmailsList.innerHTML = '';
  const colors = ['dot-blue', 'dot-teal', 'dot-coral', 'dot-purple', 'dot-amber'];

  conversations.forEach((conv, index) => {
    const title = conv.type === 'internal_chat'
      ? (conv.subject || 'Internal Group')
      : `${conv.external_contact_name || 'Client'} (${conv.subject || 'No Subject'})`;

    if (filter && !title.toLowerCase().includes(filter)) return;

    const latestMsg = conv.messages && conv.messages.length > 0
      ? conv.messages[0].body
      : 'No messages yet';

    const isActive    = activeConversation && activeConversation.id === conv.id;
    const colorClass  = colors[index % colors.length];
    const time        = conv.updated_at ? new Date(conv.updated_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
    const unreadCount = conv.unread_count || 0;
    const badgeHtml   = unreadCount > 0 ? `<span class="unread-badge">${unreadCount}</span>` : '';

    let itemHtml = '';
    if (conv.type === 'external_email') {
      const clientName = escapeHtml(conv.external_contact_name || 'Client');
      const subject    = escapeHtml(conv.subject || 'No Subject');
      const preview    = escapeHtml(latestMsg);
      itemHtml = `
        <div class="conv-item ${isActive ? 'active' : ''}" data-id="${conv.id}" style="align-items: flex-start; padding: 10px 12px;">
          <div class="dot ${colorClass}" style="margin-top: 5px;"></div>
          <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <span style="font-size: 13px; font-weight: 600; color: var(--color-text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70%;">${clientName}</span>
              <span style="font-size: 10px; color: var(--color-text-tertiary);">${time}</span>
            </div>
            <div style="font-size: 11px; font-weight: 500; color: var(--color-text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${subject}</div>
            <div style="font-size: 11px; color: var(--color-text-tertiary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display:flex; justify-content:space-between;">
              <span>${preview}</span>${badgeHtml}
            </div>
          </div>
        </div>`;
    } else {
      const safeTitle   = escapeHtml(title);
      const safePreview = escapeHtml(latestMsg);
      itemHtml = `
        <div class="conv-item ${isActive ? 'active' : ''}" data-id="${conv.id}">
          <div class="dot ${colorClass}"></div>
          <div style="flex: 1; min-width: 0;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <div class="conv-title">${safeTitle}</div>
              ${badgeHtml}
            </div>
            <div style="font-size: 11px; color: var(--color-text-tertiary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${safePreview}</div>
          </div>
        </div>`;
    }

    const container = conv.type === 'internal_chat' ? internalChatsList : externalEmailsList;
    container.insertAdjacentHTML('beforeend', itemHtml);
  });

  // Attach click events
  document.querySelectorAll('.conv-item').forEach(item => {
    item.addEventListener('click', () => {
      const id = parseInt(item.getAttribute('data-id'));
      selectConversation(id);
    });
  });
}

// ── Select conversation ──
async function selectConversation(id) {
  const selected = conversations.find(c => c.id === id);
  if (!selected) return;

  activeConversation = selected;
  renderConversations();
  selectConversationMobile();

  emptyStateSection.style.display = 'none';
  chatMainSection.style.display = 'flex';

  topbarTitle.textContent = selected.type === 'internal_chat'
    ? (selected.subject || 'Internal Group')
    : `${selected.external_contact_name || 'Client'} (${selected.subject || 'No Subject'})`;

  topbarTypeChip.innerHTML = selected.type === 'internal_chat'
    ? `<i class="ti ti-brand-hipchat"></i> محادثة داخلية / Internal Chat`
    : `<i class="ti ti-mail"></i> بريد خارجي / Client Email`;

  const emailHeader  = document.getElementById('email-compose-header');
  const normalSendBtn = document.getElementById('send-btn');
  const inputHint    = document.getElementById('input-hint');

  if (selected.type === 'external_email') {
    emailHeader.style.display = 'flex';
    document.getElementById('email-compose-to').textContent = `${selected.external_contact_name || 'Client'} <${selected.external_contact_email}>`;
    document.getElementById('email-compose-subject').textContent = selected.subject || 'No Subject';
    gmailSendBtn.style.display = 'inline-flex';
    normalSendBtn.style.display = 'none';
    inputHint.style.display = 'none';
    messageInput.placeholder = 'اكتب رسالة البريد الإلكتروني هنا...';
    messageInput.style.minHeight = '120px';
  } else {
    emailHeader.style.display = 'none';
    gmailSendBtn.style.display = 'none';
    normalSendBtn.style.display = 'flex';
    inputHint.style.display = 'block';
    messageInput.placeholder = 'Type your message here...';
    messageInput.style.minHeight = '48px';
  }

  showLoading();

  // Reset message pagination
  messagesCurrentPage = 1;
  messagesHasMore = true;
  messagesLoading = false;
  msgsContainer.innerHTML = '';

  await loadMessages(id, 1);
  subscribeToChannel(id);
}

// ── Load messages with pagination ──
async function loadMessages(convId, page = 1) {
  if (messagesLoading) return;
  messagesLoading = true;
  try {
    const response = await axios.get(`/api/conversations/${convId}/messages?page=${page}`);
    const resMessages = response.data.data;
    
    messagesHasMore = response.data.meta.current_page < response.data.meta.last_page;
    messagesCurrentPage = response.data.meta.current_page;

    if (page === 1) {
      msgsContainer.innerHTML = '';
    }

    const previousScrollHeight = msgsContainer.scrollHeight;

    // We prepend messages when loading older history (page > 1)
    resMessages.forEach(msg => appendMessageMarkup(msg, page > 1));

    if (page === 1) {
      scrollToBottom();
    } else {
      // Maintain scroll position after prepending
      msgsContainer.scrollTop = msgsContainer.scrollHeight - previousScrollHeight;
    }
  } catch (err) {
    console.error('Error loading messages:', err);
    if (page === 1) {
      msgsContainer.innerHTML = '<div class="loading-state" style="color:#dc2626;">فشل تحميل الرسائل</div>';
    }
  } finally {
    messagesLoading = false;
  }
}

// ── Setup infinite scroll up for older messages ──
function setupMessagesInfiniteScroll() {
  msgsContainer.addEventListener('scroll', () => {
    if (msgsContainer.scrollTop === 0 && messagesHasMore && !messagesLoading && activeConversation) {
      loadMessages(activeConversation.id, messagesCurrentPage + 1);
    }
  });
}

// ── Show loading state in message container ──
function showLoading() {
  msgsContainer.innerHTML = `
    <div class="loading-state">
      <div class="spinner"></div>
      <span>جاري التحميل...</span>
    </div>`;
}

// ── WebSocket subscription ──
function subscribeToChannel(convId) {
  if (currentSubscription) {
    window.Echo.leave(`conversation.${currentSubscription}`);
  }
  currentSubscription = convId;
  window.Echo.private(`conversation.${convId}`)
    .listen('.message.sent', (e) => {
      if (e.message && e.message.conversation_id === convId) {
        appendMessageMarkup(e.message, false);
        scrollToBottom();
        fetchConversations();
      }
    })
    .listen('.message.reaction_updated', (e) => {
      if (e.message && e.message.conversation_id === convId) {
        updateMessageReactionsMarkup(e.message.id, e.message.reactions);
      }
    })
    .listenForWhisper('typing', (e) => {
      if (e.name) {
        typingName.textContent = `${escapeHtml(e.name)} يكتب...`;
        typingIndicator.classList.add('visible');
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => typingIndicator.classList.remove('visible'), 2500);
      }
    });
}

// ── Render message markup using Blade Template blocks ──
function appendMessageMarkup(msg, prepend = false) {
  const existing = document.querySelector(`[data-msg-id="${msg.id}"]`);
  const isUser = msg.sender_id === currentUserId;
  const time = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
  
  let statusText = '';
  if (isUser) {
    if (msg.status === 'sending')   statusText = ' · Sending...';
    else if (msg.status === 'sent') statusText = ' · Sent';
    else if (msg.status === 'delivered') statusText = ' · Delivered';
    else if (msg.status === 'failed')    statusText = ' · Failed';
  }

  // Update existing status
  if (existing) {
    const meta = existing.querySelector('.bubble-meta') || existing.querySelector('.email-card-meta');
    if (meta) {
      meta.textContent = activeConversation && activeConversation.type === 'external_email'
        ? `${time}${statusText}`
        : `${msg.sender_name} · ${time}${statusText}`;
    }
    updateMessageReactionsMarkup(msg.id, msg.reactions);
    return;
  }

  const initial = msg.sender_name ? msg.sender_name.substring(0, 2).toUpperCase() : 'US';
  const safeBody = escapeHtml(msg.body);
  const safeSender = escapeHtml(msg.sender_name);

  let tempNode;
  if (activeConversation && activeConversation.type === 'external_email') {
    const template = document.getElementById('email-card-template');
    const clone = template.content.cloneNode(true);
    
    tempNode = clone.querySelector('.email-card');
    tempNode.setAttribute('data-msg-id', msg.id);
    tempNode.querySelector('.email-card-sender').textContent = safeSender;
    
    const senderEmail = msg.sender_email || (isUser ? currentUserEmail : activeConversation.external_contact_email);
    tempNode.querySelector('.email-card-email').textContent = `<${senderEmail}>`;
    
    const metaEl = tempNode.querySelector('.email-card-meta');
    metaEl.textContent = `${time}${statusText}`;
    if (msg.status === 'failed') {
      metaEl.style.color = '#dc2626';
    }
    
    tempNode.querySelector('.email-card-body').innerHTML = safeBody;

    // Attachments link
    if (msg.attachment_path) {
      tempNode.querySelector('.attachment-link-container').innerHTML = `
        <a href="${msg.attachment_path}" target="_blank" class="attachment-badge" style="color:#1a73e8;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
          <i class="ti ti-paperclip"></i> ${escapeHtml(msg.attachment_name || 'Attachment')}
        </a>
      `;
    }
  } else {
    const template = document.getElementById('msg-row-template');
    const clone = template.content.cloneNode(true);
    
    tempNode = clone.querySelector('.msg-row');
    tempNode.setAttribute('data-msg-id', msg.id);
    
    if (isUser) {
      tempNode.classList.add('user');
    }
    
    const avatar = tempNode.querySelector('.msg-avatar');
    avatar.textContent = initial;
    avatar.classList.add(isUser ? 'user-av' : 'ai');

    tempNode.querySelector('.bubble').textContent = msg.body;
    if (isUser) {
      tempNode.querySelector('.bubble').classList.add('user');
    } else {
      tempNode.querySelector('.bubble').classList.add('ai');
    }

    tempNode.querySelector('.bubble-meta').textContent = `${safeSender} · ${time}${statusText}`;

    // Attachments link
    if (msg.attachment_path) {
      const attachDiv = document.createElement('div');
      attachDiv.style.marginTop = '6px';
      attachDiv.innerHTML = `
        <a href="${msg.attachment_path}" target="_blank" style="font-size:12px;color:${isUser ? '#b5d4f4' : '#1a73e8'};text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
          <i class="ti ti-paperclip"></i> ${escapeHtml(msg.attachment_name || 'Attachment')}
        </a>
      `;
      tempNode.querySelector('.bubble').appendChild(attachDiv);
    }
  }

  // Populate reactions
  populateReactionsMarkup(tempNode.querySelector('.reactions-container'), msg.id, msg.reactions);

  // Setup click reactions toggles
  tempNode.querySelectorAll('.react-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      toggleReaction(msg.id, btn.getAttribute('data-reaction'));
    });
  });

  if (prepend) {
    msgsContainer.insertBefore(tempNode, msgsContainer.firstChild);
  } else {
    msgsContainer.appendChild(tempNode);
  }
}

// ── Render Reactions Badges ──
function populateReactionsMarkup(container, msgId, reactions = []) {
  container.innerHTML = '';
  if (!reactions || reactions.length === 0) return;

  // Group reactions by emoji type
  const grouped = {};
  reactions.forEach(r => {
    if (!grouped[r.reaction]) grouped[r.reaction] = [];
    grouped[r.reaction].push(r);
  });

  Object.keys(grouped).forEach(emoji => {
    const list = grouped[emoji];
    const userReacted = list.some(r => r.user_id === currentUserId);
    const names = list.map(r => r.user_name).join(', ');

    const badge = document.createElement('span');
    badge.className = `reaction-badge ${userReacted ? 'active' : ''}`;
    badge.title = names;
    badge.innerHTML = `<span>${emoji}</span> <span style="font-weight:600;">${list.length}</span>`;
    
    badge.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      toggleReaction(msgId, emoji);
    });

    container.appendChild(badge);
  });
}

function updateMessageReactionsMarkup(msgId, reactions) {
  const row = document.querySelector(`[data-msg-id="${msgId}"]`);
  if (row) {
    const container = row.querySelector('.reactions-container');
    if (container) {
      populateReactionsMarkup(container, msgId, reactions);
    }
  }
}

// ── Toggle Reaction to Database ──
async function toggleReaction(msgId, emoji) {
  if (!activeConversation) return;
  try {
    const response = await axios.post(`/api/conversations/${activeConversation.id}/messages/${msgId}/reactions`, {
      reaction: emoji
    });
    if (response.data.status === 'success') {
      updateMessageReactionsMarkup(msgId, response.data.data.reactions);
    }
  } catch (err) {
    console.error('Error toggling reaction:', err);
    showToast('فشل تعديل التفاعل', 'error');
  }
}

// ── Send Message ──
async function sendMessage() {
  const text = messageInput.value.trim();
  if (!text || !activeConversation) return;

  // Check character limit (5000)
  if (text.length > 5000) {
    showToast('الرسالة طويلة جداً، الحد الأقصى 5000 حرف', 'error');
    return;
  }

  messageInput.value = '';

  const tempId = 'temp-' + Date.now();
  const tempMsg = {
    id: tempId,
    conversation_id: activeConversation.id,
    sender_id: currentUserId,
    sender_name: config.userName,
    sender_email: currentUserEmail,
    body: text,
    status: 'sending',
    created_at: new Date().toISOString(),
    reactions: []
  };
  appendMessageMarkup(tempMsg);
  scrollToBottom();

  try {
    const response = await axios.post(`/api/conversations/${activeConversation.id}/messages`, { body: text });
    const savedMsg = response.data.data;
    
    const tempElement = document.querySelector(`[data-msg-id="${tempId}"]`);
    if (tempElement) {
      tempElement.setAttribute('data-msg-id', savedMsg.id);
      
      // Update quick reaction listeners to use new id
      tempElement.querySelectorAll('.react-btn').forEach(btn => {
        // Clone and replace button to purge old listeners
        const clone = btn.cloneNode(true);
        btn.parentNode.replaceChild(clone, btn);
        clone.addEventListener('click', (e) => {
          e.preventDefault();
          toggleReaction(savedMsg.id, clone.getAttribute('data-reaction'));
        });
      });

      const meta = tempElement.querySelector('.bubble-meta') || tempElement.querySelector('.email-card-meta');
      const t = new Date(savedMsg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      if (meta) {
        const st = activeConversation.type === 'external_email' ? ' · Sending...' : ' · Delivered';
        meta.textContent = activeConversation.type === 'external_email'
          ? `${t}${st}`
          : `${savedMsg.sender_name} · ${t}${st}`;
      }
    }
    fetchConversations();
  } catch (err) {
    console.error('Error sending message:', err);
    const tempElement = document.querySelector(`[data-msg-id="${tempId}"]`);
    if (tempElement) {
      const meta = tempElement.querySelector('.bubble-meta') || tempElement.querySelector('.email-card-meta');
      if (meta) {
        meta.textContent = 'Failed to send';
        meta.style.color = '#dc2626';
      }
    }
    const msg = err.response && err.response.data && err.response.data.message 
      ? err.response.data.message 
      : 'فشل إرسال الرسالة';
    showToast(msg, 'error');
  }
}

// ── Typing whisper ──
let typingWhisperTimeout;
function setupTypingWhisper() {
  messageInput.addEventListener('input', () => {
    if (!activeConversation || activeConversation.type === 'external_email') return;
    clearTimeout(typingWhisperTimeout);
    typingWhisperTimeout = setTimeout(() => {
      window.Echo.private(`conversation.${activeConversation.id}`)
        .whisper('typing', { name: config.userName });
    }, 300);
  });
}

function scrollToBottom() {
  msgsContainer.scrollTop = msgsContainer.scrollHeight;
}

// ── File Attachments upload handling via toolbar click ──
function initFileAttachmentHandlers() {
  const attachChatBtn = document.querySelector('.input-toolbar .tool-btn:first-child');
  const attachComposeBtn = document.querySelector('.compose-footer-right .btn-attach');

  // Inject hidden file inputs
  const chatFileInput = document.createElement('input');
  chatFileInput.type = 'file';
  chatFileInput.className = 'hidden';
  document.body.appendChild(chatFileInput);

  const composeFileInput = document.createElement('input');
  composeFileInput.type = 'file';
  composeFileInput.name = 'attachment';
  composeFileInput.className = 'hidden';
  composeForm.appendChild(composeFileInput);

  if (attachChatBtn) {
    attachChatBtn.addEventListener('click', () => chatFileInput.click());
  }
  if (attachComposeBtn) {
    attachComposeBtn.addEventListener('click', () => composeFileInput.click());
  }

  // Show status feedback on attachment select
  chatFileInput.addEventListener('change', async () => {
    const file = chatFileInput.files[0];
    if (!file) return;

    // Client validation: Max 5MB
    if (file.size > 5 * 1024 * 1024) {
      showToast('حجم الملف كبير جداً، الحد الأقصى 5 ميجابايت', 'error');
      chatFileInput.value = '';
      return;
    }

    if (!activeConversation) return;

    const formData = new FormData();
    formData.append('body', `أرفق ملفاً: ${file.name}`);
    formData.append('attachment', file);

    showToast('جاري رفع الملف...', 'info');

    try {
      const response = await axios.post(`/api/conversations/${activeConversation.id}/messages`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      if (response.data.data) {
        appendMessageMarkup(response.data.data);
        scrollToBottom();
        fetchConversations();
        showToast('تم إرسال الملف بنجاح', 'success');
      }
    } catch (err) {
      console.error('Error uploading attachment:', err);
      const msg = err.response && err.response.data && err.response.data.message 
        ? err.response.data.message 
        : 'فشل رفع الملف';
      showToast(msg, 'error');
    } finally {
      chatFileInput.value = '';
    }
  });

  composeFileInput.addEventListener('change', () => {
    const file = composeFileInput.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
      showToast('حجم الملف كبير جداً، الحد الأقصى 5 ميجابايت', 'error');
      composeFileInput.value = '';
      return;
    }
    
    showToast(`تم إرفاق: ${file.name}`, 'success');
  });
}

// ── Compose Panel Actions ──
let currentType = 'email';

function openComposePanel() {
  composePanel.classList.remove('hidden', 'minimized');
  composeBtn.classList.add('hidden');
  const activeInput = currentType === 'email'
    ? composePanel.querySelector('input[name="client_email"]')
    : composePanel.querySelector('input[name="employee_search"]');
  if (activeInput) activeInput.focus();
}

function closeComposePanel() {
  composePanel.classList.add('hidden');
  composeBtn.classList.remove('hidden');
}

// ── Autocomplete search employees (Debounced) ──
function initEmployeeSearch() {
  let searchTimeout;
  employeeSearch.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    const q = employeeSearch.value.trim();
    if (q.length < 2) { suggestions.classList.add('hidden'); return; }
    searchTimeout = setTimeout(async () => {
      try {
        const res = await axios.get(`/api/employees/search?q=${encodeURIComponent(q)}`);
        const employees = res.data.data || [];
        
        suggestions.innerHTML = employees.map(emp => `
          <div class="suggestion-item" data-id="${emp.id}" data-name="${escapeHtml(emp.name)}">
            <div class="suggestion-avatar">${emp.name.charAt(0).toUpperCase()}</div>
            <div>
              <div style="font-weight:600">${escapeHtml(emp.name)}</div>
              <div style="font-size:12px;color:#888">${escapeHtml(emp.email)}</div>
            </div>
          </div>`).join('');
        suggestions.classList.toggle('hidden', employees.length === 0);
        suggestions.querySelectorAll('.suggestion-item').forEach(item => {
          item.addEventListener('click', () => {
            employeeSearch.value = item.dataset.name;
            employeeSearch.dataset.selectedId = item.dataset.id;
            suggestions.classList.add('hidden');
          });
        });
      } catch (err) { console.error('Error fetching employees:', err); }
    }, 300);
  });
}

// ── Initialize Event Listeners ──
function initListeners() {
  // Search conversations (Debounced)
  searchInput.addEventListener('input', debounce(e => renderConversations(e.target.value.toLowerCase()), 250));

  if (composeBtn) composeBtn.addEventListener('click', openComposePanel);
  if (sidebarNewChatBtn) sidebarNewChatBtn.addEventListener('click', (e) => { e.preventDefault(); openComposePanel(); });

  closeCompose.addEventListener('click', closeComposePanel);
  discardCompose.addEventListener('click', () => {
    if (confirm('هل تريد حذف المسودة؟ / Discard draft?')) {
      composeForm.reset();
      const fileInput = composeForm.querySelector('input[name="attachment"]');
      if (fileInput) fileInput.value = '';
      if (employeeSearch) employeeSearch.dataset.selectedId = '';
      closeComposePanel();
    }
  });

  minimizeCompose.addEventListener('click', () => composePanel.classList.toggle('minimized'));
  document.getElementById('composeDragHandle').addEventListener('dblclick', () => composePanel.classList.toggle('minimized'));

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

  // Compose form submission
  composeForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (currentType === 'email') {
      const clientEmail = composeForm.querySelector('input[name="client_email"]').value.trim();
      if (!clientEmail) { showToast('يرجى إدخال البريد الإلكتروني للعميل', 'error'); return; }
    } else {
      const selectedId = employeeSearch.dataset.selectedId;
      if (!selectedId) { showToast('يرجى تحديد موظف من القائمة', 'error'); return; }
    }
    const bodyText = composeForm.querySelector('textarea[name="body"]').value.trim();
    if (!bodyText) { showToast('يرجى كتابة نص الرسالة', 'error'); return; }

    if (bodyText.length > 5000) {
      showToast('الرسالة طويلة جداً، الحد الأقصى 5000 حرف', 'error');
      return;
    }

    const submitBtn = composeForm.querySelector('.btn-send');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ جاري الإرسال...';

    const formData = new FormData(composeForm);
    formData.append('type', currentType);
    if (currentType === 'internal' && employeeSearch.dataset.selectedId) {
      formData.append('recipient_id', employeeSearch.dataset.selectedId);
    }
    try {
      const res = await axios.post('/api/conversations', formData);
      const data = res.data;
      if (data.data) {
        composeForm.reset();
        const fileInput = composeForm.querySelector('input[name="attachment"]');
        if (fileInput) fileInput.value = '';
        employeeSearch.dataset.selectedId = '';
        closeComposePanel();
        showToast('تم إنشاء المحادثة وإرسال الرسالة', 'success');
        
        await fetchConversations(data.data.id);
      } else {
        showToast('حدث خطأ: ' + (data.message || 'فشلت العملية'), 'error');
      }
    } catch (err) {
      const msg = err.response && err.response.data && err.response.data.message 
        ? err.response.data.message 
        : 'فشل الاتصال بالخادم';
      showToast(msg, 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left:5px;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> إرسال / Send`;
    }
  });

  // Hide suggestions list when clicking outside
  document.addEventListener('click', (e) => {
    if (!toInternalField.contains(e.target)) suggestions.classList.add('hidden');
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeComposePanel();
  });

  // Chat message keys
  messageInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
  });
  sendBtn.addEventListener('click', sendMessage);
  if (gmailSendBtn) gmailSendBtn.addEventListener('click', sendMessage);

  // Drag compose panel
  let isDragging = false, startX, startY, initialRight, initialBottom;
  const dragHandle = document.getElementById('composeDragHandle');
  dragHandle.addEventListener('mousedown', (e) => {
    if (e.target.tagName === 'BUTTON') return;
    isDragging = true; dragHandle.style.cursor = 'grabbing';
    startX = e.clientX; startY = e.clientY;
    const rect = composePanel.getBoundingClientRect();
    initialRight  = window.innerWidth - rect.right;
    initialBottom = window.innerHeight - rect.bottom;
    e.preventDefault();
  });
  document.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    composePanel.style.right  = `${initialRight  - (e.clientX - startX)}px`;
    composePanel.style.bottom = `${initialBottom - (e.clientY - startY)}px`;
  });
  document.addEventListener('mouseup', () => {
    if (isDragging) { isDragging = false; dragHandle.style.cursor = 'grab'; }
  });
}

// ── DOMContentLoaded Init ──
document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  initEmojiPickers();
  initMobileNavigation();
  initFileAttachmentHandlers();
  initEmployeeSearch();
  initListeners();
  
  // Setup infinite scroll up for message history
  setupMessagesInfiniteScroll();
  
  // Setup lazy load scrolling on sidebar
  setupSidebarScroll();

  // Load conversations initially
  fetchConversations();
  
  // Expose function for empty state button
  window.openComposePanel = openComposePanel;
});
