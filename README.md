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

Run migrations (only default tables: users, sessions, cache, jobs, password_reset_tokens):

```bash
php artisan migrate
```

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

- **Auth**: Login, Register, Forgot Password, Profile (Laravel Breeze).
- **Dashboard**: `/dashboard` — welcome message and placeholder cards; uses `DashboardLayout` (Sidebar + Topbar).
- **Settings**: `/settings` — language switcher and profile shortcut.
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

| Command              | Description                |
|----------------------|----------------------------|
| `composer install`   | Install PHP dependencies    |
| `npm install`        | Install frontend deps       |
| `npm run dev`        | Vite dev server (HMR)       |
| `npm run build`      | Build for production        |
| `php artisan migrate`| Run database migrations     |
| `php artisan serve` | Start dev server            |

## Notes

- No business tables or models (e.g. containers, warehouses, inventory) are included; only the default Laravel/Breeze migrations.
- For production, point your web server document root to `public/` and set `APP_ENV=production`, `APP_DEBUG=false`, and run `npm run build` and `php artisan config:cache`.
