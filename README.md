# Inventory Management System

A Laravel 12 app I built for tracking inventory and stock. It has user roles, a running history of every stock change, low-stock warnings, some basic reports you can export to CSV, and a little AI chat helper that can read and edit your data if you ask it to.

## What it does

- Two roles: **admin** and **staff**. Admins can do everything; staff can mostly just look at things and move stock around.
- Manage **products, categories and suppliers** — normal add/edit/delete pages.
- Every stock **in / out / adjustment** is saved to a ledger so you can see what changed and when, and undo mistakes (admin only).
- Automatically flags **low stock** and clears the flag once you restock.
- Dashboard with a few key numbers and a handful of **reports** (valuation, stock levels, movements, suppliers). CSV export is admin-only.
- An **AI assistant** chat box that can handle product, category, supplier, stock and report actions for you.

## What you need

- PHP 8.2+
- Composer
- SQLite (default) or MySQL/PostgreSQL
- A [Gemini API key](https://aistudio.google.com/apikey) if you want the AI assistant to work

## Setting it up

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Put your Gemini key in `.env`:

```
GEMINI_API_KEY=your_key_here
```

Then migrate and load some demo data:

```bash
php artisan migrate --seed
```

The seeder creates two accounts:

- **Admin** — `admin@example.com` / `password`
- **Staff** — `staff@example.com` / `password`

Run it:

```bash
php artisan serve
npm run dev
```

## Who can do what

Everyone with an account can browse products, categories and suppliers, create or edit products, move stock in and out, check and clear low-stock alerts, and view the reports.

The things only an **admin** can do:

- delete products
- add, edit or remove categories and suppliers (staff can only look at them)
- undo a stock movement
- export reports as CSV
- create and manage user accounts

## How the data fits together

The main tables:

- `users` — login info plus a role flag (admin or staff)
- `categories` and `suppliers` — the usual name and contact fields
- `products` — sku, name, description, price, stock count, reorder level, and links to a category and supplier
- `stock_movements` — one row per stock change, with the type (in/out/adjustment), the quantity, the stock before and after, a reason, and who did it
- `stock_alerts` — which product is low, the alert type, and whether it's still open or already resolved

**Stock consistency:** product stock is never edited by hand. Every change goes through a transaction that updates the stock and records a movement at the same time, so stock can't be driven below zero.

**Low-stock alerts:** when stock drops to or below the reorder level an alert is opened (one per product). It closes on its own once you restock above the level, or you can close it manually.

## Tests

```bash
php artisan test
vendor/bin/pint
```

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).