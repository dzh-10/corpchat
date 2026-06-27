@php
  $authUser = auth()->user();
  $initials = $authUser ? strtoupper(substr($authUser->name, 0, 2)) : 'AH';
@endphp

<aside class="sidebar">

  {{-- Header --}}
  <div class="sidebar-header">
    <span>{{ __('conversations') }}</span>
    <button class="new-btn" id="new-chat-btn" title="New conversation">
      <i class="ti ti-edit"></i>
    </button>
  </div>

  {{-- Search --}}
  <div class="search-wrap">
    <i class="ti ti-search"></i>
    <input type="text" id="search-input"
           placeholder="{{ __('search_placeholder') }}">
  </div>

  {{-- Folders --}}
  <nav class="folder-nav" id="folder-nav">
    <a href="#" class="folder-item active" data-folder="inbox" id="folder-inbox">
      <i class="ti ti-inbox"></i>
      <span>Inbox</span>
      <span class="badge" id="badge-inbox" style="display:none"></span>
    </a>
    <a href="#" class="folder-item" data-folder="starred" id="folder-starred">
      <i class="ti ti-star"></i>
      <span>Starred</span>
    </a>
    <a href="#" class="folder-item" data-folder="snoozed" id="folder-snoozed">
      <i class="ti ti-clock"></i>
      <span>Snoozed</span>
    </a>
    <a href="#" class="folder-item" data-folder="sent" id="folder-sent">
      <i class="ti ti-send"></i>
      <span>Sent</span>
    </a>
    <a href="#" class="folder-item" data-folder="drafts" id="folder-drafts">
      <i class="ti ti-file"></i>
      <span>Drafts</span>
      <span class="badge" id="badge-drafts" style="display:none"></span>
    </a>
  </nav>

  {{-- Labels --}}
  <div class="sidebar-section-header">
    <button class="section-toggle" id="labels-toggle" aria-expanded="true">
      <i class="ti ti-chevron-down"></i>
    </button>
    <span>التصنيفات / LABELS / ÉTIQUETTES</span>
  </div>
  <div class="labels-list" id="labels-list">
    {{-- Populated by JS via GET /api/labels --}}
  </div>

  {{-- Spam / Trash --}}
  <a href="#" class="folder-item" data-folder="spam" id="folder-spam">
    <i class="ti ti-alert-circle"></i>
    <span>Spam</span>
    <span class="badge" id="badge-spam" style="display:none"></span>
  </a>
  <a href="#" class="folder-item" data-folder="trash" id="folder-trash">
    <i class="ti ti-trash"></i>
    <span>Trash</span>
  </a>

  {{-- Mail List --}}
  <div class="sidebar-section-header">
    <button class="new-btn" id="new-mail-list-btn" title="New mail list">
      <i class="ti ti-plus"></i>
    </button>
    <span>قائمة بريدية / Mail List / Liste de diffusion</span>
  </div>

  {{-- Footer --}}
  <div class="sidebar-footer">
    <div class="avatar-sm">{{ $initials }}</div>
    <div>
      <div class="avatar-name">{{ $authUser->name }}</div>
      <div class="avatar-role">{{ $authUser->is_admin ? 'Admin' : 'Employee' }}</div>
    </div>
    @if($authUser->is_admin)
      <a href="/admin" class="new-btn" title="Settings">
        <i class="ti ti-settings"></i>
      </a>
    @endif
    <form action="{{ route('logout') }}" method="POST"
          style="margin-left:{{ $authUser->is_admin ? '0' : 'auto' }}; display:inline-flex;">
      @csrf
      <button type="submit" class="new-btn" title="Logout">
        <i class="ti ti-logout"></i>
      </button>
    </form>
  </div>

</aside>
