# ContiStock (كونتي ستوك)

Laravel + Inertia.js + Vue 3 foundation with Auth, Dashboard Layout, and i18n (Arabic/English with RTL/LTR).

## Requirements

- PHP 8.2+
- Composer
- Node.js 20.19+ or 22.12+ (for Vite; Node 21 may work with warnings)
- MySQL 5.7+ or 8.x
- npm or pnpm

## Installation

### 1. Clone or copy the project

If the project was created in a different path (e.g. `C:\Users\Ebrahim\ContiStock`), copy it to your web root:

```bash
# Example: copy to XAMPP htdocs
xcopy /E /I C:\Users\Ebrahim\ContiStock C:\xampp\htdocs\ContiStock
```

### 2. Install PHP dependencies

```bash
cd ContiStock
composer install
```

### 3. Environment and database

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=contistock
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database (e.g. in MySQL or phpMyAdmin):

```sql
CREATE DATABASE contistock CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations (creates tenants, users with tenant_id, suppliers, containers, warehouse receipts, products, sales, customers, payments, stock_movements, settings):

```bash
php artisan migrate
```

Seed the database with a demo tenant and admin user:

```bash
php artisan db:seed
```

This creates:
- **Tenants**: Demo Tenant (slug: `demo`), Test Tenant (slug: `test`)
- **Admin user**: `admin@contistock.demo` / `password` (belongs to Demo Tenant)
- **Sample data**: one supplier, one customer, one product, one container for the demo tenant

**First login**: Use `admin@contistock.demo` and password `password` to access the dashboard.

### 4. Frontend

```bash
npm install
npm run build
```

For development with hot reload:

```bash
npm run dev
```

### 5. Run the application

```bash
php artisan serve
```

Open `http://localhost:8000` (or your XAMPP virtual host, e.g. `http://localhost/ContiStock/public` if using XAMPP).

## Project structure

- **Multi-tenant**: Shared database with `tenant_id` on all data tables. Users belong to a tenant; middleware `SetCurrentTenant` sets the current tenant from the authenticated user. All queries are scoped by tenant via `BelongsToTenant` trait.
- **Auth**: Login, Forgot Password, Profile (Laravel Breeze). Users have `tenant_id` and `role` (admin/user).
- **Dashboard**: `/dashboard` — stats (suppliers balance, customers balance, inventory value, total profit) and quick links to reports.
- **Suppliers**: CRUD; list containers and payments per supplier.
- **Containers**: CRUD; link to supplier, total cost, status (draft / purchased / received_to_warehouse / closed). Supplier payments and warehouse receipts per container.
- **Warehouse receipts**: Create a receipt for a container and add receipt items (product, qty, buy price, sale price). Creates stock movements (in).
- **Products**: CRUD; name, SKU (unique per tenant).
- **Stock**: List products with available quantity; filter by container.
- **Customers**: CRUD; list sales and balance per customer.
- **Sales**: Create sale (customer + items with product, container, qty, unit price). Confirm sale to deduct stock (creates stock_movements out). Customer payments per sale.
- **Reports**: Container summary, customer balance, supplier balance, profit report.
- **Layouts**: `DashboardLayout.vue` (sidebar + topbar, brand colors), `GuestLayout.vue` (auth pages).
- **Components**: `Button`, `Input`, `Card`, `Badge`, `Modal` (plus Breeze components).
- **i18n**: `resources/js/i18n/` — `ar.json`, `en.json`; locale stored in cookie and synced with Laravel.

## Brand colors

- **Primary Navy**: `#0F2A44`
- **Accent Orange**: `#F28C28`
- **Light background**: `#F4F6F8`

Used in Tailwind as `primary-navy`, `accent-orange`, `background-light`.

## Language and RTL/LTR

- **Languages**: Arabic (عربي) and English (EN).
- **Switch**: Use the language switcher in the top bar (dashboard/settings) or the switcher on the Settings page.
- **RTL**: When Arabic is selected, the interface is RTL; when English, LTR. The `dir` and `lang` attributes on `<html>` are set from the server and updated by the frontend when the user changes language.
- **Persistence**: The chosen language is stored in the `contistock_locale` cookie and in the frontend (localStorage) so it persists across requests.
- **Fonts**: Inter for English, Cairo for Arabic (loaded from Google Fonts in `app.blade.php`).

## Logo

- Place your logo at **`public/images/logo.png`**.
- If the file exists, it is shown in the Sidebar and on the Login page; otherwise the default application logo (SVG) is used.

## Toast notifications

The app uses **vue3-toastify**. To show a toast from any component:

```js
import { toast } from 'vue3-toastify';

toast('Message');
toast.success('Success!');
toast.error('Error!');
```

## Commands summary

| Command                | Description                     |
|------------------------|---------------------------------|
| `composer install`     | Install PHP dependencies        |
| `npm install`         | Install frontend deps           |
| `npm run dev`         | Vite dev server (HMR)           |
| `npm run build`       | Build for production            |
| `php artisan migrate` | Run database migrations         |
| `php artisan db:seed` | Seed tenants + admin + sample   |
| `php artisan serve`   | Start dev server                |
| `php artisan test`    | Run feature and unit tests      |

## Notes

- **Tenant isolation**: Every user must have a `tenant_id`. The `tenant` middleware runs on all app routes (dashboard, suppliers, containers, etc.) and aborts with 403 if the user has no tenant.
- **Calculations**: Container paid/remaining and Sale paid/remaining are computed from payment records (not stored). Stock is computed from `stock_movements` (in/out/adjust).
- **Allow negative stock**: Optional tenant setting `allow_negative_stock` (in `settings` table) allows selling more than available stock; otherwise confirm sale will fail if stock is insufficient.
- For production, point your web server document root to `public/` and set `APP_ENV=production`, `APP_DEBUG=false`, and run `npm run build` and `php artisan config:cache`.
