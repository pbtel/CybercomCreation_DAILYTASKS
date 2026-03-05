DROP TABLE IF EXISTS system1_inventory;
DROP TABLE IF EXISTS system2_inventory;

CREATE TABLE system1_inventory (
    product_id INT PRIMARY KEY,
    product_name VARCHAR(100),
    stock INT
);

CREATE TABLE system2_inventory (
    product_id INT PRIMARY KEY,
    product_name VARCHAR(100),
    stock INT
);


INSERT INTO system1_inventory VALUES
(1,'Laptop',50),
(2,'Phone',100),
(3,'Headphones',75),
(4,'Keyboard',40);

INSERT INTO system2_inventory VALUES
(1,'Laptop',50),
(2,'Phone',90),
(3,'Headphones',75),
(5,'Mouse',60);

-- Reconcile inventory data between two separate systems
SELECT 
    -- Use COALESCE to get product_id from either system (handle mismatches)
    COALESCE(s1.product_id, s2.product_id) AS product_id,
    COALESCE(s1.product_name, s2.product_name) AS product_name,
    
    -- Show stock levels from both systems for comparison
    s1.stock AS system1_stock,
    s2.stock AS system2_stock,

    -- Determine reconciliation status
    CASE
        WHEN s1.product_id IS NULL THEN 'Missing in System1'  -- Exists only in System2
        WHEN s2.product_id IS NULL THEN 'Missing in System2'  -- Exists only in System1
        WHEN s1.stock <> s2.stock THEN 'Stock Mismatch'  -- Both have it but different quantities
        ELSE 'Match'  -- Both systems have same stock
    END AS status

FROM system1_inventory s1
-- FULL OUTER JOIN includes all products from both systems
FULL OUTER JOIN system2_inventory s2
ON s1.product_id = s2.product_id  -- Match products by ID

ORDER BY product_id;
