<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo"/>
</p>

<h1 align="center">CorpChat</h1>
<p align="center">
  <strong>Enterprise Hybrid Messaging System — نظام المراسلة المؤسسي المتكامل</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Filament-v3-FDAE4B?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/Redis-Cache%20%26%20Queue-DC382D?style=for-the-badge&logo=redis&logoColor=white"/>
  <img src="https://img.shields.io/badge/WebSockets-Laravel%20Reverb-6C3483?style=for-the-badge"/>
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge"/>
</p>

---

> CorpChat is a unified enterprise messaging platform that combines **real-time internal team chat** with **external client email management** in a single, modern interface.

---

## ✨ Key Features

| Feature | Description |
|---|---|
| 💬 **Real-time Chat** | Instant messaging via Laravel Reverb (WebSockets) with send/read indicators |
| 📧 **Email Integration** | Full IMAP/SMTP sync with Hostinger for client email management |
| 🛡️ **Admin Panel** | Complete Filament v3 dashboard for users, conversations & settings |
| ⚙️ **Settings Hub** | 7 settings pages: General, Mail, Chat, Notifications, Security, Appearance, Reverb |
| 🐳 **Docker Ready** | One-command setup with Docker Compose (6 services) |
| ⚡ **Queue Worker** | Redis-powered background jobs for email sync & notifications |

---

## 🛠️ Tech Stack

```
Backend   →  Laravel 12.x + PHP 8.3
Frontend  →  Blade + Vanilla JS (Laravel Echo) + CSS3 (Glassmorphism)
Database  →  MySQL 8.0
Cache     →  Redis
WebSocket →  Laravel Reverb
Admin     →  Filament PHP v3
Email     →  webklex/laravel-imap (IMAP) + SMTP
Deploy    →  Docker Compose + Nginx
```

---

## 🚀 Quick Start

### 1. Clone the repository

```bash
git clone https://github.com/dzh-10/corpchat.git
cd corpchat
```

### 2. Configure environment

```bash
cp .env.example .env
```

Edit `.env` with your credentials:

```env
APP_NAME=CorpChat
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=corpchat
DB_USERNAME=corpchat
DB_PASSWORD=secret

# WebSocket (Laravel Reverb)
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=corpchat_id
REVERB_APP_KEY=corpchat_key
REVERB_APP_SECRET=corpchat_secret
REVERB_HOST=reverb-server
REVERB_PORT=8080
REVERB_SCHEME=http

# SMTP — Outgoing Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your_email@company.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your_email@company.com

# IMAP — Incoming Mail
IMAP_HOST=imap.hostinger.com
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_VALIDATE_CERT=true
IMAP_USERNAME=your_email@company.com
IMAP_PASSWORD=your_password
IMAP_DEFAULT_ACCOUNT=default
```

### 3. Launch with Docker

```bash
docker compose up -d --build
```

This starts **6 services** automatically:

| Container | Role | Port |
|---|---|---|
| `corpchat_web` | Nginx Web Server | `80` |
| `corpchat_app` | Laravel App (PHP-FPM) | — |
| `corpchat_db` | MySQL 8.0 | `3306` |
| `corpchat_redis` | Cache & Queue | `6379` |
| `corpchat_reverb` | WebSocket Server | `8080` |
| `corpchat_queue_worker` | Background Jobs | — |

### 4. Access the application

| URL | Description |
|---|---|
| [http://localhost](http://localhost) | Employee Chat Portal |
| [http://localhost/admin](http://localhost/admin) | Admin Dashboard |

**Default admin credentials:**
```
Email:    admin@corpchat.test
Password: password
```

### 5. Sync incoming emails (IMAP)

```bash
docker exec corpchat_app php artisan emails:sync
```

> 💡 Add this to a Cron Job to auto-sync every minute.

---

## 📁 Project Structure

```
corpchat/
├── app/
│   ├── Console/Commands/        # emails:sync, fetch-inbound
│   ├── Events/                  # MessageSent (WebSocket broadcast)
│   ├── Filament/
│   │   ├── Pages/Settings/      # 7 settings pages
│   │   └── Resources/           # Users, Conversations, Messages
│   ├── Jobs/                    # SendOutboundEmailJob
│   ├── Models/                  # User, Conversation, Message
│   └── Settings/                # Spatie Settings classes
├── database/
│   ├── migrations/
│   └── settings/                # Settings migrations
├── resources/views/
│   ├── auth/login.blade.php
│   └── chat.blade.php           # Main chat interface
├── docker-compose.yml
├── Dockerfile.app
└── nginx.conf
```

---

## ⚙️ Admin Settings Pages

| Page | Settings |
|---|---|
| 🌐 General | App name, logo, language, timezone, maintenance mode |
| 📧 Mail | SMTP config, IMAP config, sync interval, test connection |
| 💬 Chat | Attachments, read receipts, typing indicators, retention |
| 🔔 Notifications | Browser, email, sound alerts, event triggers |
| 🛡️ Security | Session timeout, 2FA, login attempts, IP whitelist |
| 🎨 Appearance | Theme mode, colors, fonts, Glassmorphism, date format |
| ⚡ Reverb | WebSocket host, port, connections limit, debug mode |

---

## 📋 Requirements

- Docker & Docker Compose
- Git
- (Optional) PHP 8.3 + Composer for local development without Docker

---

## 📄 License

This project is open-sourced under the [MIT License](LICENSE).

---

<p align="center">
  Built with ❤️ using <strong>Laravel 12</strong> + <strong>Filament v3</strong> + <strong>Laravel Reverb</strong>
</p>
