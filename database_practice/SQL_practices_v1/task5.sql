DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;

CREATE TABLE orders(
    order_id SERIAL PRIMARY KEY,
    order_date DATE
);

CREATE TABLE order_items(
    item_id SERIAL PRIMARY KEY,
    order_id INT,
    product_name VARCHAR(100),

    FOREIGN KEY(order_id) REFERENCES orders(order_id)
);

INSERT INTO orders (order_date)
SELECT CURRENT_DATE
FROM generate_series(1,20);


INSERT INTO order_items (order_id, product_name) VALUES
(1,'Laptop'),(1,'Mouse'),
(2,'Laptop'),(2,'Mouse'),
(3,'Laptop'),(3,'Mouse'),
(4,'Laptop'),(4,'Mouse'),
(5,'Laptop'),(5,'Mouse'),
(6,'Laptop'),(6,'Mouse'),
(7,'Laptop'),(7,'Mouse'),
(8,'Laptop'),(8,'Mouse'),
(9,'Laptop'),(9,'Mouse'),
(10,'Laptop'),(10,'Mouse'),
(11,'Laptop'),(11,'Mouse'),
(12,'Laptop'),(12,'Mouse'),

(13,'Laptop'),(13,'Keyboard'),
(14,'Mouse'),(14,'Keyboard'),
(15,'Laptop'),(15,'Tablet'),
(16,'Mouse'),(16,'Tablet'),
(17,'Laptop'),(17,'Mouse'),
(18,'Laptop'),(18,'Mouse'),
(19,'Laptop'),(19,'Mouse'),
(20,'Laptop'),(20,'Mouse');


-- Market basket analysis: Find products frequently bought together
SELECT 
    oi1.product_name AS product1,
    oi2.product_name AS product2,
    COUNT(*) AS times_bought_together,  -- Count how many times this pair was purchased
    
    -- Calculate what percentage of all orders include this product pair
    ROUND(
        COUNT(*) * 100.0 /
        (SELECT COUNT(*) FROM orders),  -- Divide by total order count
        2
    ) AS percentage_of_orders

FROM order_items oi1
JOIN order_items oi2
ON oi1.order_id = oi2.order_id  -- Same order (purchased together)
AND oi1.product_name < oi2.product_name  -- Avoid duplicate pairs (A,B) and (B,A)

GROUP BY oi1.product_name, oi2.product_name

-- Only show product pairs that have been bought together more than 10 times
HAVING COUNT(*) > 10;
