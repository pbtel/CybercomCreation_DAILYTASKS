DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS customers;

CREATE TABLE customers(
    customer_id SERIAL PRIMARY KEY,
    customer_name VARCHAR(100)
);

CREATE TABLE orders(
    order_id SERIAL PRIMARY KEY,
    customer_id INT,
    order_date DATE,
    
    FOREIGN KEY(customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE order_items(
    item_id SERIAL PRIMARY KEY,
    order_id INT,
    product_name VARCHAR(100),
    price INT,
    quantity INT,
    
    FOREIGN KEY(order_id) REFERENCES orders(order_id)
);



INSERT INTO customers (customer_name) VALUES
('Alice'),
('Bob'),
('Carol'),
('David');

INSERT INTO orders (customer_id, order_date) VALUES
(1, CURRENT_DATE - INTERVAL '5 days'),
(1, CURRENT_DATE - INTERVAL '10 days'),
(2, CURRENT_DATE - INTERVAL '3 days'),
(3, CURRENT_DATE - INTERVAL '20 days'),
(4, CURRENT_DATE - INTERVAL '15 days');

INSERT INTO order_items (order_id, product_name, price, quantity) VALUES
(1,'Laptop',5000,1),
(1,'Mouse',500,2),

(2,'Keyboard',1500,1),

(3,'Phone',8000,1),

(4,'Tablet',4000,1),

(5,'Headphones',2000,2);


-- Analyze customer spending to identify high-value customers
WITH customer_spending AS (
    -- Calculate total spending for each customer from orders in last 30 days
    SELECT 
        c.customer_id,
        c.customer_name,
        COUNT(DISTINCT o.order_id) AS purchase_count,  -- Count unique orders
        SUM(oi.price * oi.quantity) AS total_spent  -- Calculate total spent per customer
    FROM customers c
    JOIN orders o 
        ON c.customer_id = o.customer_id
    JOIN order_items oi 
        ON o.order_id = oi.order_id
    WHERE o.order_date >= CURRENT_DATE - INTERVAL '30 days'  -- Recent orders only
    GROUP BY c.customer_id, c.customer_name
),

-- Calculate average spending across all customers
avg_spending AS (
    SELECT AVG(total_spent) AS avg_spent
    FROM customer_spending
)

-- Return customers whose spending exceeds the average
SELECT 
    cs.customer_id,
    cs.customer_name,
    cs.purchase_count,
    cs.total_spent,
    cs.total_spent - a.avg_spent AS amount_above_average  -- Show how much above average
FROM customer_spending cs
CROSS JOIN avg_spending a  -- Join average to all customer rows
WHERE cs.total_spent > a.avg_spent;  -- Filter only above-average spenders
