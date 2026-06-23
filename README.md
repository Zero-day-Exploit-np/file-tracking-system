#  Setup File Tracking System on Your Local Machine

## Prerequisites

Install:

* PHP 8.2+
* Composer
* MySQL 8+
* Git
* Node.js (optional, if frontend assets need building)
* XAMPP / Laragon / WAMP

---

## 1. Fork the Repository

Click **Fork** on GitHub.

Then clone your fork:

```bash
git clone https://github.com/YOUR_USERNAME/file-tracking-system.git
```

Move into the project:

```bash
cd file-tracking-system
```

---

## 2. Install Dependencies

```bash
composer install
```

If `vendor/autoload.php` is missing, this step was not completed successfully.

---

## 3. Create Environment File

Windows:

```bash
copy .env.example .env
```

Linux/Mac:

```bash
cp .env.example .env
```

---

## 4. Generate Application Key

```bash
php artisan key:generate
```

---

## 5. Create Database

Create a new MySQL database:

```sql
CREATE DATABASE file_tracking_system;
```

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=file_tracking_system
DB_USERNAME=root
DB_PASSWORD=
```

---

## 6. Run Migrations

```bash
php artisan migrate
```

If seeders are available:

```bash
php artisan migrate --seed
```

---

## 7. Create Storage Link

```bash
php artisan storage:link
```

---

## 8. Clear Cache

```bash
php artisan optimize:clear
```

---

## 9. Start Development Server

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

---

## 10. Create Super Admin

If no Super Admin exists:

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Super Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
    'role' => 'super_admin',
    'is_active' => true,
]);
```

Exit:

```bash
exit
```

Login:

```text
Email: admin@example.com
Password: password123
```

---

## Common Fixes

### Missing Vendor Folder

```bash
composer install
```

### Storage Files Not Opening

```bash
php artisan storage:link
```

### Route Errors

```bash
php artisan optimize:clear
```

### Database Connection Error

Check `.env` credentials and restart the server.

### Too Many Redirects

Clear browser cookies and run:

```bash
php artisan optimize:clear
```

---

## Update From Upstream Repository

```bash
git remote add upstream https://github.com/ORIGINAL_OWNER/file-tracking-system.git

git fetch upstream

git merge upstream/main
```

This keeps your fork updated with the latest fixes and features.
