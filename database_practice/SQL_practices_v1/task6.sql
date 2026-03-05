-- Categories
CREATE TABLE categories (
    category_id SERIAL PRIMARY KEY,
    category_name VARCHAR(100)
);

-- Products
CREATE TABLE products (
    product_id SERIAL PRIMARY KEY,
    product_name VARCHAR(100),
    category_id INT REFERENCES categories(category_id),
    price DECIMAL(10,2)
);

-- Orders
CREATE TABLE orders (
    order_id SERIAL PRIMARY KEY,
    order_date DATE,
    status VARCHAR(20) -- completed, pending, cancelled
);

-- Order Items
CREATE TABLE order_items (
    order_item_id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(order_id),
    product_id INT REFERENCES products(product_id),
    quantity INT,
    price DECIMAL(10,2)
);


-- Categories
INSERT INTO categories (category_name) VALUES
('Electronics'),
('Clothing');

-- Products
INSERT INTO products (product_name, category_id, price) VALUES
('Laptop', 1, 60000),
('Mobile', 1, 20000),
('T-Shirt', 2, 500),
('Jeans', 2, 1500);

-- Orders
INSERT INTO orders (order_date, status) VALUES
('2025-01-10', 'completed'),
('2025-01-15', 'pending'),
('2025-02-01', 'cancelled'),
('2025-04-10', 'completed');

-- Order Items
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1,1,1,60000),
(1,3,2,500),
(2,2,1,20000),
(3,4,1,1500),
(4,2,1,20000),
(4,3,1,500);


-- Analyze sales performance by category and quarter, broken down by order status
SELECT 
    c.category_name,
    EXTRACT(QUARTER FROM o.order_date) AS quarter,  -- Extract quarter (1-4) from order date

    -- Count and sum revenue for completed orders
    COUNT(CASE WHEN o.status='completed' THEN o.order_id END) AS completed_orders,
    SUM(CASE WHEN o.status='completed' THEN oi.price*oi.quantity END) AS completed_amount,

    -- Count and sum revenue for pending orders
    COUNT(CASE WHEN o.status='pending' THEN o.order_id END) AS pending_orders,
    SUM(CASE WHEN o.status='pending' THEN oi.price*oi.quantity END) AS pending_amount,

    -- Count and sum revenue for cancelled orders
    COUNT(CASE WHEN o.status='cancelled' THEN o.order_id END) AS cancelled_orders,
    SUM(CASE WHEN o.status='cancelled' THEN oi.price*oi.quantity END) AS cancelled_amount

FROM orders o
JOIN order_items oi ON o.order_id = oi.order_id  -- Get order details
JOIN products p ON oi.product_id = p.product_id  -- Get product info
JOIN categories c ON p.category_id = c.category_id  -- Group by category

-- Group results by category and quarter for aggregated analysis
GROUP BY c.category_name, quarter
ORDER BY c.category_name, quarter;
