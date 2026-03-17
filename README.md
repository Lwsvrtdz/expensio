# Expensio

**Manage expenses, simplify sharing.** Split group expenses and track who owes whom—for trips, roommates, or any shared spending.

## Features

- **Personal expenses** — Track your own spending
- **Groups** — Create groups (e.g. trips, households) with a name and optional trip date
- **Group expenses** — Add expenses to a group with a payer and split amounts across members
- **Balances** — See per-group and dashboard totals for what you’re owed and what you owe
- **Invites** — Invite members to groups via token link; they can accept without an account (email-only participants supported for splits)
- **Auth** — Registration, login, two-factor authentication, and password reset (Laravel Fortify)
- **Settings** — Profile, security, and appearance (e.g. dark mode)

## Tech stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Vue 3, TypeScript, Inertia.js v2, Tailwind CSS v4, Vite 7
- **Auth:** Laravel Fortify
- **Routes:** Laravel Wayfinder (type-safe route generation for the frontend)
- **Tests:** Pest 4

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- SQLite (default), or MySQL/PostgreSQL

## Installation

1. **Clone and install PHP dependencies**

   ```bash
   git clone https://github.com/your-username/expensio.git
   cd expensio
   composer install
   ```

2. **Environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Configure `.env` (e.g. `APP_NAME`, `APP_URL`, and database if not using SQLite).

3. **Database**

   ```bash
   php artisan migrate
   ```

4. **Frontend**

   ```bash
   npm install
   npm run build
   ```

   One-line setup (after cloning and copying `.env`):

   ```bash
   composer run setup
   ```

## Development

Run the app with the dev server, queue worker, and Vite:

```bash
composer run dev
```

Then open the app at the URL given (e.g. `http://expensio.test` with [Laravel Herd](https://herd.laravel.com/) or `http://localhost:8000` with `php artisan serve`).

## Testing

```bash
php artisan test --compact
```

Tests are written with [Pest](https://pestphp.com/).

## Project structure (high level)

- **Backend:** `app/Http/Controllers`, `app/Http/Services` (e.g. `ExpenseService`, `GroupBalanceService`, `ExpenseSplitService`), `app/Models` (User, Group, GroupMember, Expense, ExpenseSplit)
- **Frontend:** `resources/js/pages` (Inertia pages), `resources/js/components`
- **Routes:** `routes/web.php` (dashboard, groups, expenses, invites); `routes/settings.php` for user settings

## License

MIT.
