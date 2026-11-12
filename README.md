# The Kafe — Restaurant Website

Laravel + Tailwind CSS + MySQL website for **The Kafe** (Jakhalabandha): customer-facing menu and pickup ordering, party bookings, and an admin workspace for menu and order operations.

## Requirements

- PHP 8.3+
- Composer
- Node.js & npm
- MySQL (XAMPP / Laragon)

## Setup

1. **Create the database** in phpMyAdmin or MySQL:

```sql
CREATE DATABASE the_kafe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. **Configure `.env`** (already templated):

- `DB_*` — MySQL credentials
- `KAFE_*` — branding text and Instagram URL
- `KAFE_OWNER_PASSWORD` — a unique password of at least 12 characters for the seeded owner account

3. **Install & migrate:**

```bash
composer install
npm install
npm run build
php artisan migrate --seed
php artisan storage:link
```

4. **Run the app:**

```bash
php artisan serve
```

Open `http://127.0.0.1:8000`

## Owner login

The seeded owner account is `owner@thekafe.com`. The password is read from `KAFE_OWNER_PASSWORD`; the project does not ship a default owner password. To reset it securely, run:

```bash
php artisan kafe:reset-owner
```

The command prompts for a password without printing it. A password can also be supplied with `--password`, but an interactive prompt is preferred to avoid exposing it in shell history.

Admin: `/admin` (menu, categories, customizations, combos, discounts, specials, bookings, orders, notifications, staff access, and accounting). Public registration is disabled.

## How orders & bookings work

1. Customers browse the menu, configure items, and submit pickup orders with a mobile number.
2. The application calculates prices from the database, stores item snapshots and status history, and displays an order reference.
3. Admins process orders from the order queue. Internal notifications are stored in the database and polled by signed-in admins for unread counts and sound alerts.
4. Party booking requests are saved and surfaced in the admin booking list with internal notifications.

Online payments and WhatsApp integration are not part of the current development phase. WhatsApp should be added later as a separate notification adapter without coupling it to order creation.

## Testing

Run the project test suite with:

```bash
php artisan test --compact
```

## Customization

- **Theme & copy:** `resources/views/home.blade.php`, `config/kafe.php`
- **Party types & time slots:** `config/kafe.php`
- **Navbar links:** `resources/views/partials/navbar.blade.php`
- **Replace gallery/hero images** with photos from [@the_kafe_jakhalabandha](https://www.instagram.com/the_kafe_jakhalabandha/)

## XAMPP note

Point your virtual host document root to `theKafe/public`, or access via `http://localhost/theKafe/public` if the project lives under `htdocs`.
