# Contributing to CorpChat

Thank you for your interest in contributing to CorpChat! Follow this guide to set up your environment and make changes.

## Development Setup

1. **Prerequisites**: Docker, Docker Compose, PHP 8.2+, Composer, Node.js 20+, and npm.
2. **Build and Run Containers**:
   ```bash
   docker compose up -d --build
   ```
3. **Initialize App**:
   ```bash
   composer install
   npm install
   npm run build
   php artisan migrate --seed
   ```
4. **WebSocket Server**:
   Ensure Laravel Reverb is running:
   ```bash
   docker exec corpchat_app php artisan reverb:start
   ```

## Development Guidelines

- **Architecture Rules**:
  - Keep styling inside `resources/css/chat.css`.
  - Keep JS logic inside `resources/js/chat.js` and build via Vite.
  - Return all API payloads formatted via `JsonResource` classes.
  - Write validation rules inside distinct `FormRequest` classes.
- **Code Style**:
  - Format PHP files using Laravel Pint:
    ```bash
    vendor/bin/pint
    ```
  - Format JS files using Prettier:
    ```bash
    npx prettier --write resources/js/chat.js
    ```
- **Testing**:
  - Write feature tests for any new API routes.
  - Run all tests before submitting pull requests:
    ```bash
    docker exec corpchat_app php artisan test
    ```
