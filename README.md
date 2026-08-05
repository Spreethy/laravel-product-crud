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

## Roles & permissions

| Capability | Admin | Staff |
| --- | :---: | :---: |
| View products, categories, suppliers | ✅ | ✅ |
| Create / update products | ✅ | ✅ |
| Delete products | ✅ | ❌ |
| Manage categories & suppliers (CRUD) | ✅ | ❌ (view only) |
| Record stock movements (in / out / adjust) | ✅ | ✅ |
| Delete / reverse stock movements | ✅ | ❌ |
| View & resolve low-stock alerts | ✅ | ✅ |
| View reports | ✅ | ✅ |
| Export reports (CSV) | ✅ | ❌ |
| User management (create/edit staff accounts) | ✅ | ❌ |

## Data model

- **users** — id, name, email, password, role (admin|staff)
- **categories** — name, slug, description (soft deletes)
- **suppliers** — name, contact_name, email, phone, address, notes (soft deletes)
- **products** — sku, name, description, price, stock, reorder_level, is_active, category_id, supplier_id (soft deletes)
- **stock_movements** — product_id, type (in|out|adjustment), quantity, previous_stock, new_stock, reason, user_id
- **stock_alerts** — product_id, type (low_stock), status (open|resolved), resolved_at, resolved_by

**Stock consistency:** `products.stock` is never written directly — it is updated only in a DB transaction together with a `stock_movements` row (stock never drops below 0), centralized in the `StockMovement` model.

**Low-stock alerts:** when stock drops to or below `reorder_level`, an open alert is created (one per product); it auto-resolves when stock rises back above it. Manual resolution is also supported.

## Tests & code style

```bash
php artisan test
vendor/bin/pint
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
