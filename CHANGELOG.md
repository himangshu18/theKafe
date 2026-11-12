# The Kafé - Development Tracker & Changelog

This is the project’s working record for implementation progress, decisions, and completed changes. Update the roadmap when phase status changes and append changelog entries only for work that was actually completed. Do not mark planned features complete or record secrets or customer data here.

Blueprint reference: `The_Kafe_V1_Development_Blueprint.pdf` (Version 1.0, supplied by the project owner).

## Project baseline

**Last reviewed:** 2026-11-11  
**Application:** Laravel 13 (Composer constraint `^13.17`), PHP `^8.3`, Blade, Alpine.js, Tailwind CSS and Vite. The README documents MySQL. Keep this stack unless a concrete deployment requirement calls for a change.

### Current implementation status

- Customer menu, pickup cart, server-priced order submission, customer mobile number, order confirmation and repeat-submission protection are implemented.
- Admin category/item/image management, food type, variations, add-ons, availability schedules, combos, offers and Today’s Specials are implemented.
- Role-restricted booking/order workflows, order history, database-backed admin notifications, unread polling and click-enabled sound alerts are implemented.
- Offline order payments, expenses, cash sessions, reports and CSV export are implemented. Cash transactions require an open cash session.
- WhatsApp and online payments are intentionally not integrated. No production deployment or production migration is part of this phase.
- Targeted ordering/admin/accounting tests pass. Full-suite status and remaining owner approvals are recorded below; do not treat this application as production-ready.

## Development roadmap

Status values distinguish implementation from business approval and validation. **Implemented** does not imply production readiness. Update this table as work proceeds.

| Order | Phase | Status | Exit criteria |
|---:|---|---|---|
| 0 | Repository and blueprint assessment | Complete | Existing app and blueprint compared; implementation has not started from this plan. |
| 1 | Business decisions and content approval | In progress | Owner verifies food labels, menu/prices, combo mappings, operating hours, fees/taxes and staff roles. |
| 2 | Foundation, access control and test approach | Implemented; review pending | Role middleware, per-role access, safe owner-password setup, timezone and feature-test foundations are in place. |
| 3 | Menu catalogue and administration | Implemented; content approval pending | Categories/items, food types, images, variations, add-ons and schedules are manageable in admin. |
| 4 | Order data model and business rules | Implemented; targeted tests pass | Orders have unique public references, server-authoritative cents pricing, snapshots, idempotency, status history and separate payment state. |
| 5 | Combos, offers and Today’s Specials | Implemented; owner rules pending | Admin-configurable choices and schedules feed customer pricing; combo choice quantities are recorded in snapshots. |
| 6 | Customer menu, cart and checkout | Implemented; acceptance testing pending | Guest can build and submit pickup orders with a mobile number and receive an order reference without online payment. |
| 7 | Admin order desk and internal notifications | Implemented; acceptance testing pending | Role-checked staff can process orders/bookings; notification history, unread polling, sound and read state are stored. |
| 8 | WhatsApp Business | Deferred by instruction | No Meta account, API, credentials, templates, webhooks or fake WhatsApp workflow are configured. |
| 9 | Counter orders, specialized kitchen queue and receipts | Deferred / outside current scope | The current admin order queue supports kitchen-role processing; POS/counter-specific flows and receipts remain separate work. |
| 10 | Offline payments, expenses and reports | Implemented; reconciliation review pending | Offline payments, expense records, cash sessions, totals and CSV export are connected to the database. |
| 11 | QA, staging acceptance and handover | In progress | Targeted tests, route registration and frontend build pass; review known baseline failures and complete owner-led acceptance before release. Production deployment remains out of scope. |

### Decisions and owner inputs

| ID | Topic | Current record | Status |
|---|---|---|---|
| D-001 | Online payments | Blueprint excludes online payment processing for Version 1.0; staff may record offline payments. | Confirmed by blueprint |
| D-002 | Food policy | Customer menu and seed data include vegetarian, egg and chicken items; verify all marketing and dietary labels against the café’s actual policy. | Owner approval required |
| D-003 | Launch order types | Current order flow is pickup only; enable dine-in or delivery only after process, fields and charges are confirmed. | Pickup for this phase |
| D-004 | Menu and combo data | Verify every item, spelling, price and combo selection. Some PDF combo names are mapped to the closest available standalone item and require owner confirmation. | Owner approval required |
| D-005 | Customer/staff messaging | WhatsApp Business integration is explicitly deferred. Current admin alerts are internal and do not send customer messages. | Confirmed by user |
| D-006 | Database and frontend stack | Existing project documents MySQL and uses Blade/Alpine/Tailwind. Prefer continuing with these rather than switching databases/frameworks without a deployment reason. | Recommended; confirm with deployment plan |
| D-007 | Owner account | Seed/reset requires an environment-configured or interactively entered unique password; no default password is documented or emitted by the reset command. | Implemented |

Do not invent business rules or treat unapproved menu mappings as final. Record new decisions here with an ID, decision, owner and date.

## Development history

### 2026-11-11 - Blueprint review and tracker setup

- Compared the Version 1.0 blueprint with the current Laravel application and recorded the phased implementation order above.
- Identified the vegetarian/non-vegetarian menu conflict and other owner decisions that must be resolved before relevant work.
- Added phase tracking, decision records and a changelog entry template.
- No application behavior or database schema was changed.

### 2026-11-11 - Ordering, admin operations and accounting foundation

