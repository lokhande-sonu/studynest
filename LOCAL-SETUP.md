# Study Nest — Local Setup Guide (Windows)

> Follow these exact steps to run the project locally after cloning from GitHub.

---

## Prerequisites

Install these on your Windows machine:

| Tool | Download / Install |
|------|-------------------|
| **XAMPP** | https://www.apachefriends.org/ (includes PHP + MySQL) |
| **PHP 8.4** | `winget install PHP.PHP.8.4` (from PowerShell as Admin) |
| **Composer** | https://getcomposer.org/Download |
| **Git** | https://git-scm.com/download/win |

### Enable PHP Extensions

Open `C:\xampp\php\php.ini` and make sure these lines are **not commented** (no `;` at the start):

```ini
extension=pdo_mysql
extension=mbstring
extension=openssl
extension=curl
extension=fileinfo
extension=gd
extension=bcmath
extension=xml
extension=ctype
extension=tokenizer
```

Restart Apache/MySQL from XAMPP Control Panel after editing.

---

## Step-by-Step Setup

### Step 1: Clone the Repository

Open PowerShell and run:

```powershell
cd C:\Users\YourName\Desktop
git clone https://github.com/imohitmehto/studynest.git
cd studynest
```

### Step 2: Start MySQL

Open **XAMPP Control Panel** → Click **Start** next to MySQL.

Verify it's running:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "SELECT 1;"
```

You should see a result table with `1`.

### Step 3: Create the Database

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE studynest_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Step 4: Import the Production Database

```powershell
cmd /c "C:\xampp\mysql\bin\mysql.exe -u root studynest_dev < ""C:\Users\YourName\Downloads\Document from _"""
```

> Replace the path with your actual SQL dump file location.

### Step 5: Set Up Environment File

```powershell
copy .env.example .env
```

Open `.env` and make sure these values are set:

```env
APP_NAME="Study Nest"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=studynest_dev
DB_USERNAME=root
DB_PASSWORD=
```

### Step 6: Generate Application Key

**Important:** Use PHP 8.4 for all `artisan` commands (the vendor folder requires it):

```powershell
C:\Users\YourName\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe artisan key:generate
```

> If you installed PHP 8.4 via winget, the path should be similar. Verify with:
> ```powershell
> Get-ChildItem "C:\Users\YourName\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4*\php.exe"
> ```

### Step 7: Run Database Migrations

```powershell
C:\Users\YourName\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe artisan migrate --force
```

You should see:

```
Running migrations.
2024_01_01_000000_create_base_tables_for_testing ................. DONE
2026_05_01_000000_add_gst_columns_to_stock_inventory ............. DONE
2026_08_25_234250_make_prod_variant_id_nullable ... .............. DONE
```

### Step 8: Clear Caches

```powershell
C:\Users\YourName\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe artisan config:clear
C:\Users\YourName\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe artisan route:clear
C:\Users\YourName\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe artisan view:clear
C:\Users\YourName\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe artisan cache:clear
```

### Step 9: Start the Development Server

```powershell
C:\Users\YourName\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe artisan serve --host=127.0.0.1 --port=8000
```

### Step 10: Open in Browser

| Page | URL |
|------|-----|
| **Homepage** | http://localhost:8000 |
| **Admin Panel** | http://localhost:8000/management/login |
| **Shop** | http://localhost:8000/shop |
| **School Shop** | http://localhost:8000/school-shop |

**Admin Login:** `admin` / `admin123`

---

## Quick Reference — All Commands in One Shot

Copy-paste this entire block into PowerShell (adjust paths for your username):

```powershell
# Set PHP 8.4 path (adjust for your system)
$php = "C:\Users\imohi\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"

# Navigate to project
cd C:\Users\imohi\Desktop\studynest

# Create database
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS studynest_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import database (adjust path to your SQL dump)
cmd /c "C:\xampp\mysql\bin\mysql.exe -u root studynest_dev < ""C:\Users\imohi\Downloads\Document from _"""

# Setup .env
if (!(Test-Path .env)) { copy .env.example .env }

# Run migrations
& $php artisan migrate --force

# Clear caches
& $php artisan config:clear
& $php artisan route:clear
& $php artisan view:clear
& $php artisan cache:clear

# Start server
& $php artisan serve --host=127.0.0.1 --port=8000
```

---

## Troubleshooting

### "Composer detected issues in your platform: PHP version >= 8.4.1"

You're using XAMPP's PHP 8.0 instead of PHP 8.4. Use the full PHP 8.4 path for all `artisan` commands.

### "Can't connect to MySQL server on 'localhost'"

Start MySQL from XAMPP Control Panel.

### "No application encryption key has been specified"

Run: `php artisan key:generate`

### "Class 'App\Enums\OrderStatus' not found"

Run: `composer dump-autoload`

### "500 Internal Server Error"

1. Check `.env` has `APP_KEY` set
2. Run `php artisan config:clear`
3. Check `storage/` folder has write permissions

### Port 8000 Already in Use

Use a different port:

```powershell
& $php artisan serve --host=127.0.0.1 --port=8080
```

Then open http://localhost:8080

---

**Last updated:** August 26, 2026
