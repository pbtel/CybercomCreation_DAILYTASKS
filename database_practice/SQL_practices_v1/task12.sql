DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS regions;

CREATE TABLE categories (
    category_id SERIAL PRIMARY KEY,
    category_name VARCHAR(100)
);

CREATE TABLE regions (
    region_id SERIAL PRIMARY KEY,
    region_name VARCHAR(100)
);

CREATE TABLE sales (
    sale_id SERIAL PRIMARY KEY,
    category_id INT,
    region_id INT,
    amount DECIMAL(10,2),
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (region_id) REFERENCES regions(region_id)
);

INSERT INTO categories (category_name) VALUES
('Electronics'),
('Clothing'),
('Food');

INSERT INTO regions (region_name) VALUES
('North'),
('South'),
('West');

INSERT INTO sales (category_id, region_id, amount) VALUES
(1,1,500),
(1,2,300),
(1,3,200),

(2,1,400),
(2,2,350),
(2,3,250),

(3,1,600),
(3,2,450),
(3,3,300);

SELECT 

    -- Handle NULL values for subtotal and total rows
    COALESCE(c.category_name, 'ALL_CATEGORIES') AS category,
    COALESCE(r.region_name, 'ALL_REGIONS') AS region,

    SUM(s.amount) AS total_sales

FROM sales s
JOIN categories c 
ON s.category_id = c.category_id

JOIN regions r 
ON s.region_id = r.region_id

GROUP BY GROUPING SETS
(
    (c.category_name, r.region_name),  -- sales by category and region
    (c.category_name),                 -- subtotal by category
    (r.region_name),                   -- subtotal by region
    ()                                 -- grand total
)

ORDER BY category, region;
