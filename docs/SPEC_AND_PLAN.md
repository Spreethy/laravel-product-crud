# Inventory Management System — Spec & Implementation Plan

**Status:** Draft — pending user confirmation
**Date:** 2026-08-03
**Baseline commit:** `02a3c94` (Add AI chatbot for product management with Gemini API)

---

## 1. Overview & Vision

The project evolves from a basic product-management app into a **full inventory/stock
management system**. It must support:

- Product catalog managed by categories and suppliers
- Stock tracking via an auditable movement ledger (stock in / out / adjustment)
- Low-stock alerts with a resolution workflow
- Reporting (inventory valuation, stock levels, movement log, supplier summaries)
- Two user roles: **Admin** and **Staff**, with clear permission boundaries
- An expanded **AI assistant** that can manage the whole catalog, stock, and reports
  through natural-language chat

---

## 2. Current Baseline

| Area | Current state |
| --- | --- |
| Framework | Laravel 12.64, PHP 8.2, SQLite, Vite + Tailwind 3 |
| Auth | Breeze (login, register, profile) — email verification is out of scope and will be removed |
| Roles | None — every authenticated user has full access |
| Products | CRUD — `name`, `description`, `price (10,2)`, `stock` |
| Dashboard | Total products, total stock, recent 5 products |
| AI chat | Gemini `generateContent`; JSON action protocol for products (list/search/show/create/update/delete); session-based history |
| Tests | PHPUnit (Feature/Unit), RefreshDatabase |
| Code style | Laravel Pint, Conventional Commits (`feat:`, `docs:`, etc.) |

> **Scope note:** email verification is **not required**. The Breeze verification
> routes, `verified` middleware, and verification views will be removed so
> registration logs users straight into the app.

---

## 3. Tech Stack

- Laravel 12 (PHP 8.2), SQLite (as configured), Blade + Tailwind CSS, Alpine.js
- Vite for assets
- Laravel Pint for code style, PHPUnit for tests
- Google Gemini API for the AI assistant (`GEMINI_API_KEY`)

No new dependencies are planned unless required; new packages must be proposed in
this doc first.

---

## 4. Roles & Permissions

A single `role` column is added to `users` with an enum-backed `Role` class:

| Capability | Admin | Staff |
| --- | :---: | :---: |
| View products, categories, suppliers | ✅ | ✅ |
| Create / update products | ✅ | ✅ |
| Delete products | ✅ | ❌ |
| Manage categories (CRUD) | ✅ | ❌ (view only) |
| Manage suppliers (CRUD) | ✅ | ❌ (view only) |
| Record stock movements (in / out / adjust) | ✅ | ✅ |
| Delete / reverse stock movements | ✅ | ❌ |
| View & resolve low-stock alerts | ✅ | ✅ |
| View reports | ✅ | ✅ |
| Export reports (CSV) | ✅ | ❌ |
| User management (create/edit staff accounts) | ✅ | ❌ |

Enforcement: a reusable `admin` middleware + explicit checks in controllers/views.
Never trust the UI — always enforce server-side.

---

## 5. Data Model

```
users
  id, name, email, password, role (enum: admin|staff), timestamps

categories
  id, name (unique), slug (unique), description (nullable), timestamps, softDeletes

suppliers
  id, name, contact_name (nullable), email (nullable), phone (nullable),
  address (nullable), notes (nullable), timestamps, softDeletes

products                     (extended)
  id, category_id (FK nullable, nullOnDelete), supplier_id (FK nullable, nullOnDelete),
  sku (nullable, unique), name, description (nullable),
  price (decimal 10,2), stock (integer, default 0),        -- denormalized, ledger-driven
  reorder_level (integer, default 0), is_active (bool, default true),
  timestamps, softDeletes

stock_movements              (the audit ledger)
  id, product_id (FK, restrict), type (enum: in|out|adjustment),
  quantity (unsignedInteger), previous_stock, new_stock,
  reason (nullable), user_id (FK nullable, nullOnDelete), timestamps

stock_alerts
  id, product_id (FK, cascade), type (enum: low_stock),
  status (enum: open|resolved, default open),
  resolved_at (nullable), resolved_by (FK users nullable), timestamps
```

