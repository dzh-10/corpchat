# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-06-24

### Added
- **Sanctum Authentication**: API routes are now secured using Laravel Sanctum stateful and token authentication.
- **Message Pagination**: Implemented scroll-up infinite loading for messages (20 messages per page) to optimize performance.
- **Message Reactions**: Enabled 👍 and ❤️ reactions on messages, stored in database and synchronized via Reverb WebSockets.
- **Attachments System**: Support for file attachments in chat messages and compose windows, validated (max 5MB, selected file types).
- **Dark Mode**: Complete CSS dark mode toggle system matching the light theme layout, persisting states in local storage.
- **Toast Notifications**: Reusable visual success and error toast alerts replacing primitive browser alerts.
- **Emoji Picker**: Built-in floating emoji pickers on input textareas.
- **Health Check Endpoint**: Public `/health` route returning MySQL, Redis, and Reverb statuses.
- **CI Pipeline**: GitHub Actions workflow verifying PHPUnit, Pint, and Vite asset builds.
- **Code Linters**: Configured ESLint and Prettier for JS code formatting.

### Changed
- **Vite Asset Separation**: Refactored all inline CSS and JS from Blade templates into independent compilation units (`resources/css/chat.css` and `resources/js/chat.js`).
- **Blade Componentization**: Moved layout structures into modular components (`sidebar`, `topbar`, `message-bubble`).
- **Unified API Schema**: All endpoints return unified JSON structures using `JsonResource`.
- **Form Requests**: Extracted validation rules from controller closures into dedicated `StoreConversationRequest` and `StoreMessageRequest` classes.
- **Security Headers**: Deployed Content Security Policy (CSP), X-Frame-Options, X-Content-Type-Options, and Referrer-Policy on Nginx.

### Fixed
- Fixed unversioned CDN assets by compiling them locally or locking CDN references.
- Fixed layout responsiveness on mobile screens (fully fluid sidebar/chat toggling).

---

## [1.0.0] - 2026-06-22

### Added
- Initial release with Laravel 12 + Filament v3 administration dashboard.
- Realtime messaging using Laravel Reverb.
- Outbound SMTP email dispatching and inbound IMAP parsing.
- Gmail-style compose panels.
