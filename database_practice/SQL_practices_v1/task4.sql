DROP TABLE IF EXISTS price_history;
DROP TABLE IF EXISTS products;

CREATE TABLE products(
    product_id SERIAL PRIMARY KEY,
    product_name VARCHAR(100)
);

CREATE TABLE price_history(
    price_id SERIAL PRIMARY KEY,
    product_id INT,
    price DECIMAL(10,2),
    change_date DATE,
    
    FOREIGN KEY(product_id) REFERENCES products(product_id)
);


INSERT INTO products (product_name) VALUES
('Laptop'),
('Phone'),
('Tablet');

INSERT INTO price_history (product_id, price, change_date) VALUES
(1, 50000, CURRENT_DATE - INTERVAL '120 days'),
(1, 52000, CURRENT_DATE - INTERVAL '60 days'),
(1, 54000, CURRENT_DATE - INTERVAL '10 days'),

(2, 30000, CURRENT_DATE - INTERVAL '80 days'),
(2, 28000, CURRENT_DATE - INTERVAL '30 days'),

(3, 20000, CURRENT_DATE - INTERVAL '70 days'),
(3, 21000, CURRENT_DATE - INTERVAL '20 days');


-- Track price changes over time for each product
SELECT 
    p.product_name,
    ph.price AS current_price,
    -- Get the previous price using LAG window function (ordered by date)
    LAG(ph.price) OVER (
        PARTITION BY ph.product_id  -- Separate sequences per product
        ORDER BY ph.change_date
    ) AS previous_price,
    -- Get the next price using LEAD window function (ordered by date)
    LEAD(ph.price) OVER (
        PARTITION BY ph.product_id  -- Separate sequences per product
        ORDER BY ph.change_date
    ) AS next_price,
    -- Calculate percentage change from previous price
    ROUND(
        ((ph.price - LAG(ph.price) OVER (
            PARTITION BY ph.product_id
            ORDER BY ph.change_date
        )) 
        / LAG(ph.price) OVER (
            PARTITION BY ph.product_id
            ORDER BY ph.change_date
        )) * 100, 2  -- Convert to percentage and round to 2 decimals
    ) AS percentage_change

FROM price_history ph
JOIN products p
ON ph.product_id = p.product_id

-- Focus on recent price changes (last 90 days)
WHERE ph.change_date >= CURRENT_DATE - INTERVAL '90 days';
