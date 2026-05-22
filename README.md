# Streaming Subscription Platform (PHP + MySQL + Jellyfin)

## Setup
1. Copy `.env.example` to `.env` and fill secrets.
2. Import DB schema: `mysql -uUSER -p DB_NAME < sql/schema.sql`
3. Use your existing `public_html` as the web root (do not nest a separate `public/` folder).
4. Ensure PHP extensions: `pdo_mysql`, `curl`, `json`.
5. Run cron:
   - `0 1 * * * php /path/cron/check_expired.php`
   - `*/5 * * * * php /path/cron/sync_queue.php`

## Architecture
- Public pages at repository root (`/index.php`, `/login.php`, `/register.php`, `/dashboard.php`)
- Admin panel in `/admin` (role-protected)
- Business logic strictly in `/services`
- Jellyfin operations run asynchronously through `job_queue` + `/cron/sync_queue.php`

## Security
- Prepared statements used for DB interactions
- Passwords hashed with `password_hash()`
- CSRF token on POST forms
- Session auth + role checks
- Login/signup rate limiting (session based)
