-- Seed Admin User for Phase 10
-- Password is 'admin123' (will be automatically hashed on first login)

INSERT INTO customer_entity (email, password_hash, name, created_at)
VALUES ('admin@easycart.com', 'admin123', 'Admin User', NOW())
ON CONFLICT (email) DO NOTHING;
