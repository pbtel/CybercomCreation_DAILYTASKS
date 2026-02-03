-- EasyCart Database Schema - Phase 6
-- PostgreSQL Database Schema

-- ============================================
-- DROP EXISTING TABLES
-- ============================================

-- Drop active cart related tables
DROP TABLE IF EXISTS sales_cart_meta CASCADE;
DROP TABLE IF EXISTS sales_cart_address CASCADE;
DROP TABLE IF EXISTS sales_cart_product CASCADE;
DROP TABLE IF EXISTS sales_cart CASCADE;
DROP TABLE IF EXISTS sales_order_address CASCADE;
DROP TABLE IF EXISTS sales_order CASCADE;

-- Drop new order layout tables (if any)
DROP TABLE IF EXISTS sales_order_billing CASCADE;
DROP TABLE IF EXISTS sales_order_shipping_method CASCADE;
DROP TABLE IF EXISTS sales_order_products CASCADE;

-- Note: We generally don't drop users or catalog tables if they have data
-- DROP TABLE IF EXISTS users CASCADE;
-- DROP TABLE IF EXISTS product_images CASCADE;
-- DROP TABLE IF EXISTS catalog_category_products CASCADE;
-- DROP TABLE IF EXISTS catalog_brand_attribute CASCADE;
-- DROP TABLE IF EXISTS catalog_brand_entity CASCADE;
-- DROP TABLE IF EXISTS catalog_category_attribute CASCADE;
-- DROP TABLE IF EXISTS catalog_category_entity CASCADE;
-- DROP TABLE IF EXISTS catalog_product_attribute CASCADE;
-- DROP TABLE IF EXISTS catalog_product_entity CASCADE;

-- ============================================
-- CATALOG TABLES - Products
-- ============================================

CREATE TABLE IF NOT EXISTS catalog_product_entity (
    product_id SERIAL PRIMARY KEY,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS catalog_product_attribute (
    attribute_id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(product_id) ON DELETE CASCADE,
    price DECIMAL(10, 2) NOT NULL,
    original_price DECIMAL(10, 2),
    discount_percent INTEGER DEFAULT 0,
    shipping_type VARCHAR(50) DEFAULT 'Express',
    rating DECIMAL(3, 2) DEFAULT 0,
    reviews_count INTEGER DEFAULT 0,
    stock INTEGER DEFAULT 0,
    image VARCHAR(255),
    description TEXT,
    specs JSONB,
    variants JSONB,
    featured BOOLEAN DEFAULT FALSE,
    tags JSONB,
    category_id VARCHAR(100),
    brand_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- CATALOG TABLES - Categories
-- ============================================

CREATE TABLE IF NOT EXISTS catalog_category_entity (
    entity_id SERIAL PRIMARY KEY,
    category_slug VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS catalog_category_attribute (
    attribute_id SERIAL PRIMARY KEY,
    entity_id INTEGER NOT NULL REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS catalog_category_products (
    increment_id SERIAL PRIMARY KEY,
    category_id INTEGER NOT NULL REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(product_id) ON DELETE CASCADE,
    position INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(category_id, product_id)
);

-- ============================================
-- CATALOG TABLES - Brands
-- ============================================

CREATE TABLE IF NOT EXISTS catalog_brand_entity (
    brand_id SERIAL PRIMARY KEY,
    brand_slug VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS catalog_brand_attribute (
    attribute_id SERIAL PRIMARY KEY,
    brand_id INTEGER NOT NULL REFERENCES catalog_brand_entity(brand_id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    logo VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- PRODUCT IMAGES TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS product_images (
    image_id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(product_id) ON DELETE CASCADE,
    image_url VARCHAR(500) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    position INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- USER TABLES
-- ============================================

CREATE TABLE IF NOT EXISTS users (
    user_id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- CART TABLES (Guest Only)
-- ============================================

CREATE TABLE sales_cart (
    cart_id SERIAL PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sales_cart_product (
    cart_product_id SERIAL PRIMARY KEY,
    cart_id INTEGER NOT NULL REFERENCES sales_cart(cart_id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(product_id) ON DELETE CASCADE,
    quantity INTEGER NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- ORDER TABLES (Restructured)
-- ============================================

-- 1. Main order table
CREATE TABLE sales_order (
    order_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id) ON DELETE SET NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    discount DECIMAL(10, 2) DEFAULT 0,
    tax DECIMAL(10, 2) DEFAULT 0,
    final_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Order products table
CREATE TABLE sales_order_products (
    order_product_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(product_id) ON DELETE CASCADE,
    product_name VARCHAR(255) NOT NULL,
    quantity INTEGER NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    variant_data JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Order address table
CREATE TABLE sales_order_address (
    address_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    full_name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    address_line1 VARCHAR(500),
    address_line2 VARCHAR(500),
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Order shipping method table
CREATE TABLE sales_order_shipping_method (
    shipping_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    method_name VARCHAR(100) NOT NULL,
    shipping_cost DECIMAL(10, 2) DEFAULT 0,
    estimated_delivery VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Order billing table
CREATE TABLE sales_order_billing (
    billing_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    payment_method VARCHAR(100),
    transaction_id VARCHAR(255),
    billing_address_id INTEGER REFERENCES sales_order_address(address_id),
    billing_status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- INDEXES
-- ============================================

CREATE INDEX IF NOT EXISTS idx_cart_session ON sales_cart(session_id);
CREATE INDEX IF NOT EXISTS idx_cart_active ON sales_cart(is_active);
CREATE INDEX IF NOT EXISTS idx_order_user ON sales_order(user_id);
CREATE INDEX IF NOT EXISTS idx_order_number ON sales_order(order_number);
