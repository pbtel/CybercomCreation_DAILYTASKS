-- EasyCart Database Schema - Phase 6
-- PostgreSQL Database Schema

-- Drop existing tables if they exist (for clean setup)
DROP TABLE IF EXISTS sales_order_meta CASCADE;
DROP TABLE IF EXISTS sales_order_address CASCADE;
DROP TABLE IF EXISTS sales_order_product CASCADE;
DROP TABLE IF EXISTS sales_order CASCADE;
DROP TABLE IF EXISTS sales_cart_meta CASCADE;
DROP TABLE IF EXISTS sales_cart_address CASCADE;
DROP TABLE IF EXISTS sales_cart_product CASCADE;
DROP TABLE IF EXISTS sales_cart CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS product_images CASCADE;
DROP TABLE IF EXISTS catalog_category_products CASCADE;
DROP TABLE IF EXISTS catalog_brand_attribute CASCADE;
DROP TABLE IF EXISTS catalog_brand_entity CASCADE;
DROP TABLE IF EXISTS catalog_category_attribute CASCADE;
DROP TABLE IF EXISTS catalog_category_entity CASCADE;
DROP TABLE IF EXISTS catalog_product_attribute CASCADE;
DROP TABLE IF EXISTS catalog_product_entity CASCADE;

-- ============================================
-- CATALOG TABLES - Products
-- ============================================

-- Core product entity table
CREATE TABLE catalog_product_entity (
    product_id SERIAL PRIMARY KEY,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product attributes table
CREATE TABLE catalog_product_attribute (
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

-- Category entity table
CREATE TABLE catalog_category_entity (
    entity_id SERIAL PRIMARY KEY,
    category_slug VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Category attributes table
CREATE TABLE catalog_category_attribute (
    attribute_id SERIAL PRIMARY KEY,
    entity_id INTEGER NOT NULL REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Category-Product mapping table
CREATE TABLE catalog_category_products (
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

-- Brand entity table
CREATE TABLE catalog_brand_entity (
    brand_id SERIAL PRIMARY KEY,
    brand_slug VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Brand attributes table
CREATE TABLE catalog_brand_attribute (
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

CREATE TABLE product_images (
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

CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- CART TABLES
-- ============================================

-- Main cart table
CREATE TABLE sales_cart (
    cart_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id) ON DELETE SET NULL,
    session_id VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart products table
CREATE TABLE sales_cart_product (
    cart_product_id SERIAL PRIMARY KEY,
    cart_id INTEGER NOT NULL REFERENCES sales_cart(cart_id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(product_id) ON DELETE CASCADE,
    quantity INTEGER NOT NULL DEFAULT 1,
    variant_data JSONB,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart address table
CREATE TABLE sales_cart_address (
    address_id SERIAL PRIMARY KEY,
    cart_id INTEGER NOT NULL REFERENCES sales_cart(cart_id) ON DELETE CASCADE,
    full_name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    address_line1 VARCHAR(500),
    address_line2 VARCHAR(500),
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart metadata table (for coupon, shipping method, etc.)
CREATE TABLE sales_cart_meta (
    meta_id SERIAL PRIMARY KEY,
    cart_id INTEGER NOT NULL REFERENCES sales_cart(cart_id) ON DELETE CASCADE,
    meta_key VARCHAR(100) NOT NULL,
    meta_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(cart_id, meta_key)
);

-- ============================================
-- ORDER TABLES
-- ============================================

-- Main order table
CREATE TABLE sales_order (
    order_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(user_id) ON DELETE SET NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_type VARCHAR(50),
    shipping_cost DECIMAL(10, 2) DEFAULT 0,
    tax DECIMAL(10, 2) DEFAULT 0,
    discount DECIMAL(10, 2) DEFAULT 0,
    final_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order products table
CREATE TABLE sales_order_product (
    order_product_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(product_id) ON DELETE CASCADE,
    product_name VARCHAR(255) NOT NULL,
    quantity INTEGER NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    variant_data JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order address table
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

-- Order metadata table
CREATE TABLE sales_order_meta (
    meta_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    meta_key VARCHAR(100) NOT NULL,
    meta_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(order_id, meta_key)
);

-- ============================================
-- INDEXES for Performance
-- ============================================

-- Product indexes
CREATE INDEX idx_product_sku ON catalog_product_entity(sku);
CREATE INDEX idx_product_attr_product_id ON catalog_product_attribute(product_id);
CREATE INDEX idx_product_attr_category ON catalog_product_attribute(category_id);
CREATE INDEX idx_product_attr_brand ON catalog_product_attribute(brand_id);

-- Category indexes
CREATE INDEX idx_category_slug ON catalog_category_entity(category_slug);
CREATE INDEX idx_category_products_category ON catalog_category_products(category_id);
CREATE INDEX idx_category_products_product ON catalog_category_products(product_id);

-- Brand indexes
CREATE INDEX idx_brand_slug ON catalog_brand_entity(brand_slug);

-- User indexes
CREATE INDEX idx_user_email ON users(email);

-- Cart indexes
CREATE INDEX idx_cart_user ON sales_cart(user_id);
CREATE INDEX idx_cart_session ON sales_cart(session_id);
CREATE INDEX idx_cart_active ON sales_cart(is_active);
CREATE INDEX idx_cart_product_cart ON sales_cart_product(cart_id);

-- Order indexes
CREATE INDEX idx_order_user ON sales_order(user_id);
CREATE INDEX idx_order_number ON sales_order(order_number);
CREATE INDEX idx_order_status ON sales_order(status);
CREATE INDEX idx_order_product_order ON sales_order_product(order_id);

-- ============================================
-- COMMENTS
-- ============================================

COMMENT ON TABLE catalog_product_entity IS 'Core product information';
COMMENT ON TABLE catalog_product_attribute IS 'Product attributes and details';
COMMENT ON TABLE catalog_category_entity IS 'Category entities';
COMMENT ON TABLE catalog_category_attribute IS 'Category attributes';
COMMENT ON TABLE catalog_category_products IS 'Category-Product mapping';
COMMENT ON TABLE catalog_brand_entity IS 'Brand entities';
COMMENT ON TABLE catalog_brand_attribute IS 'Brand attributes';
COMMENT ON TABLE sales_cart IS 'Shopping cart sessions';
COMMENT ON TABLE sales_cart_product IS 'Cart items';
COMMENT ON TABLE sales_order IS 'Customer orders';
COMMENT ON TABLE sales_order_product IS 'Order items';
