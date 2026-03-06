DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS products;


CREATE TABLE IF NOT EXISTS customers (
    customer_id SERIAL PRIMARY KEY,
    customer_name VARCHAR(100),
    region VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS products (
    product_id SERIAL PRIMARY KEY,
    product_name VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS orders (
    order_id SERIAL PRIMARY KEY,
    customer_id INT,
    order_date DATE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE IF NOT EXISTS order_items (
    order_item_id SERIAL PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);


INSERT INTO customers (customer_name, region) VALUES
('Alice', 'North America'),
('Bob', 'Europe'),
('Charlie', 'North America'),
('David', 'Asia');

INSERT INTO products (product_name) VALUES
('Laptop'),
('Phone'),
('Tablet'),
('Camera'),
('Headphones');

INSERT INTO orders (customer_id, order_date) VALUES
(1, '2025-01-10'),
(2, '2025-02-12'),
(3, '2025-03-15');

INSERT INTO order_items (order_id, product_id, quantity) VALUES
(1, 1, 1),
(1, 2, 2),
(2, 3, 1),
(3, 2, 1);


-- Select product details from products table
SELECT p.product_id, p.product_name
FROM products p

-- Return only those products for which the subquery returns NO rows
-- NOT EXISTS means: if the subquery finds any matching record,
-- the product will be excluded
WHERE NOT EXISTS (

    -- Subquery checks whether this product was ever ordered
    SELECT 1
    
    -- order_items tells which product belongs to which order
    FROM order_items oi
    
    -- join orders to get order information
    JOIN orders o 
    ON oi.order_id = o.order_id
    
    -- join customers to know which customer placed the order
    JOIN customers c 
    ON o.customer_id = c.customer_id

    -- correlated condition:
    -- this connects the outer query product with the subquery
    -- it checks orders for the current product from the outer query
    WHERE oi.product_id = p.product_id

    -- filter orders placed by customers from North America
    AND c.region = 'North America'
);



-- APPROCH 2 
-- Select product details
-- DISTINCT is used because joins may create duplicate rows for a product
SELECT DISTINCT p.product_id, p.product_name
FROM products p

-- LEFT JOIN keeps all products even if they are not ordered
LEFT JOIN order_items oi 
ON p.product_id = oi.product_id

-- Join orders to know which order contains the product
LEFT JOIN orders o 
ON oi.order_id = o.order_id

-- Join customers to identify the customer who placed the order
-- The region filter is applied inside the join condition
-- so only North America customers are considered here
LEFT JOIN customers c 
ON o.customer_id = c.customer_id 
AND c.region = 'North America'

-- If a product was ordered by a North America customer,
-- c.customer_id will contain a value
-- If it was NOT ordered by a North America customer,
-- the join will produce NULL
-- So we keep only rows where customer_id is NULL
WHERE c.customer_id IS NULL;
-- Select product details
-- DISTINCT is used because joins may create duplicate rows for a product
SELECT DISTINCT p.product_id, p.product_name
FROM products p

-- LEFT JOIN keeps all products even if they are not ordered
LEFT JOIN order_items oi 
ON p.product_id = oi.product_id

-- Join orders to know which order contains the product
LEFT JOIN orders o 
ON oi.order_id = o.order_id

-- Join customers to identify the customer who placed the order
-- The region filter is applied inside the join condition
-- so only North America customers are considered here
LEFT JOIN customers c 
ON o.customer_id = c.customer_id 
AND c.region = 'North America'

-- If a product was ordered by a North America customer,
-- c.customer_id will contain a value
-- If it was NOT ordered by a North America customer,
-- the join will produce NULL
-- So we keep only rows where customer_id is NULL
WHERE c.customer_id IS NULL;
