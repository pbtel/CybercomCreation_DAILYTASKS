# Files Explanation and Cleanup Plan

## Batch Files in `database/` Directory

### 1. `enable-pgsql.bat`
**Purpose:** One-time setup script that enabled PostgreSQL PHP extensions in your XAMPP php.ini file.
**Status:** ✅ Already completed - extensions are enabled
**Action:** Can be safely removed (already did its job)

### 2. `setup.bat`
**Purpose:** Windows batch script to create database using psql command-line tool.
**Status:** ⚠️ Not used - we used `setup.php` instead because psql wasn't in PATH
**Action:** Can be safely removed (we use setup.php instead)

## JSON Files in `data/` Directory

### 1. `users_db.json.migrated`
**Purpose:** Archived original user data from before database migration
**Status:** ✅ Already migrated to PostgreSQL `users` table
**Action:** Can be safely removed (data is in database)

### 2. `orders_db.json.migrated`
**Purpose:** Archived original order data from before database migration
**Status:** ✅ Already migrated to PostgreSQL `sales_order` tables
**Action:** Can be safely removed (data is in database)

## Backup Files in `includes/` Directory

### 1. `products_static.php.bak`
**Purpose:** Backup of original products.php file (with static PHP arrays)
**Why .bak:** Created before replacing with database version
**Status:** Archive of old implementation
**Action:** Can be removed (you have database version working)

### 2. `categories_static.php.bak`
**Purpose:** Backup of original categories.php file (with static PHP arrays)
**Why .bak:** Created before replacing with database version
**Status:** Archive of old implementation
**Action:** Can be removed (you have database version working)

### 3. `brands_static.php.bak`
**Purpose:** Backup of original brands.php file (with static PHP arrays)
**Why .bak:** Created before replacing with database version
**Status:** Archive of old implementation
**Action:** Can be removed (you have database version working)

## Summary

All these files were created during the migration process:
- **Batch files**: One-time setup scripts (already executed)
- **JSON archives**: Old data files (already migrated to database)
- **BAK files**: Backups of old code (before database conversion)

**All can be safely removed** since:
✅ Database is set up and working
✅ Data is migrated and verified
✅ New database-driven code is working
