# IRONX Fitness — PHP & MySQL

A modular gym management starter application with public pages, member/trainer/admin areas, membership records, workout and diet planning, attendance, booking requests, progress tracking and an AI assistant integration placeholder.

## Requirements
- PHP 8.1+ (8.2+ recommended)
- MySQL 8+
- Apache with mod_rewrite/AllowOverride if using `.htaccess`
- PHP PDO MySQL extension

## Installation on macOS
1. Place the `IRONX` folder in your web server document root, or run it from the project directory.
2. Create/import the database:
   ```bash
   mysql -u root -p < database/ironx_gym.sql
   ```
   The SQL creates the `ironx_gym` database.
3. Edit `config/database.php` or configure `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` in your PHP server environment.
4. Start PHP:
   ```bash
   php -S localhost:8000
   ```
5. Open `http://localhost:8000/index.php`.

## Create the first administrator
For safety, public registration only creates member accounts. Create the first admin from the MySQL console after registering an account:
```sql
UPDATE users SET role='admin' WHERE email='your-email@example.com';
```
Trainer accounts can be promoted similarly:
```sql
UPDATE users SET role='trainer' WHERE email='trainer@example.com';
INSERT INTO trainer_profiles(user_id,specialization,experience_years,bio)
SELECT id,'Strength & Conditioning',5,'Trainer profile' FROM users WHERE email='trainer@example.com';
```
Log out and log back in after changing a role.

## Important configuration notes
- `.env` is an example file; plain PHP does not automatically load it. Set environment variables in your web server, or add a vetted dotenv package.
- Payment endpoints intentionally return a not-configured response. Add Razorpay order creation, signature verification and webhook handling before taking real payments.
- `classes/AIService.php` is a safe integration placeholder. Add a provider SDK/API call server-side and keep the API key out of browser code.
- Password reset currently creates a hashed, one-time token. Connect PHPMailer or another mail service before production. In development the token is shown on-screen.
- QR attendance, automated email reminders, refunds, advanced charting and full AI-generated plans are extension points, not completed production integrations.
- Use HTTPS, backups, monitoring, access controls and security review before deploying with real member data.

## Security
The project uses PDO prepared statements for database operations, password hashing, session-based authentication, role checks, CSRF tokens on POST forms, escaped output and server-side credential guidance. A production deployment should additionally configure login throttling, audit logging, secure secret management, file upload validation, email verification and tested payment webhooks.


## Member dashboard upgrade

For the advanced member dashboard, import the additive migration once after your existing database is set up:

```bash
mysql -u root -p ironx_gym < database/ironx_member_upgrade.sql
```

This migration creates `member_activities` and `fitness_goals` without dropping or changing existing user, membership, attendance, payment, workout, or booking records. The member dashboard includes live account metrics, upcoming sessions, membership status, notifications, goals and recent activity. Members can add activity logs and create/update personal fitness goals.

## Administrator login and account setup

- Dedicated administrator sign-in: `/admin/login.php`
- Shared member sign-in remains: `/auth/login.php`
- Admin accounts must have `users.role = 'admin'` and `users.status = 'active'`.
- To create an admin or promote an existing account, run this from the project root in Terminal:

```bash
php tools/create-admin.php
```

The CLI utility prompts for the administrator's name, email and password, hashes the password with PHP `password_hash()`, and creates/promotes the account. It is not accessible over HTTP. Use a unique password of at least 10 characters.

The login controller checks the account's active status and, on `/admin/login.php`, rejects accounts that do not have the admin role. Protected admin pages refresh the role from the database so role changes take effect without relying on stale session data.


## Member Management workspace

After updating the project, run the additive migration once:

```bash
mysql -u root -p ironx_gym < database/ironx_member_management.sql
```

Members can open `member/management.php` or select **My Management** in their sidebar. The workspace provides shortcuts to existing personal tools and a private support-request form. It does not grant members administrator privileges or access to other members' records.

## IRONX Gemini AI Coach (public + members)

1. Create a Gemini API key in https://aistudio.google.com/apikey.
2. Put it in the project-root `.env` file as `GEMINI_API_KEY=your_key_here` (do not commit or share this file). On production hosting, prefer a server environment variable named `GEMINI_API_KEY`.
3. Ensure PHP cURL is enabled (`php -m | grep -i curl`).
4. Open `/ai-coach.php` for the public AI coach, or sign in and open `/member/ai-assistant.php`.
5. The backend uses Gemini `generateContent` with the `x-goog-api-key` header. The key is never sent to browser JavaScript. Visitors are not stored; authenticated conversations are saved to the existing `ai_conversations` and `ai_messages` tables.

The endpoint uses session CSRF validation, an 8-requests-per-minute session limit, and a 2,000-character message limit. Add IP-level rate limiting/WAF before exposing it to substantial public traffic. Gemini free-tier availability and quotas are controlled by Google and can change; do not assume unlimited free usage.
