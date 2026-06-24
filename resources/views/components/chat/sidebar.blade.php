@php
  $authUser = auth()->user();
  $initials = $authUser ? strtoupper(substr($authUser->name, 0, 2)) : 'AH';
@endphp

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
  <div class="conv-list" id="internal-chats-list"></div>

  <div class="section-label" style="padding-left:8px">البريد الخارجي / Client Emails</div>
  <div class="conv-list" id="external-emails-list"></div>

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
