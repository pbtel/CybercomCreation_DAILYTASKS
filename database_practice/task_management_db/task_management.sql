
-- REMOVE EXISTING TABLES
-- =========================================
DROP TABLE IF EXISTS task_items CASCADE;
DROP TABLE IF EXISTS project_master CASCADE;
DROP TABLE IF EXISTS app_users CASCADE;

-- =========================================
-- 1. USER TABLE
-- =========================================
CREATE TABLE app_users (
    user_id SERIAL PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    user_email VARCHAR(160) UNIQUE NOT NULL,
    user_password VARCHAR(255) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================
-- 2. PROJECT TABLE
-- =========================================
CREATE TABLE project_master (
    project_id SERIAL PRIMARY KEY,
    project_title VARCHAR(180) NOT NULL,
    project_details TEXT,
    owner_id INTEGER NOT NULL,
    created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_owner_project
        FOREIGN KEY (owner_id)
        REFERENCES app_users(user_id)
        ON DELETE CASCADE
);

-- =========================================
-- 3. TASK TABLE
-- =========================================
CREATE TABLE task_items (
    task_id SERIAL PRIMARY KEY,
    project_ref INTEGER NOT NULL,
    task_name VARCHAR(220) NOT NULL,
    task_info TEXT,
    task_status VARCHAR(25) DEFAULT 'pending'
        CHECK (task_status IN ('pending','ongoing','completed')),
    deadline DATE,
    assigned_user INTEGER,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_project_task
        FOREIGN KEY (project_ref)
        REFERENCES project_master(project_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_user_task
        FOREIGN KEY (assigned_user)
        REFERENCES app_users(user_id)
        ON DELETE SET NULL
);

-- =========================================
-- SAMPLE USERS
-- =========================================
INSERT INTO app_users (full_name, user_email, user_password) VALUES
('Rohan Sharma', 'rohan.sharma@mail.com', 'pass123'),
('Priya Desai', 'priya.desai@mail.com', 'secure456'),
('Arjun Mehta', 'arjun.mehta@mail.com', 'mypassword');

-- =========================================
-- SAMPLE PROJECTS
-- =========================================
INSERT INTO project_master (project_title, project_details, owner_id) VALUES
('Inventory Management System', 'Develop backend and UI for inventory tracking', 1),
('E-Commerce Website', 'Complete full stack development project', 2);

-- =========================================
-- SAMPLE TASKS
-- =========================================
INSERT INTO task_items 
(project_ref, task_name, task_info, task_status, deadline, assigned_user) 
VALUES
(1, 'Create ER Diagram', 'Design database structure for inventory', 'ongoing', '2026-04-02', 1),
(1, 'Build Admin Panel', 'Develop admin dashboard with CRUD features', 'pending', '2026-04-08', 3),
(1, 'Testing Module', 'Perform integration testing', 'pending', '2026-04-15', 2),

(2, 'Homepage Design', 'Design responsive homepage layout', 'completed', '2026-03-20', 2),
(2, 'Payment Gateway Integration', 'Integrate Razorpay/Stripe API', 'ongoing', '2026-04-10', 3);
