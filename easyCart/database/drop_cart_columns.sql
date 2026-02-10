-- Drop unnecessary columns from sales_cart
ALTER TABLE sales_cart
DROP COLUMN IF EXISTS order_number,
DROP COLUMN IF EXISTS coupon_code,
DROP COLUMN IF EXISTS user_id;
