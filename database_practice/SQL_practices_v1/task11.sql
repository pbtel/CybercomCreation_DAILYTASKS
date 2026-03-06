DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS customers;

CREATE TABLE customers (
    customer_id SERIAL PRIMARY KEY,
    customer_name VARCHAR(100)
);

CREATE TABLE orders (
    order_id SERIAL PRIMARY KEY,
    customer_id INT,
    order_date DATE,
    amount DECIMAL(10,2),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

INSERT INTO customers (customer_name) VALUES
('Arya'),
('Nischay'),
('Rahi');

INSERT INTO orders (customer_id, order_date, amount) VALUES
(1,'2025-01-01',500),
(1,'2025-01-05',300),
(1,'2025-01-10',700),
(1,'2025-02-01',200),
(1,'2025-02-10',900),
(1,'2025-03-01',400),

(2,'2025-01-03',600),
(2,'2025-01-08',250),
(2,'2025-02-14',800),
(2,'2025-03-20',350),

(3,'2025-01-02',150),
(3,'2025-01-15',450),
(3,'2025-02-25',550);

-- Select customer and order details
SELECT 
    c.customer_id,          -- Customer ID from customers table
    c.customer_name,        -- Customer name

    o.order_id,             -- Order ID from orders table
    o.order_date,           -- Date when order was placed
    o.amount,               -- Order amount

    -- Assign row numbers for each customer's orders
    ROW_NUMBER() OVER (
        PARTITION BY c.customer_id     -- Restart numbering for each customer
        ORDER BY o.order_date DESC     -- Most recent order gets row number 1
    ) AS row_num

-- Main table: customers
FROM customers c

-- LATERAL join allows the subquery to use columns from the outer query (c.customer_id)
JOIN LATERAL (

    -- Subquery to fetch orders of the current customer
    SELECT *
    FROM orders o

    -- Match orders belonging to the current customer
    WHERE o.customer_id = c.customer_id

    -- Sort orders by latest date first
    ORDER BY o.order_date DESC

    -- Limit to only the 5 most recent orders
    LIMIT 5

) o ON TRUE   -- ON TRUE means always join the returned rows

-- Final sorting of output
ORDER BY 
    c.customer_id,  -- Group results by customer
    row_num;        -- Show orders in ranked order