**Stock consistency rule:** `products.stock` is never written directly in normal
flows. It is updated only inside a DB transaction together with an inserted
`stock_movements` row (out/adjustment must never drive stock below 0). Movement
creation is centralized in the `StockMovement` model so the rule can't be bypassed.

**Alert rule:** when a product's stock drops to or below `reorder_level`, an `open`
`low_stock` alert is created (one open alert per product). When stock rises back
above `reorder_level`, the open alert is auto-resolved. Manual resolution is also
supported.

---

## 6. Feature Specifications

### 6.1 Products (enhanced)
- Fields: `name`, `sku`, `description`, `price`, `stock`, `reorder_level`,
  `category_id`, `supplier_id`, `is_active`.
- Product forms show category/supplier dropdowns; validation: `name` required,
  `price >= 0`, `stock >= 0`, `sku` unique when provided, `reorder_level >= 0`.
- Product page shows stock history (recent movements) and current alert status.
- Soft-deletable; deleting a product keeps its movement history (FK restrict on
  movements prevents hard delete while history exists).

### 6.2 Categories
- CRUD (admin only; staff view-only). Fields: `name`, `description`, `slug`.
- Deleting a category nulls the link on its products (no product deletion).
- Category index shows product count per category.

### 6.3 Suppliers
- CRUD (admin only; staff view-only). Fields: `name`, `contact_name`, `email`,
  `phone`, `address`, `notes`.
- Supplier index shows number of products and total stock value supplied.

### 6.4 Stock movements
- Record stock **in**, **out**, and **adjustment** from a product's page or a
  dedicated form. Each movement records `quantity`, `previous_stock`,
  `new_stock`, `reason`, and the acting user.
- Movement ledger: list view filterable by date range, product, and type, with
  before/after stock shown.
- Deletion of movements is admin-only and adjusts stock back accordingly.

### 6.5 Low-stock alerts
- Alerts appear when `stock <= reorder_level`.
- UI: badge in navigation (open alert count) + dashboard widget + dedicated list.
- Alerts can be marked resolved (with auto-resolution on restock).

### 6.6 Dashboard (enhanced)
- KPIs: total products, inventory value (`sum(price * stock)`), low-stock count,
  total suppliers, total categories.
- Widgets: low-stock products, recent stock movements, recent products.

### 6.7 Reports (admin view; staff view, no export)
- **Inventory valuation** — per product/category: qty, unit price, value; totals.
- **Stock levels** — all products with current stock vs reorder level.
- **Movement log** — filterable ledger with aggregates per type.
- **Supplier & category summaries** — product counts and stock value.
- CSV export (admin only) using streamed responses, no new packages.

### 6.8 User management (admin)
- Admin lists users, creates staff accounts (name/email/password), edits role,
  and can promote/demote. No self-demotion below admin.
- Prevent deleting the last admin / own account.

### 6.9 AI assistant (expanded)
- Refactor the current `ChatController` action handling into modular handler
  classes (one per domain) dispatched by a registry.
- Existing product actions are preserved: `list`, `search`, `show`, `create`,
  `update`, `delete`.
- New action groups:
  - **Categories:** `category_list`, `category_create`, `category_update`,
    `category_delete`
  - **Suppliers:** `supplier_list`, `supplier_create`, `supplier_update`,
    `supplier_delete`
  - **Stock:** `stock_in`, `stock_out`, `stock_adjust`, `movement_history`
  - **Reports:** `low_stock`, `report_valuation`, `report_summary`
- All actions run under the permission rules of the logged-in user; the AI must
  return a clear "not authorized" message when staff attempts an admin-only action.
- The system prompt documents the full action protocol; the model replies with a
  single JSON action object (or natural text for greetings/questions).

---

## 7. Non-Functional Requirements

- **Validation:** Form Request classes for every write path; server-side only.
- **Security:** Authorization enforced server-side (middleware + controller checks);
  no secrets committed; `.env` never tracked.
