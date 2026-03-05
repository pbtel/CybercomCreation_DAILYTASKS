DROP TABLE IF EXISTS products;

CREATE TABLE products(
    product_id SERIAL PRIMARY KEY,
    category_id INT,
    product_name VARCHAR(100),
    revenue INT
);

INSERT INTO products (category_id, product_name, revenue) VALUES
(1,'Laptop',5000),
(1,'Mouse',2000),
(1,'Keyboard',2000),
(1,'Monitor',1500),
(1,'Printer',1200),

(2,'Shoes',4000),
(2,'Shirt',3000),
(2,'Jeans',3000),
(2,'Jacket',2500),
(2,'Cap',1000),

(3,'Phone',8000),
(3,'Tablet',6000),
(3,'Smartwatch',6000),
(3,'Earbuds',2000);


-- Find top 3 products per category sorted by revenue
SELECT *
FROM (
    -- Rank products within each category by revenue in descending order
    SELECT 
        product_id,
        category_id,
        product_name,
        revenue,
        -- DENSE_RANK() assigns rank without gaps (handles tied values)
        -- Window function partitions by category to rank independently per category
        DENSE_RANK() OVER (
            PARTITION BY category_id  -- Rank each category separately
            ORDER BY revenue DESC  -- Higher revenue gets lower rank number
        ) AS rank
    FROM products
) ranked_products
-- Filter to keep only top 3 products per category
WHERE rank <= 3;
