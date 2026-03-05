DROP TABLE IF EXISTS employees;

CREATE TABLE employees (
    emp_id SERIAL PRIMARY KEY,
    emp_name VARCHAR(100) NOT NULL,
    job_title VARCHAR(100),
    manager_id INT,
    
    FOREIGN KEY (manager_id)
    REFERENCES employees(emp_id)
);


INSERT INTO employees (emp_id, emp_name, job_title, manager_id) VALUES
(1,'Devendra','CEO',NULL),
(2,'Mahesh','CTO',1),
(3,'Rahul','CFO',1),
(4,'Tina','Engineering Manager',2),
(5,'Mahendra','Developer',4),
(6,'Vamika','Developer',4),
(7,'Rutu','Finance Manager',3);


-- Build organizational hierarchy using recursive CTE
WITH RECURSIVE hierarchy AS (
    -- Base case: Start with root employees (those with no manager)
    SELECT 
        emp_id,
        emp_name,
        job_title,
        manager_id,
        0 AS level,  -- Root level is 0
        emp_name::TEXT AS path  -- Initialize path with employee name
    FROM employees
    WHERE manager_id IS NULL

    UNION ALL

    -- Recursive case: Find all subordinates of current employees and increment level
    SELECT 
        e.emp_id,
        e.emp_name,
        e.job_title,
        e.manager_id,
        h.level + 1,  -- Increment level for each hierarchy depth
        h.path || ' -> ' || e.emp_name  -- Append subordinate to path chain
    FROM employees e
    JOIN hierarchy h
    ON e.manager_id = h.emp_id  -- Match employees with their managers
)

-- Display the complete organizational hierarchy with reporting paths
SELECT emp_id, emp_name, job_title, level, path
FROM hierarchy
ORDER BY level;
