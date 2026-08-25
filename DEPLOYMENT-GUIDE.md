# Study Nest — Deployment Guide

> **Version:** 1.0 — August 26, 2026  
> **Repository:** https://github.com/imohitmehto/studynest  
> **Production URL:** https://studynested.com  
> **Hosting:** Hostinger PHP Hosting (Shared)

---

## Table of Contents

1. [Overview](#overview)
2. [Local Development Setup](#local-development-setup)
3. [Running Tests](#running-tests)
4. [What's Included (Bug Fixes)](#whats-included)
5. [Production Deployment on Hostinger](#production-deployment-on-hostinger)
6. [Environment Variables Reference](#environment-variables-reference)
7. [Database Reference](#database-reference)
8. [Troubleshooting](#troubleshooting)

---

## Overview

Study Nest is a Laravel 9.x e-commerce platform for school supplies:

- **Backend:** Laravel 9.x (PHP 8.0+)
- **Database:** MySQL / MariaDB
- **Frontend:** Blade templates + Bootstrap + jQuery
- **Payment Gateway:** PayU
- **PDF Generation:** DomPDF
- **Excel Import/Export:** PhpSpreadsheet

---

## Local Development Setup

### Step 1: Clone the Repository

```bash
git clone https://github.com/imohitmehto/studynest.git
cd studynest
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Create Environment File

```bash
copy .env.example .env
```

> On Linux/Mac: `cp .env.example .env`

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Configure Database

Open `.env` and set your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studynest_dev
DB_USERNAME=root
DB_PASSWORD=
```

Create the database:

```sql
CREATE DATABASE studynest_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 6: Import Database

```bash
mysql -u root -p studynest_dev < database/migrations.sql
```

### Step 7: Create Upload Directories

```bash
mkdir -p public/uploads/product-category-photo
mkdir -p public/uploads/product-photo
mkdir -p public/uploads/customer-profile-photo
mkdir -p public/uploads/management-profile-photo
mkdir -p public/uploads/app-slider-photo
mkdir -p public/uploads/sample-excel-files
```

### Step 8: Start Development Server

```bash
php artisan serve
```

Site: **http://localhost:8000**  
Admin: **http://localhost:8000/management/login** (`admin` / `admin123`)

---

## Running Tests

```bash
php artisan test
```

### Test Coverage

| File | Tests | Coverage |
|------|-------|----------|
| `tests/Unit/OrderStatusTest.php` | 33 | Enums |
| `tests/Feature/HomePageFilterTest.php` | 16 | Filter/sort |
| `tests/Feature/InvoiceCalculationTest.php` | 23 | GST, pricing |
| `tests/Feature/UserDataTest.php` | 13 | Profile pre-fill |
| `tests/Feature/OrderManagementTest.php` | 15 | Status labels, stock |

---

## What's Included

### 19 Bug Fixes Across 4 Requirements

#### Requirement 1: Home Page Filters (2 fixes)
- Fixed sort dropdown JavaScript (was outside `<form>`, submit failed)
- Removed duplicate mobile/desktop `<select>` elements

#### Requirement 2: Invoice Calculation (4 fixes — 1 CRITICAL)
- Fixed GST double-counting for inclusive GST products
- Fixed checkout price source: uses `ProductStockInventory` instead of `ProductVariant`
- Fixed delivery charges: iterates active charges
- Fixed customer order subtotal display

#### Requirement 3: User Data Management (2 fixes)
- Added address/city/state/pincode pre-fill from customer profile
- Added customer profile update after order placement

#### Requirement 4: Order Management (10 fixes — 2 CRITICAL)
- Fixed status label mismatch across ALL admin views
- Fixed PDF export to use `OrderStatus::label()`
- Fixed export transaction report status mapping
- Fixed stock restore: only restores for CONFIRMED orders
- Fixed payment status sync: sets CONFIRMED when Paid
- Fixed `Log::error` → `Log::debug`
- Fixed duplicate `name="order_status"` in transaction report
- Fixed payment status value mapping inconsistency

---

## Production Deployment on Hostinger

### Prerequisites

- Access to Hostinger hPanel (https://hpanel.hostinger.com)
- Access to Hostinger File Manager or FTP
- Existing Hostinger PHP hosting plan (already running studynested.com)
- GitHub repository access

### Method 1: Deploy Via Hostinger SSH Terminal (Recommended)

Hostinger provides a web-based SSH terminal in hPanel.

#### Step 1: Open Hostinger SSH Terminal

1. Login to **hPanel**: https://hpanel.hostinger.com
2. Select your **studynested.com** hosting plan
3. Go to **Advanced** → **SSH Terminal** (or **Terminal**)
4. Connect to the SSH session

> If SSH Terminal is not available on your plan, use **Method 2** (File Manager) below.

#### Step 2: Navigate to the Website Root

```bash
cd ~/studynested.com
```

> On Hostinger, the website root is usually at `~/domainname.com` or `~/public_html`.

#### Step 3: Backup Current .env (IMPORTANT)

```bash
cp .env .env.backup-$(date +%Y%m%d)
```

> Never lose the production `.env` — it has live PayU keys, database credentials, and APP_KEY.

#### Step 4: Pull Latest Code from GitHub

```bash
git pull origin main
```

> If git is not initialized on the server, use **Method 3** (Manual Upload) below.

#### Step 5: Install/Update Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

#### Step 6: Restore Production .env

```bash
cp .env.backup-$(date +%Y%m%d) .env
```

> Or manually edit `.env` to ensure production values are correct.

#### Step 7: Run Database Migration

```bash
php artisan migrate --force
```

> This runs the new migration that makes `prod_variant_id` nullable on `tbl_product_stock_inventory`.

#### Step 8: Clear and Rebuild Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
composer dump-autoload
```

#### Step 9: Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/uploads
```

#### Step 10: Verify the Site

Open https://studynested.com in your browser and test:
- [ ] Homepage loads with products
- [ ] Sort/filter dropdown works on homepage
- [ ] Add to cart works
- [ ] Checkout page loads with correct prices
- [ ] GST calculated correctly (not double-counted)
- [ ] Place a test order (COD mode)
- [ ] Admin panel at /management/login works
- [ ] Order report shows correct status labels
- [ ] PDF export downloads with correct labels

---

### Method 2: Deploy Via Hostinger File Manager

If SSH is not available, use the Hostinger File Manager.

#### Step 1: Prepare Files Locally

On your local machine, run:

```bash
# Install production dependencies (no dev packages)
composer install --no-dev --optimize-autoloader

# Build cache files locally
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> If local artisan commands fail, skip the cache steps — you'll clear them via File Manager.

#### Step 2: Upload via File Manager

1. Login to **hPanel** → **File Manager**
2. Navigate to `~/studynested.com` (or `~/public_html`)
3. **DO NOT upload `.env`** — the existing production `.env` must stay
4. Upload the following files/folders (overwrite existing):

**Upload these folders (replace entirely):**
```
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
vendor/
```

**Upload these files:**
```
artisan
composer.json
composer.lock
phpunit.xml
```

**Do NOT upload:**
```
.env            ← KEEP the existing production .env
.env.example    ← Not needed on production
.env.testing    ← Not needed on production
tests/          ← Not needed on production
docs/           ← Not needed on production
.git/           ← Not needed on production
```

#### Step 3: Upload the New Migration Only

Upload this specific file:
```
database/migrations/2026_08_25_234250_make_prod_variant_id_nullable_on_stock_inventory_table.php
```

#### Step 4: Fix Permissions via File Manager

Right-click on `storage/` → Set Permissions → **775**  
Right-click on `bootstrap/cache/` → Set Permissions → **775**  
Right-click on `public/uploads/` → Set Permissions → **775**

#### Step 5: Run Migration via Hostinger SSH or PHP

If SSH is available:
```bash
cd ~/studynested.com
php artisan migrate --force
```

If SSH is NOT available, create a temporary file `run-migration.php` in the `public/` folder:

```php
<?php
// TEMPORARY FILE — DELETE AFTER RUNNING
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->call('migrate', ['--force' => true]);
echo $status;
```

Visit: `https://studynested.com/run-migration.php`

Then **DELETE** `run-migration.php` immediately.

#### Step 6: Clear Caches

If SSH is available:
```bash
cd ~/studynested.com
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
composer dump-autoload
```

---

### Method 3: Deploy Via FTP/SFTP

If neither SSH nor File Manager works for bulk upload.

#### Step 1: Connect via FTP

Use FileZilla or any FTP client:
- **Host:** Your Hostinger FTP host (from hPanel)
- **Username:** Your Hostinger FTP username
- **Password:** Your Hostinger FTP password
- **Port:** 22 (SFTP) or 21 (FTP)

#### Step 2: Upload Files

Navigate to the website root (usually `public_html` or the domain folder).

Upload everything EXCEPT:
- `.env` (keep the production one)
- `tests/` (not needed on production)
- `.git/` (not needed on production)
- `docs/` (not needed on production)
- `.env.example`, `.env.testing`

#### Step 3: Run Migration

Use Hostinger PHP terminal or the temporary script method from Method 2, Step 5.

#### Step 4: Fix Permissions

Set these folders to 775:
- `storage/`
- `bootstrap/cache/`
- `public/uploads/`

---

## Environment Variables Reference

Your production `.env` should have these values:

```env
APP_NAME="Study Nest"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://studynested.com
APP_KEY=base64:...          ← KEEP your existing key

DB_CONNECTION=mysql
DB_HOST=localhost           ← Hostinger uses localhost
DB_PORT=3306
DB_DATABASE=u1234567_studynest   ← Your Hostinger DB name
DB_USERNAME=u1234567_admin       ← Your Hostinger DB user
DB_PASSWORD=...                  ← Your Hostinger DB password

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# File Upload Folders
PRODUCT_CATEGORY_PHOTO="uploads/product-category-photo"
PRODUCT_PHOTO="uploads/product-photo"
CUSTOMER_PROFILE_PHOTO="uploads/customer-profile-photo"
MANAGEMENT_PROFILE_PHOTO="uploads/management-profile-photo"
APP_SLIDER="uploads/app-slider-photo"
SAMPLE_EXCEL="uploads/sample-excel-files"

CATEGORY_DISPLAY_ORDER="Books,Uniforms,Accessories,Premium"

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io      ← Or your SMTP host
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="notification@studynested.com"
MAIL_FROM_NAME="Study Nest"
ADMIN_EMAIL=admin@studynested.com

# PayU (Production)
PAYU_MERCHANT_KEY=...           ← Your live PayU key
PAYU_MERCHANT_SALT=...          ← Your live PayU salt
PAYU_MODE=production

# WhatsApp / SMS (if configured)
WHATSAPP_API_KEY=...
SMS_AUTH_KEY=...
```

> **Important:** On Hostinger, `DB_HOST` is usually `localhost`, not `127.0.0.1`. Check your Hostinger database details in hPanel → Databases.

---

## Database Reference

### New Migration (Required)

```sql
-- This runs automatically via: php artisan migrate --force
-- Makes prod_variant_id nullable on tbl_product_stock_inventory
ALTER TABLE tbl_product_stock_inventory 
MODIFY COLUMN prod_variant_id INT NULL;
```

### Key Enum Values

| Field | Values |
|-------|--------|
| `order_status` | 0 = Cancelled, 1 = Pending, 2 = Confirmed, 3 = Delivered |
| `order_payment_status` | 0 = Failed, 1 = Paid, 2 = Pending |
| `order_payment_mode` | 0 = Cashfree, 1 = PayU, 2 = COD |

### Tables (29 total)

Products, orders, customers, schools, classes, subjects, cart, charges, bundles, blogs, enquiries, pre-bookings, and supporting tables.

---

## Troubleshooting

### "Class 'App\Enums\OrderStatus' not found"

```bash
composer dump-autoload
```

On Hostinger without SSH, re-upload the `vendor/` folder.

### "500 Internal Server Error"

1. Check `.env` exists and has correct `APP_KEY`
2. Check `storage/` and `bootstrap/cache/` have 775 permissions
3. Check Hostinger error logs in hPanel → **Logs** → **Error Logs**

### "No application encryption key has been specified"

Your `.env` file is missing or `APP_KEY` is empty. Restore from backup or generate a new key.

### PayU Not Working in Production

1. Verify `PAYU_MODE=production` in `.env`
2. Verify `PAYU_MERCHANT_KEY` and `PAYU_MERCHANT_SALT` are your LIVE keys
3. Check PayU dashboard for transaction logs

### Prices Showing Wrong on Checkout

The price source was changed from `ProductVariant` to `ProductStockInventory`. If prices look wrong:
1. Check `tbl_product_stock_inventory` has correct `selling_price` values
2. Clear view cache: `php artisan view:cache` (rebuild)

### Status Labels Showing Numbers Instead of Words

The order status views were updated. If you see "1" instead of "Pending":
1. Make sure all Blade files were uploaded (the updated versions)
2. Clear view cache: `php artisan view:cache`

### GST Double-Counted on Invoice

This was the critical bug fix. If GST is still doubled:
1. Ensure `WebsiteController.php` was updated (the `placeOrder` method)
2. Clear config cache: `php artisan config:cache`

---

## Post-Deployment Checklist

After deploying to Hostinger, verify:

- [ ] Site loads at https://studynested.com
- [ ] Homepage products display correctly
- [ ] Sort/filter dropdown works (select class/subject/sort)
- [ ] Product detail page loads with correct price
- [ ] Add to cart works
- [ ] Checkout page shows correct prices (no double GST)
- [ ] Delivery charges calculated correctly
- [ ] Address/city/state/pincode pre-filled from profile
- [ ] Place order (COD) works
- [ ] Customer profile updated after order
- [ ] My Orders page shows correct subtotal
- [ ] Admin login works at /management/login
- [ ] Admin dashboard loads
- [ ] Order list shows status labels (Pending/Confirmed/Delivered)
- [ ] Order detail page shows correct status labels
- [ ] Order report shows correct filter and status labels
- [ ] Transaction report shows correct labels
- [ ] PDF export downloads with correct status text
- [ ] PayU payment works (test with small amount)
- [ ] No 500 errors in Hostinger error logs

---

**Built by:** Mohit Mehta  
**Date:** August 2026  
**Repository:** https://github.com/imohitmehto/studynest
