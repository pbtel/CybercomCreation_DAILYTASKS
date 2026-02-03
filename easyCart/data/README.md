# Data Directory - Archive

This directory contains archived JSON files from the migration to PostgreSQL database.

## Archived Files

- `users_db.json.migrated` - Original user data (migrated to PostgreSQL `users` table)
- `orders_db.json.migrated` - Original order data (migrated to PostgreSQL `sales_order` tables)

## Important Notes

**The application is now fully database-driven using PostgreSQL.**

- All user data is stored in the `users` table
- All order data is stored in `sales_order`, `sales_order_product`, and `sales_order_address` tables
- All product data is stored in `catalog_product_entity` and related tables
- Cart data is managed in session (can be persisted to `sales_cart` tables if needed)

## These archived files can be safely deleted after verifying the database migration is successful.

To verify the migration:
```bash
cd database
php setup.php    # If database needs to be recreated
php migrate.php  # If data needs to be re-migrated
```

## Database Connection

The application uses PostgreSQL database `easycart_db` with the following configuration:
- Host: localhost
- Port: 5432
- Database: easycart_db
- User: postgres

Configuration file: `config/database.php`
