# File Tracking System

A Laravel-based **Government Style File Tracking and Management System** designed for organizations, educational institutions, government offices, and departments to track file movement, approvals, and departmental workflows.

---

# Features

## Authentication & Roles

* Secure Login System
* Role-Based Access Control
* Super Admin
* Admin
* User

---

## Department Management

* Create Departments
* Edit Departments
* Delete Departments
* Department-wise File Access

---

## Designation Management

* Create Designations
* Manage Designations
* Assign Designations to Users

---

## User Management

* Create Users
* Edit Users
* Delete Users
* Department Assignment
* Role Assignment

---

## File Management

* Create Files
* Upload Attachments
* View Files
* Download Files
* Edit Files
* Delete Files

---

## File Transfer System

* Transfer Files Between Departments
* Transfer Files Between Users
* Approval Workflow
* Transfer Requests

---

## File Movement Timeline

Track complete file history:

* File Created
* Transfer Requested
* Transfer Approved
* Transfer Rejected
* Department Changes
* User Changes

---

## Public File Submission

Public users can submit files from the landing page.

Submitted files can be reviewed by administrators.

---

## Notifications

* Transfer Notifications
* Approval Notifications
* Rejection Notifications
* Notification Center

---

## Audit Logs

Track system activities:

* Login
* Logout
* File Creation
* File Updates
* File Transfers
* User Management Activities

---

# Technology Stack

| Technology | Version       |
| ---------- | ------------- |
| PHP        | 8.2+          |
| Laravel    | 12            |
| MySQL      | 8+            |
| Bootstrap  | 5             |
| Blade      | Laravel Blade |
| Composer   | Latest        |

---

# System Roles

## Super Admin

Can:

* Manage All Departments
* Manage All Users
* View All Files
* View All Timelines
* Approve Transfers
* View Audit Logs
* Access Entire System

---

## Admin

Can:

* Manage Files of Own Department
* View Department Files
* Approve Department Transfers
* Manage Department Users

Cannot:

* Access Other Departments
* Access Super Admin Features

---

## User

Can:

* View Assigned Files
* Transfer Assigned Files
* View Own Timeline
* Receive Notifications

Cannot:

* Manage Users
* Manage Departments
* Access Admin Panels

---

# Installation Guide

## Step 1: Download Project

Clone project:

```bash
git clone https://github.com/your-repository/file-tracking-system.git
```

Or extract ZIP file.

---

## Step 2: Open Project

```bash
cd file-tracking-system
```

---

## Step 3: Install Dependencies

```bash
composer install
```

---

## Step 4: Create Environment File

Copy:

```bash
cp .env.example .env
```

Windows:

```bash
copy .env.example .env
```

---

## Step 5: Configure Database

Open `.env`

Update:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=file_tracking_system
DB_USERNAME=root
DB_PASSWORD=
```

Create database in MySQL:

```sql
CREATE DATABASE file_tracking_system;
```

---

## Step 6: Generate Application Key

```bash
php artisan key:generate
```

---

## Step 7: Run Migrations

```bash
php artisan migrate
```

If seeders exist:

```bash
php artisan db:seed
```

Or

```bash
php artisan migrate --seed
```

---

## Step 8: Create Storage Link

Required for file uploads.

```bash
php artisan storage:link
```

Expected output:

```text
The [public/storage] link has been connected.
```

---

## Step 9: Clear Cache

```bash
php artisan optimize:clear
```

---

## Step 10: Start Application

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

---

# Deployment on Own Server

## Shared Hosting

Upload project files.

Run:

```bash
composer install --no-dev
```

Set document root:

```text
/public
```

Run:

```bash
php artisan storage:link
php artisan optimize
```

---

## VPS / Linux Server

Install:

* PHP 8.2+
* Composer
* MySQL
* Nginx or Apache

Run:

```bash
composer install
php artisan migrate
php artisan storage:link
php artisan optimize
```

Configure web server to point to:

```text
project/public
```

---

# Common Commands

## Start Server

```bash
php artisan serve
```

---

## Run Tests

```bash
php artisan test
```

---

## Show Routes

```bash
php artisan route:list
```

---

## Clear Cache

```bash
php artisan optimize:clear
```

---

## Recreate Storage Link

```bash
php artisan storage:unlink
php artisan storage:link
```

---

# Troubleshooting

## Files Not Opening

Run:

```bash
php artisan storage:link
```

Check:

```text
storage/app/public
```

---

## 403 Unauthorized

Verify:

* User Role
* Department Assignment
* Route Middleware

---

## Database Connection Error

Verify `.env` database credentials.

Run:

```bash
php artisan config:clear
```

---

## Route Not Found

Run:

```bash
php artisan route:list
```

Then clear cache:

```bash
php artisan optimize:clear
```

---

# Project Structure

```text
app/
 ├── Http/
 ├── Models/
 ├── Notifications/

database/
 ├── migrations/

resources/
 ├── views/

routes/
 ├── web.php

storage/
 ├── app/public
```

---

# Security Features

* Role-Based Access Control
* Department-Level Authorization
* File Ownership Validation
* Secure Downloads
* Request Validation
* CSRF Protection
* Authentication Middleware

---

# Future Improvements

* Email Notifications
* PDF Reports
* Digital Signatures
* QR Code Tracking
* Advanced Search
* Mobile Application
* REST API

---

# Author

Bikram Kumar Das

B.Sc. Computer Science

Sikkim Manipal University

---

# License

This project is intended for educational and organizational use.
