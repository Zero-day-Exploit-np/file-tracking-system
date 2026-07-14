# Contributing Guide

Thank you for your interest in contributing to the File Tracking System.

Please read this guide before making changes.

---

# Development Setup

## Requirements

- PHP 8.2+
- Composer
- Laravel 12
- MySQL
- Node.js & npm

---

## Installation

Clone the repository

```bash
git clone <repository-url>
cd file-tracking-system
```

Install dependencies

```bash
composer install
npm install
```

Copy environment

```bash
cp .env.example .env
```

Generate application key

```bash
php artisan key:generate
```

Configure database in `.env`

Run migrations

```bash
php artisan migrate
```

Run development server

```bash
php artisan serve
```

Compile assets

```bash
npm run dev
```

---

# Coding Standards

- Follow PSR-12 coding style.
- Keep controllers thin.
- Business logic should remain in Services or dedicated classes.
- Use Laravel Form Requests for validation whenever possible.
- Write readable code.
- Avoid duplicate logic.

---

# Git Workflow

Create a new branch before starting work.

Example:

```bash
git checkout -b feature/file-transfer
```

Commit using Conventional Commits.

Examples:

```text
feat: add department transfer search

fix: resolve timeline visibility bug

refactor: simplify transfer controller

docs: update README

test: add transfer authorization tests
```

---

# Pull Requests

Before opening a Pull Request, ensure:

- Code compiles successfully.
- No PHP syntax errors.
- Run

```bash
php artisan test
```

All tests should pass.

---

# UI Guidelines

Use:

- Bootstrap 5
- Responsive layouts
- Consistent spacing
- Accessible colors
- Modern cards
- Government-style professional UI

Avoid unnecessary animations.

---

# File Transfer Rules

When modifying file transfer functionality:

- Never break ownership rules.
- Preserve timeline history.
- Preserve movement remarks.
- Do not delete movement records.
- Maintain department visibility rules.
- Super Admin must always see the complete timeline.

---

# Security

Never expose:

- Passwords
- Tokens
- Hidden IDs
- Sensitive user information

Always validate requests on the server.

Do not trust client-side input.

---

# Testing

Run

```bash
php artisan test
```

before every commit.

New features should include tests whenever practical.

---

# Documentation

Update documentation whenever:

- New routes are added.
- Database schema changes.
- New permissions are introduced.
- UI workflow changes.
- APIs change.

---

Thank you for contributing to the File Tracking System.
