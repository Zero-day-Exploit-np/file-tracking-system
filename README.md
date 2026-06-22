# File Tracking System

A Government-Style File Tracking and Management System built with Laravel 12 for managing file movement, departmental workflows, approvals, notifications, and audit tracking.

---

## Overview

The File Tracking System helps organizations, educational institutions, and government offices manage files efficiently through a secure role-based workflow.

The system provides complete visibility of file movement from creation to final delivery while maintaining departmental security and audit records.

---

## Key Features

### Authentication & Authorization

* Secure Login & Logout
* Role-Based Access Control (RBAC)
* Super Admin, Admin, and User Roles
* Session Protection
* Access Restriction by Department

### Department & User Management

* Department Management
* Designation Management
* User Management
* Role Assignment
* Department Assignment

### File Management

* Create Files
* Upload Attachments
* View Files
* Download Files
* Unique File Number Generation
* Current Holder Tracking

### File Transfer Workflow

* User-to-User File Transfer
* Cross-Department Transfer Requests
* Admin Approval/Rejection Workflow
* File Status Management

### File Tracking

* Complete File Movement History
* Timeline Tracking
* Department Changes
* User Changes
* Current Location Tracking

### Notifications

* Transfer Notifications
* Approval Notifications
* Rejection Notifications
* Notification Center
* Sound Alerts

### Public File Submission

* Public File Upload Without Login
* Administrator Review Panel

### Audit Logs

* Login Activity
* Logout Activity
* File Creation Logs
* File Transfer Logs
* User Management Logs
* System Activity Monitoring

### Security Features

* CSRF Protection
* SQL Injection Protection
* XSS Protection
* Role-Based Authorization
* Department-Level Authorization
* Security Headers
* Session Protection
* Audit Trail

---

## User Roles

### Super Admin

Can:

* Manage Departments
* Manage Admin Users
* View All Files
* View All Timelines
* View Audit Logs
* Monitor System Activities

### Admin

Can:

* Manage Department Users
* Manage Department Files
* Approve Transfer Requests
* View Department Statistics
* Manage Designations

### User

Can:

* Create Files
* Transfer Files
* Track File Status
* View File Timeline
* Receive Notifications

---

## Technology Stack

| Technology | Version       |
| ---------- | ------------- |
| PHP        | 8.2+          |
| Laravel    | 12            |
| MySQL      | 8+            |
| Bootstrap  | 5             |
| Blade      | Laravel Blade |
| Composer   | Latest        |

---

# Installation Guide

## 1. Clone the Project

```bash
git clone <repository-url>
cd file-tracking-system
```

## 2. Install Dependencies

```bash
composer install
```

## 3. Create Environment File

Linux/macOS:

```bash
cp .env.example .env
```

Windows:

```bash
copy .env.example .env
```

## 4. Configure Database

Open `.env` and update:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=file_tracking_system
DB_USERNAME=root
DB_PASSWORD=
```

Create database:

```sql
CREATE DATABASE file_tracking_system;
```

## 5. Generate Application Key

```bash
php artisan key:generate
```

## 6. Run Migrations

```bash
php artisan migrate
```

If seeders are available:

```bash
php artisan migrate --seed
```

## 7. Create Storage Link

```bash
php artisan storage:link
```

## 8. Clear Cache

```bash
php artisan optimize:clear
```

## 9. Start the Application

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

---

# Common Commands

### Start Server

```bash
php artisan serve
```

### Run Tests

```bash
php artisan test
```

### Show Routes

```bash
php artisan route:list
```

### Clear Cache

```bash
php artisan optimize:clear
```

### Recreate Storage Link

```bash
php artisan storage:unlink
php artisan storage:link
```

---

# Troubleshooting

### Files Not Opening

Run:

```bash
php artisan storage:link
```

### Route Not Found

Run:

```bash
php artisan optimize:clear
```

### Database Connection Error

Check `.env` database configuration and run:

```bash
php artisan config:clear
```

### 403 Unauthorized

Verify:

* User Role
* Department Assignment
* Route Permissions

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

# Future Enhancements

* Email Notifications
* SMS Notifications
* QR Code Tracking
* Digital Signatures
* Mobile Application
* REST API
* Analytics Dashboard

---

# Project Status

✅ Authentication System

✅ Role-Based Access Control

✅ Department Management

✅ User Management

✅ File Creation & Tracking

✅ File Transfer Workflow

✅ Approval System

✅ File Timeline Tracking

✅ Public File Submission

✅ Notification System

✅ Audit Logs

✅ Security Hardening

✅ Government Portal UI

✅ Dashboard & Reports

🚀 Production Ready (Version 1.0)

---

# Author

**Bikram Kumar Das**

B.Sc. Computer Science

Sikkim Manipal University

---

# License

This project is intended for educational and organizational use.
