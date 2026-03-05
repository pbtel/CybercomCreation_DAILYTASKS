DROP TABLE IF EXISTS order_items CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS customers CASCADE;

CREATE TABLE customers (
    customer_id SERIAL PRIMARY KEY,
    customer_name VARCHAR(100),
    email VARCHAR(100)
);

CREATE TABLE products (
    product_id SERIAL PRIMARY KEY,
    product_name VARCHAR(100),
    price DECIMAL(10,2)
);

CREATE TABLE orders (
    order_id SERIAL PRIMARY KEY,
    customer_id INT,
    order_date DATE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE order_items (
    order_item_id SERIAL PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

INSERT INTO cus
tomers (customer_name, email) VALUES
('pooja','pooja


@gmail.com'),
('riya','riya@gmail.com'),
('jay','jay@gmail.com');

INSERT INTO products (product_name, price) VALUES
('Laptop',80000),
('Phone',30000),
('Headphones',2000);

INSERT INTO orders (customer_id, order_date) VALUES
(1,'2025-01-10'),
(1,'2025-02-15'),
(2,'2025-03-12');

INSERT INTO order_items (order_id, product_id, quantity) VALUES
(1,1,1),
(1,3,2),
(2,2,1),
(3,3,3);


-- Build hierarchical JSON structure of customers with their orders and items
SELECT json_agg(
    json_build_object(
        'customer_id', c.customer_id,
        'customer_name', c.customer_name,
        'email', c.email,
        -- Nested: aggregate all orders for this customer
        'orders', (
            SELECT json_agg(
                json_build_object(
                    'order_id', o.order_id,
                    'order_date', o.order_date,
                    -- Nested: aggregate all items in each order
                    'items', (
                        SELECT json_agg(
                            json_build_object(
                                'product_id', p.product_id,
                                'product_name', p.product_name,
                                'price', p.price,
                                'quantity', oi.quantity
                            )
                        )
                        FROM order_items oi
                        JOIN products p 
                        ON oi.product_id = p.product_id
                        WHERE oi.order_id = o.order_id  -- Items for this order
                    )
                )
            )
            FROM orders o
            WHERE o.customer_id = c.customer_id  -- Orders for this customer
        )
    )
) AS api_response
FROM customers c
-- Only include customers who have placed at least one order
WHERE EXISTS (
    SELECT 1 
    FROM orders o 
    WHERE o.customer_id = c.customer_id
);