- **Integrity:** Stock mutations wrapped in DB transactions; stock never below 0.
- **UX:** Consistent Breeze layout; clear empty states; flash success/error
  messages; responsive Tailwind styling.
- **Code quality:** Laravel Pint formatting (`vendor/bin/pint`); PSR-4; no
  comments unless asked; no dead code.
- **Tests:** PHPUnit feature tests per feature area with `RefreshDatabase` and
  model factories. Every phase ships passing tests.
- **Docs:** keep this doc updated; update README setup/features at the end.

---

## 8. Implementation Plan (phases with detailed commits)

> One logical change per commit, Conventional Commits. Phases are implemented in
> order; each phase ends green (tests passing, Pint clean) before the next starts.

### Phase 0 — Baseline & spec
- `docs: add inventory management spec and implementation plan` (this document)

### Phase 1 — Roles & permissions
0. `refactor: remove email verification (routes, middleware, views)`
1. `feat: add role column to users table`
2. `feat: add Role enum and update User model`
3. `feat: add admin middleware and role helpers`
4. `feat: add admin user management (index/create/edit)`
5. `feat: seed admin user`
6. `test: cover role-based authorization`

### Phase 2 — Categories & Suppliers
1. `feat: create categories and suppliers tables`
2. `feat: add Category and Supplier models with factories`
3. `feat: add CategoryController, views, and routes (CRUD)`
4. `feat: add SupplierController, views, and routes (CRUD)`
5. `feat: extend products with category_id, supplier_id, sku, reorder_level, is_active`
6. `feat: update product forms and views for category/supplier links`
7. `test: cover categories and suppliers CRUD`

### Phase 3 — Stock movements & inventory tracking
1. `feat: create stock_movements table`
2. `feat: add StockMovement model with transactional stock sync`
3. `feat: add StockMovementController (in/out/adjust)`
4. `feat: add movement ledger view with filters`
5. `feat: show stock history on product page`
6. `test: cover stock movements and stock consistency`

### Phase 4 — Low-stock alerts
1. `feat: create stock_alerts table`
2. `feat: add alert detection on stock changes`
3. `feat: add navigation alert badge and dashboard widget`
4. `feat: add alert list and resolve workflow`
5. `test: cover alert lifecycle`

### Phase 5 — Enhanced dashboard & reports
1. `feat: redesign dashboard KPIs and widgets`
2. `feat: add inventory valuation report`
3. `feat: add stock levels report`
4. `feat: add movement log report`
5. `feat: add supplier and category summary reports`
6. `feat: add CSV export for reports (admin)`
7. `test: cover report calculations and export`

### Phase 6 — AI assistant expansion
1. `refactor: extract modular AI action handlers`
2. `feat: add AI actions for categories and suppliers`
3. `feat: add AI actions for stock movements`
4. `feat: add AI actions for reports and low stock`
5. `test: cover AI action dispatch`

### Phase 7 — Seeding, polish & final QA
1. `feat: seed demo data (categories, suppliers, products, movements)`
2. `style: apply Pint formatting`
3. `fix: resolve QA findings`
4. `docs: update README with setup and features`
5. `test: full suite passes`
6. `chore: final verification and commit`

---

## 9. Commit Workflow

- Conventional Commits: `feat:`, `fix:`, `refactor:`, `test:`, `docs:`, `style:`,
  `chore:`.
- One commit per logical change; commits within a phase land one by one (no
  squashing) so history stays reviewable.
- Commit only intended files; never commit `.env` or secrets.
- Before committing: `php artisan test` and `vendor/bin/pint --test` must pass.

---

## 10. Definition of Done (per phase)

- [ ] All listed commits for the phase created with clear messages
- [ ] `php artisan test` passes (existing + new tests for the phase)
- [ ] `vendor/bin/pint --test` passes
- [ ] Manual smoke test of the relevant UI flows at `http://127.0.0.1:8000`
- [ ] Spec updated if behavior changed during implementation
