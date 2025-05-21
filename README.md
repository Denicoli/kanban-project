# 🧩 Laravel + Vue.js Kanban Project

This is a full-stack Kanban board application built with **Laravel 12 (API REST)** and **Vue.js 3** as a monolithic SPA. It supports full authentication, board management, draggable columns and tasks, and a scalable API-first architecture.

---

## 🚀 Features

- ✅ Laravel 12 backend with fully tested REST API
- ✅ Vue.js 3 SPA frontend (no Blade or Inertia)
- ✅ Tailwind CSS styling
- ✅ Breeze used only for initial Vue setup
- ✅ Authentication (Login/Register) via Laravel Sanctum
- ✅ Create boards dynamically
- ✅ Boards have default columns: `todo`, `in_progress`, `done`
- ✅ Create and drag tasks between columns
- ✅ Column and task reordering
- ✅ Ready to be consumed by other frontends (React, mobile, etc.)

---

## 🧱 Tech Stack

| Layer      | Technology           |
|------------|----------------------|
| Backend    | Laravel 12 (API)     |
| Frontend   | Vue.js 3 (SPA)       |
| Auth       | Laravel Sanctum      |
| Styling    | Tailwind CSS         |
| State Mgmt | Composition API      |
| DB         | MySQL/PostgreSQL     |
| API Test   | Laravel's test suite |

---

## ⚙️ Installation

```bash
# Clone the repo
git clone https://github.com/your-username/kanban-laravel-vue.git
cd kanban-laravel-vue

# Install backend dependencies
composer install

# Install frontend dependencies
npm install

# Copy .env and generate app key
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Run dev server
npm run dev

# Start Laravel server
php artisan serve
