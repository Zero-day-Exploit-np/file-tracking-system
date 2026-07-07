# FileTrack Office Portal

A Laravel 12 government file tracking system with role-based access, instant file transfers, hierarchical impersonation, and a linked-list timeline.

---

## Features

| Feature | Description |
|---|---|
| **File Management** | Create, view, edit, download files with attachments |
| **Government File Number** | Manual entry with uniqueness validation |
| **File Transfer** | Instant same-dept or cross-dept transfer — no approval workflow |
| **Linked-List Timeline** | Visual file journey with profile pictures, remarks, current-holder badge |
| **Impersonation** | Super Admin → Admin or User; Admin → own dept Users |
| **Default Password** | `Password@123` on creation; forced change on first login |
| **Public File Search** | Search by file number — shows current holder, no auth required |
| **Notifications** | Bell badge, dropdown auto-read on open, sound on new arrival |
| **Role-Based Access** | `super_admin`, `admin`, `user` with ownership-based transfer |

---

## Roles

| Role | Can Create Files | Can Transfer | Can Impersonate |
|---|---|---|---|
| Super Admin | ✗ | ✗ (unless holder) | Admins + Users |
| Admin | ✗ | ✓ (if holder) | Own dept Users |
| User | ✓ (if permitted) | ✓ (if holder) | — |

---

## Prerequisites

- PHP 8.2+
- Composer
- MySQL 8+
- Git
- XAMPP / Laragon / WAMP (or any local PHP stack)

---

## Local Setup

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/file-tracking-system.git
cd file-tracking-system
```

### 2. Install dependencies

```bash
composer install
```

### 3. Create environment file

**Windows:**
```bash
copy .env.example .env
```
**Linux / Mac:**
```bash
cp .env.example .env
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Create database

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

### 6. Run migrations

```bash
php artisan migrate
```

With seeders (if available):

```bash
php artisan migrate --seed
```

### 7. Create storage link

```bash
php artisan storage:link
```

### 8. Clear caches

```bash
php artisan optimize:clear
```

### 9. Start the development server

```bash
php artisan serve
```

Open: `http://127.0.0.1:8000`

---

## Create Super Admin

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name'     => 'Super Admin',
    'email'    => 'superadmin@example.com',
    'password' => Hash::make('Password@123'),
    'role'     => 'super_admin',
    'is_active' => true,
    'must_change_password' => false,
]);
exit
```

---

## Default Password

When Super Admin creates an **Admin** or **User** account, the default password is:

```
Password@123
```

The user is forced to change this password on first login. The `must_change_password` flag is automatically cleared after a successful password change.

---

## File Numbers

File numbers must be entered **manually** by the creator. They must be unique across all records.

Valid formats:

```
HR/FIN/2026/234
FIN-12/456
ABC-2026-001
```

Allowed characters: letters, numbers, hyphens (`-`), slashes (`/`), dots (`.`), spaces.

---

## File Transfer Rules

- **Same Department:** Select any active user in your department.
- **Cross Department:** Search for the target department by name (AJAX autocomplete). File is assigned to the department's Admin on arrival.
- Whoever holds the file (`current_user_id`) can transfer it — no role restriction.
- If the target department has no active users, the transfer is blocked.

---

## Impersonation

| Impersonator | Can Impersonate |
|---|---|
| Super Admin | Any Admin or User (not another Super Admin) |
| Admin | Users in their own department only |
| User | Nobody |

An amber banner appears at the top of every page during impersonation with a **Stop Impersonating** button.

Notifications during impersonation belong to the impersonated user. Stopping restores the original session.

---

## Timeline

Every file has a linked-list style vertical timeline showing:

- User or Department name
- Profile picture (or initials fallback)
- Department
- Date and Time
- Remarks entered during transfer
- **Current Holder** badge (green highlight)

The same `<x-file-timeline>` Blade component is used on both `/files/{uuid}` and `/admin/files/{uuid}/timeline`.

---

## Public File Search

Available at `/public/file-search` — no login required.

Returns: File Number, File Name, Department, **Current Holder**, Status, Created Date.

No internal data is exposed.

---

## Running Tests

```bash
php artisan test
```

Expected: **35 tests, 103 assertions, 0 failures.**

Test coverage includes:
- File creation (attachment, duplicate number, cross-dept)
- File transfer ownership chain
- Notification mark-as-read
- Auth flows (login, logout, password reset, registration)
- Profile management
- Public file search

---

## Common Fixes

| Problem | Fix |
|---|---|
| Missing vendor folder | `composer install` |
| Storage files not loading | `php artisan storage:link` |
| Route errors | `php artisan optimize:clear` |
| DB connection error | Check `.env` credentials, restart server |
| Too many redirects | Clear browser cookies + `php artisan optimize:clear` |

---

## Update From Upstream

```bash
git remote add upstream https://github.com/ORIGINAL_OWNER/file-tracking-system.git
git fetch upstream
git merge upstream/main
```

---

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** Bootstrap 5.3, Font Awesome 6.5, Inter font
- **Database:** MySQL 8
- **Storage:** Laravel private disk (file attachments), public disk (profile photos)
- **Notifications:** Laravel database notifications with polling

---

&copy; {{ date('Y') }} FileTrack Office Portal — Government File Tracking System
