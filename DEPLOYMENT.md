# VSP TIMES E-Paper — Production Deployment Guide
**Target Subdomain:** `https://epaper.vspmedianetwork.com/`  
**Framework:** Laravel 13 on PHP 8.2+ / MySQL 8.0  
**Hosting Environment:** Hostinger Cloud / VPS / Shared cPanel  

---

## 1. System Requirements & Hosting Prerequisites

* **PHP Version:** 8.2 or 8.3 (CLI & FPM)
* **PHP Extensions:**
  * `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`, `zip`
  * `imagick` (recommended on Linux production for raster broadsheet decomposition) or `poppler-utils` (`pdftoppm`).
* **Web Server:** Nginx or Apache with `mod_rewrite` enabled.
* **Document Root:** Must point strictly to the `public/` directory (NOT the project root).
* **SSL Certificate:** Active Let's Encrypt / Hostinger SSL for `epaper.vspmedianetwork.com`.

---

## 2. Directory Structure on Hostinger

Create a dedicated folder for the E-Paper subdomain:
```
/home/u123456789/domains/epaper.vspmedianetwork.com/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/                 <--- SUBDOMAIN ROOT TARGET
│   ├── index.php
│   ├── storage -> ../storage/app/public
│   └── build/
├── storage/
│   ├── app/
│   │   ├── epaper/
│   │   │   └── staging/    <--- PRIVATE MASTER PDF ARCHIVE
│   │   └── public/
│   ├── framework/
│   └── logs/
└── .env
```

---

## 3. Step-by-Step Installation

### Step 1: Upload or Clone Standalone Codebase
```bash
cd /home/u123456789/domains/epaper.vspmedianetwork.com/
git clone <git-repository-url> .
```

### Step 2: Install PHP & Frontend Dependencies
```bash
# Install PHP production packages (no dev)
composer install --no-dev --optimize-autoloader

# Build production assets (Vite)
npm install
npm run build
```

### Step 3: Configure Environment
Copy `.env.production.example` to `.env`:
```bash
cp .env.production.example .env
nano .env
```
Fill in the MySQL credentials, `APP_URL=https://epaper.vspmedianetwork.com`, and run:
```bash
php artisan key:generate
```

### Step 4: Storage Permissions & Symbolic Link
```bash
# Set folder permissions
chmod -R 775 storage bootstrap/cache
chmod -R 775 storage/app/epaper

# Link public storage
php artisan storage:link
```

### Step 5: Database Migrations & Initial Seeding
```bash
# Run migrations safely
php artisan migrate --force

# Seed default editorial roles and initial regions (Main, Visakhapatnam, Amaravati, Hyderabad)
php artisan db:seed --class=EpaperSampleSeeder --force
```

### Step 6: Optimize Laravel for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Production Cron Job Setup (Daily 5:00 AM IST Publication)

In Hostinger cPanel / Advanced Cron Jobs, add a 1-minute runner:

```bash
* * * * * cd /home/u123456789/domains/epaper.vspmedianetwork.com && php artisan schedule:run >> /dev/null 2>&1
```

The application's internal schedule automatically triggers `epaper:publish-scheduled` at **05:00 AM IST** every morning with `withoutOverlapping()`.

---

## 5. Security & Isolation Verification

* **Independence:** The main portal at `https://vspmedianetwork.com/` runs independently. No files or tables are shared directly.
* **Master PDF Privacy:** Raw uploaded master PDFs reside in `storage/app/epaper/staging/{edition_id}/` (outside `public_html`).
* **Role-Based Protection:** Only authorized staff with `super_admin` or `admin` roles can publish or access system settings.
* **Rate Limiting:** Login attempts are rate-limited to prevent brute-force attacks.
