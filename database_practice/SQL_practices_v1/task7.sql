DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS customers CASCADE;

CREATE TABLE customers (
    customer_id SERIAL PRIMARY KEY,
    customer_name VARCHAR(100)
);

CREATE TABLE products (
    product_id SERIAL PRIMARY KEY,
    product_name VARCHAR(100),
    category VARCHAR(100)
);

CREATE TABLE orders (
    order_id SERIAL PRIMARY KEY,
    customer_id INT,
    product_id INT,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);


INSERT INTO customers (customer_name) VALUES
('Diya'),
('Riya'),
('Akash');

INSERT INTO products (product_name, category) VALUES
('Laptop', 'Electronics'),
('Phone', 'Electronics'),
('Shirt', 'Clothing'),
('Jeans', 'Clothing'),
('Bread', 'Food');

INSERT INTO orders (customer_id, product_id) VALUES
(1,1), 
(1,3), 
(1,5), 
(2,1), 
(2,3), 
(3,1), 
(3,3), 
(3,5);


-- Find customers who have purchased from ALL product categories
SELECT c.customer_id, c.customer_name
FROM customers c
WHERE NOT EXISTS (
    -- Find any category that customer c has NOT bought from
    SELECT DISTINCT p.category
    FROM products p
    WHERE NOT EXISTS (
        -- Check if customer c bought anything from this category p
        SELECT 1
        FROM orders o
        JOIN products p2 ON o.product_id = p2.product_id
        WHERE o.customer_id = c.customer_id  -- Orders by this customer
        AND p2.category = p.category  -- Products from this category
    )
);
-- Logic: If NOT EXISTS finds no categories that customer hasn't bought from,
-- then customer has bought from ALL categories
