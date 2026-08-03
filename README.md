# Inventory Management System

A Laravel 12 inventory/stock management system with role-based access control, a stock movement ledger, automated low-stock alerts, reporting with CSV export, and an AI assistant that can operate on your data through natural language.

## Features

- **Roles & permissions** — `admin` and `staff` roles; admins manage everything, staff get read-only access (plus stock operations). Admin-only routes are enforced via middleware and `abort_unless`.
- **Products, categories & suppliers** — full CRUD with SKUs, reorder levels, soft deletes, and category/supplier links.
- **Stock movement ledger** — every stock in/out/adjustment is recorded transactionally with previous and new stock levels; movements can be reverted.
- **Low-stock alerts** — automatically opened/resolved as stock crosses the reorder level, surfaced in the nav badge and dashboard.
- **Dashboard & reports** — KPI cards (inventory value, low-stock count, suppliers, categories), and reports for valuation, stock levels, movements, suppliers, and categories, all exportable as CSV.
- **AI assistant** — a chat interface backed by the Gemini API that can list, search, create, update, and (as admin) delete products, manage categories/suppliers, move stock, and run reports.

## Requirements

- PHP 8.2+
- Composer
- SQLite (default) or MySQL/PostgreSQL
- A [Gemini API key](https://aistudio.google.com/apikey) for the AI assistant

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Add your Gemini API key to `.env`:

```
GEMINI_API_KEY=your_key_here
```

Prepare the database and seed demo data:

```bash
php artisan migrate --seed
```

Seed users:

- **Admin** — `admin@example.com` / `password` (from the default UserFactory)
- **Staff** — `staff@example.com` / `password`

Start the app:

```bash
php artisan serve
npm run dev
```

## Tests & code style

```bash
php artisan test
vendor/bin/pint
```

## Documentation

The full specification, permission matrix, data model, and per-phase implementation plan live in [docs/SPEC_AND_PLAN.md](docs/SPEC_AND_PLAN.md).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
