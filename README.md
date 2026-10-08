# The Kafe — Restaurant Website

Laravel + Tailwind CSS + MySQL website for **The Kafe** (Jakhalabandha): landing page, online menu ordering (WhatsApp to owner), party bookings, and an owner admin panel for menu management.

## Requirements

- PHP 8.2+
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
- `KAFE_OWNER_PHONE` — owner WhatsApp with country code, no `+` (e.g. `919876543210`). Change in `.env` anytime, then run `php artisan config:clear`.
- `KAFE_*` — branding text and Instagram URL

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

After seeding:

| Email | Password |
|-------|----------|
| `owner@thekafe.com` | `password` |

If login fails, reset the owner account:

```bash
php artisan kafe:reset-owner
# optional: php artisan kafe:reset-owner --password=YourNewPassword
```

Admin: `/admin` (menu, categories, bookings). Public registration is disabled.

## How orders & bookings work

1. Customer completes the form on **Order** or **Book a Party**.
2. The app saves the record in MySQL.
3. The browser redirects to **WhatsApp** (`wa.me`) with a pre-filled message to `KAFE_OWNER_PHONE`.
4. The customer taps **Send** — no WhatsApp Business API required.

To swap in the official WhatsApp Cloud API later, replace `App\Services\WhatsAppMessageBuilder` usage in the controllers.

## Customization

- **Theme & copy:** `resources/views/home.blade.php`, `config/kafe.php`
- **Party types & time slots:** `config/kafe.php`
- **Navbar links:** `resources/views/partials/navbar.blade.php`
- **Replace gallery/hero images** with photos from [@the_kafe_jakhalabandha](https://www.instagram.com/the_kafe_jakhalabandha/)

## XAMPP note

Point your virtual host document root to `theKafe/public`, or access via `http://localhost/theKafe/public` if the project lives under `htdocs`.
