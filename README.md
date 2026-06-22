# File Tracking System

## Overview

File Tracking System is a Laravel 12 based Government-Style File Management and Tracking Portal designed for organizations, educational institutions, government offices, and departments.

The system enables secure file creation, tracking, transfer, approval workflows, audit logging, and role-based access management across departments.

---

# Key Features

## Authentication & Authorization

* Secure Login System
* Email Verification
* Password Reset
* Role-Based Access Control (RBAC)
* Session Security
* Account Activity Logging

### Roles

* Super Admin
* Admin
* User

---

# Super Admin Features

Super Admin has complete system access.

### Can Manage

* Departments
* Designations
* Admin Users
* System Users
* Public File Submissions
* Audit Logs

### Can View

* All Files
* All Departments
* Transfer Requests
* System Statistics
* User Activities

---

# Admin Features

Department-level administration.

### Can Manage

* Department Users
* Department Designations
* Department Files

### Can

* Approve Transfer Requests
* Reject Transfer Requests
* View Department Statistics
* Monitor User Activities

---

# User Features

### Can

* Create Files
* Upload Attachments
* View Assigned Files
* Transfer Files
* Track File Status
* Receive Notifications
* Manage Profile
* Upload/Delete Profile Photo
* Change Password

---

# File Management

### File Creation

Each file receives:

* Unique File Number
* Department Assignment
* Creator Information
* Current Holder
* Creation Date

### File Tracking

Track:

* Creator
* Current Holder
* Department
* Status
* Complete Movement History

### File Status

* Active
* Pending Transfer
* Approved
* Rejected
* Archived

---

# Transfer Workflow

## Same Department Transfer

User → User

Transfer occurs immediately.

---

## Cross Department Transfer

User → Different Department

Workflow:

User Request
↓
Destination Department Admin
↓
Approve / Reject
↓
Transfer Completed

All actions are recorded in audit logs.

---

# Notifications

Users receive notifications for:

* Transfer Requests
* Transfer Approval
* Transfer Rejection
* New File Assignments

Features:

* Notification Bell
* Unread Count
* Notification History
* Sound Alerts

---

# Audit Logs

Tracks all major activities:

* Login
* Logout
* Profile Update
* Password Change
* File Creation
* File Transfer
* Transfer Approval
* Transfer Rejection
* User Creation
* Department Creation

Each log stores:

* User
* Action
* Timestamp
* IP Address

---

# Public File Submission

Visitors can submit files without logging in.

Submitted files are reviewed by administrators.

---

# Security Features

* Role-Based Authorization
* Department-Level Access Control
* CSRF Protection
* XSS Protection
* SQL Injection Protection
* Session Security
* Secure Password Hashing
* File Validation
* Audit Logging
* Rate Limiting
* Security Headers

---

# Technology Stack

| Component       | Technology   |
| --------------- | ------------ |
| Backend         | Laravel 12   |
| Language        | PHP 8.2+     |
| Database        | MySQL 8+     |
| Frontend        | Bootstrap 5  |
| Icons           | Font Awesome |
| Template Engine | Blade        |
| Authentication  | Laravel Auth |

---

# Local Installation Guide

## Step 1: Clone Project

```bash
git clone <repository-url>
```

or download ZIP and extract.

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

Linux/Mac:

```bash
cp .env.example .env
```

Windows:

```bash
copy .env.example .env
```

---

## Step 5: Configure Database

Create database:

```sql
CREATE DATABASE file_tracking_system;
```

Update `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=file_tracking_system
DB_USERNAME=root
DB_PASSWORD=
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
php artisan migrate --seed
```

---

## Step 8: Create Storage Link

Required for profile photos and uploads.

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

Open browser:

```text
http://127.0.0.1:8000
```

---

# Creating First Super Admin

Open database and update any user:

```sql
UPDATE users
SET role='super_admin'
WHERE email='your-email@example.com';
```

Then login using that account.

---

# Useful Commands

## Start Server

```bash
php artisan serve
```

## Show Routes

```bash
php artisan route:list
```

## Run Tests

```bash
php artisan test
```

## Clear Cache

```bash
php artisan optimize:clear
```

## Rebuild Cache

```bash
php artisan optimize
```

## Recreate Storage Link

```bash
php artisan storage:unlink
php artisan storage:link
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
├── app/
├── public/
```

---

# Production Deployment

### Requirements

* PHP 8.2+
* Composer
* MySQL 8+
* Apache or Nginx

### Commands

```bash
composer install --no-dev
php artisan migrate
php artisan storage:link
php artisan optimize
```

Set web root to:

```text
/public
```

---

# Future Enhancements

* Mobile Application
* Email Notifications
* SMS Notifications
* Digital Signatures
* QR Code Tracking
* REST API
* Analytics Dashboard
* File Version Control

---

# Developer

**Bikram Kumar Das**

B.Sc. Computer Science

Sikkim Manipal University

---

# License

This project is intended for educational, organizational, and research purposes.
