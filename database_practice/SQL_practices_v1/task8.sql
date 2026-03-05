DROP TABLE IF EXISTS transactions CASCADE;

CREATE TABLE transactions (
    transaction_id SERIAL PRIMARY KEY,
    transaction_date DATE NOT NULL,
    customer_id INT,
    amount DECIMAL(10,2) NOT NULL
);
DROP TABLE IF EXISTS transactions CASCADE;

CREATE TABLE transactions (
    transaction_id SERIAL PRIMARY KEY,
    transaction_date DATE NOT NULL,
    customer_id INT,
    amount DECIMAL(10,2) NOT NULL
);


INSERT INTO transactions (transaction_date, customer_id, amount) VALUES
('2024-01-15',1,500),
('2024-01-20',2,300),
('2024-02-10',1,700),
('2024-02-15',3,400),
('2024-03-12',2,600),
('2024-04-05',1,900),
('2024-05-14',3,200),
('2024-06-18',2,750),
('2024-07-09',1,650),
('2024-08-21',2,800),
('2024-09-11',3,500),
('2024-10-10',1,900),
('2024-11-03',2,400),
('2024-12-22',3,1000),

('2025-01-10',1,600),
('2025-02-15',2,700),
('2025-03-18',3,800),
('2025-04-25',1,950),
('2025-05-30',2,1200),
('2025-06-11',3,650),
('2025-07-04',1,720),
('2025-08-19',2,870),
('2025-09-15',3,430),
('2025-10-07',1,990),
('2025-11-23',2,560),
('2025-12-30',3,1100);


-- Analyze monthly revenue with running totals and year-to-date comparisons
WITH monthly_revenue AS (
    -- Aggregate transactions by month
    SELECT 
        DATE_TRUNC('month', transaction_date) AS month,
        SUM(amount) AS monthly_revenue
    FROM transactions
    WHERE transaction_date >= CURRENT_DATE - INTERVAL '24 months'  -- Last 24 months only
    GROUP BY 1
)

SELECT
    month,
    monthly_revenue,

    -- Running total: cumulative sum from start to current month
    SUM(monthly_revenue) OVER (
        ORDER BY month
    ) AS running_total,

    -- Year-to-date: cumulative sum within each year only
    SUM(monthly_revenue) OVER (
        PARTITION BY EXTRACT(YEAR FROM month)  -- Separate calculation per year
        ORDER BY month
    ) AS ytd_total,

    -- Previous month's revenue for month-over-month comparison
    LAG(monthly_revenue) OVER (
        ORDER BY month
    ) AS previous_month_revenue

FROM monthly_revenue
ORDER BY month;