- **Phase:** 2–7 and 10
- **Type:** Feature / Fix
- **Summary:** Added database-backed catalog customizations, server-priced pickup orders, combos/offers/specials, admin order and booking workflows, per-role internal notifications, offline accounting and staff access. Removed the WhatsApp-dependent customer redirect/UI; no WhatsApp API integration was added. Owner seeding now requires a configured password, and the reset command no longer prints or defaults to a password. Food type is editable and shown on customer menu cards.
- **Files/routes/migrations:** `app/Services/`, `app/Http/Controllers/Admin/`, `app/Http/Requests/Admin/`, `database/migrations/`, `database/seeders/KafeSeeder.php`, `routes/web.php`, customer/admin Blade views, `.env.example`, `README.md`.
- **Verification:** Targeted feature tests: 22 passed (110 assertions). `vendor/bin/pint --dirty --format agent`, `npm run build`, and `php artisan route:list --except-vendor` passed. Full suite: 44 passed, 3 known baseline failures remain in disabled-registration and example-home tests.
- **Decisions or risks:** Owner approval is still needed for the menu PDF’s combo-name mappings, menu/prices, dietary marketing, schedules and accounting/tax policy. No production deployment or security audit was performed.
- **Next:** Complete owner content decisions, resolve baseline test failures if they are still expected to apply, then run broader staging acceptance tests.

### 2026-11-12 - Development database migration recovery

- **Phase:** 2–5 and 10
- **Type:** Fix / Infrastructure
- **Summary:** The reported home-page error came from running application code before the pending schema migrations. The first migration attempt also exposed a MySQL 64-character index-name limit. The catalog migration now uses a short named index and safely resumes after partially applied DDL; all pending development migrations were applied without resetting or dropping existing data.
- **Files/routes/migrations:** `database/migrations/2026_11_11_173729_extend_menu_catalog_for_customization.php`.
- **Verification:** `php artisan migrate --no-interaction` completed; all migrations show as ran; `http://127.0.0.1:8000/` returned HTTP 200; full suite remains 44 passed and the same 3 baseline failures.
- **Decisions or risks:** Migration ran against the configured development database only. No production deployment or production migration was performed.
- **Next:** Continue with owner content approval and staging acceptance.

## Changelog entry template

Append a dated entry after completing a coherent change. Keep existing historical notes below.

```markdown
### YYYY-MM-DD - Short change title

- **Phase:** Phase number and name
- **Type:** Feature / Fix / Docs / Infrastructure
- **Summary:** What changed and why
- **Files/routes/migrations:** Relevant paths or migration names
- **Verification:** Tests/build/commands run and result
- **Decisions or risks:** Owner decisions, limitations, or `None`
- **Next:** Next dependency or task
```

---

## Existing project change notes

## Earlier project history (before this feature phase)

## Setup & Infrastructure
- **Dependencies Installed**: Node (`npm install`) and PHP (`composer install`) packages installed.
- **Environment Prepared**: `.env` configured, ran `php artisan key:generate` and `php artisan migrate`.
- **Laravel Boost**: Installed and configured (agents: antigravity).
- **Storage Configuration**: Ran `php artisan storage:link` to properly serve uploaded images from `storage/app/public`.
- **Servers**: Background tasks for `php artisan serve` and `npm run dev` started.

## Admin Panel Features
- **Alpine.js Image Preview**: Added real-time image upload preview functionality for creating and editing menu items in `resources/views/admin/menu/index.blade.php`.
- **Thumbnail Display**: Updated the menu list table in the admin panel to beautifully display circular image thumbnails using `$item->imageUrl()`, falling back to a stylish placeholder icon when no image exists.

## Frontend UI Overhaul (Pixel Perfect Homepage Design)
- **Typography Integration**: Added `Dancing Script` from Google Fonts in `layouts/kafe.blade.php` and created a custom `.font-cursive` utility class in `app.css` for handwriting typography.
- **Navbar (`partials/navbar.blade.php`)**: 
  - Matched the exact layout, hover effects, and spacing.
  - Re-styled the "Book a Party" button as a solid pink pill with an icon.
- **Hero Section (`home.blade.php`)**: 
  - Replaced CSS gradients with the user-provided `images/hero-banner.png` as a full background layer.
  - Matched typography sizes, line-heights, and exact hex colors (e.g., `#3D1B28` for dark maroon, `#D6336C` for bright pink).
  - Recreated the 5 circular feature icons (Pure Veg, Freshly Prepared, etc.) with custom SVGs and white circle backgrounds.
  - Rebuilt the right overlay composition using `images/logo-pink.png` for the circular stamp, accompanied by a floating "Open daily" widget and tilted cursive text overlay.
- **About Section (`home.blade.php`)**: 
  - Restructured to include the overlapping tea and samosa images featuring thick white borders.
  - Added pink circular checkmarks on the feature list and slanted cursive text anchored to the bottom right of the images.
- **Guest Favourites (`home.blade.php`)**: 
  - Restyled menu cards with crisp white backgrounds and light pink borders.
  - Tweaked prices and "Add" button styles to perfectly match the mockup.
  - Added slanted cursive background text ("Simple Food Happy People") to the right side of the section.
- **Café Mood / Gallery (`home.blade.php`)**: 
  - Shifted section title and descriptive text to the left with a small pink dash accent, placing the "View gallery" link on the right.
  - Added a dark overlay with handwriting text ("Good Food Good Mood") to the final image in the grid.
- **Party Booking Banner (`home.blade.php`)**: 
  - Updated the gradient background to a rich pink-maroon blend.
  - Integrated an overlapping birthday cake image on the left.
  - Added a 4-item feature list with custom white outline icons on the right.
- **Footer (`partials/footer.blade.php`)**: 
  - Changed the background color to dark maroon (`#6b0e30`).
  - Restructured into a precise 4-column layout (Logo/Branding, Quick Links, Our Story, Visit Us).
  - Added a green floating WhatsApp button securely fixed to the bottom right corner of the window.
