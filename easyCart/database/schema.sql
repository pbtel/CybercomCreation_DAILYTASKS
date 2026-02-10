-- PostgreSQL Database Schema for EasyCart
-- Optimized & Merged Schema (Base + Enhancements)
-- Phase 10 - Schema Optimization

-- Drop existing tables and objects if they exist (for clean migration)
DROP TRIGGER IF EXISTS update_customer_entity_updated_at ON customer_entity;
DROP TRIGGER IF EXISTS update_catalog_brand_entity_updated_at ON catalog_brand_entity;
DROP TRIGGER IF EXISTS update_catalog_brand_attribute_updated_at ON catalog_brand_attribute;
DROP TRIGGER IF EXISTS update_catalog_category_entity_updated_at ON catalog_category_entity;
DROP TRIGGER IF EXISTS update_catalog_category_attribute_updated_at ON catalog_category_attribute;
DROP TRIGGER IF EXISTS update_catalog_product_entity_updated_at ON catalog_product_entity;
DROP TRIGGER IF EXISTS update_catalog_product_attribute_updated_at ON catalog_product_attribute;
DROP TRIGGER IF EXISTS update_catalog_product_image_updated_at ON catalog_product_image;
DROP TRIGGER IF EXISTS update_catalog_category_products_updated_at ON catalog_category_products;
DROP TRIGGER IF EXISTS update_sales_cart_updated_at ON sales_cart;
DROP TRIGGER IF EXISTS update_sales_cart_product_updated_at ON sales_cart_product;
DROP TRIGGER IF EXISTS update_sales_order_updated_at ON sales_order;
DROP TRIGGER IF EXISTS update_sales_order_product_updated_at ON sales_order_product;
DROP TRIGGER IF EXISTS update_sales_order_address_updated_at ON sales_order_address;
DROP TRIGGER IF EXISTS update_sales_order_billing_updated_at ON sales_order_billing;
DROP TRIGGER IF EXISTS update_sales_order_shipping_method_updated_at ON sales_order_shipping_method;
DROP TRIGGER IF EXISTS update_customer_address_updated_at ON customer_address;

DROP FUNCTION IF EXISTS update_updated_at_column();

DROP TABLE IF EXISTS sales_order_shipping_method CASCADE;
DROP TABLE IF EXISTS sales_order_billing CASCADE;
DROP TABLE IF EXISTS sales_order_address CASCADE;
DROP TABLE IF EXISTS sales_order_product CASCADE;
DROP TABLE IF EXISTS sales_order CASCADE;
DROP TABLE IF EXISTS sales_cart_product CASCADE;
DROP TABLE IF EXISTS sales_cart CASCADE;
DROP TABLE IF EXISTS catalog_category_products CASCADE;
DROP TABLE IF EXISTS catalog_category_attribute CASCADE;
DROP TABLE IF EXISTS catalog_category_entity CASCADE;
DROP TABLE IF EXISTS catalog_product_image CASCADE;
DROP TABLE IF EXISTS catalog_product_attribute CASCADE;
DROP TABLE IF EXISTS catalog_product_entity CASCADE;
DROP TABLE IF EXISTS catalog_brand_attribute CASCADE;
DROP TABLE IF EXISTS catalog_brand_entity CASCADE;
DROP TABLE IF EXISTS customer_entity CASCADE;

-- ============================================
-- UTILITY FUNCTIONS
-- ============================================

-- Function to automatically update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- ============================================
-- CUSTOMER TABLES
-- ============================================

CREATE TABLE customer_entity (
    entity_id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_customer_email ON customer_entity(email);

-- ============================================
-- CATALOG TABLES - BRANDS
-- ============================================

CREATE TABLE catalog_brand_entity (
    entity_id SERIAL PRIMARY KEY,
    brand_slug VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    logo VARCHAR(50),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_brand_slug ON catalog_brand_entity(brand_slug);

CREATE TABLE catalog_brand_attribute (
    attribute_id SERIAL PRIMARY KEY,
    brand_id INTEGER NOT NULL REFERENCES catalog_brand_entity(entity_id) ON DELETE CASCADE,
    attribute_type VARCHAR(100) NOT NULL,
    attribute_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_brand_attr_brand ON catalog_brand_attribute(brand_id);
CREATE INDEX idx_brand_attr_type ON catalog_brand_attribute(attribute_type);

-- ============================================
-- CATALOG TABLES - CATEGORIES
-- ============================================

CREATE TABLE catalog_category_entity (
    entity_id SERIAL PRIMARY KEY,
    category_slug VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_category_slug ON catalog_category_entity(category_slug);
CREATE INDEX idx_category_active ON catalog_category_entity(is_active);

CREATE TABLE catalog_category_attribute (
    attribute_id SERIAL PRIMARY KEY,
    category_id INTEGER NOT NULL REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    attribute_type VARCHAR(100) NOT NULL,
    attribute_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_category_attr_category ON catalog_category_attribute(category_id);

-- ============================================
-- CATALOG TABLES - PRODUCTS
-- ============================================

CREATE TABLE catalog_product_entity (
    entity_id SERIAL PRIMARY KEY,
    url_slug VARCHAR(255),
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    brand_id INTEGER REFERENCES catalog_brand_entity(entity_id) ON DELETE SET NULL,
    price DECIMAL(10, 2) NOT NULL,
    original_price DECIMAL(10, 2),
    discount_percent INTEGER DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0,
    reviews_count INTEGER DEFAULT 0,
    stock INTEGER DEFAULT 0,
    description TEXT,
    shipping_type VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_product_slug ON catalog_product_entity(url_slug);
CREATE INDEX idx_product_sku ON catalog_product_entity(sku);
CREATE INDEX idx_product_brand ON catalog_product_entity(brand_id);
CREATE INDEX idx_product_active ON catalog_product_entity(is_active);

CREATE TABLE catalog_product_attribute (
    attribute_id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    attribute_type VARCHAR(100) NOT NULL,
    attribute_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_product_attr_product ON catalog_product_attribute(product_id);
CREATE INDEX idx_product_attr_type ON catalog_product_attribute(attribute_type);

CREATE TABLE catalog_product_image (
    image_id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    image_emoji VARCHAR(50),
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_product_image_product ON catalog_product_image(product_id);

-- ============================================
-- CATALOG TABLES - CATEGORY-PRODUCT JUNCTION
-- ============================================

CREATE TABLE catalog_category_products (
    id SERIAL PRIMARY KEY,
    category_id INTEGER NOT NULL REFERENCES catalog_category_entity(entity_id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(category_id, product_id)
);

CREATE INDEX idx_cat_prod_category ON catalog_category_products(category_id);
CREATE INDEX idx_cat_prod_product ON catalog_category_products(product_id);

-- ============================================
-- CART TABLES
-- ============================================

CREATE TABLE sales_cart (
    cart_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES customer_entity(entity_id) ON DELETE CASCADE,
    session_id VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    coupon_code VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_cart_user ON sales_cart(user_id);
CREATE INDEX idx_cart_session ON sales_cart(session_id);
CREATE INDEX idx_cart_active ON sales_cart(is_active);

CREATE TABLE sales_cart_product (
    id SERIAL PRIMARY KEY,
    cart_id INTEGER NOT NULL REFERENCES sales_cart(cart_id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES catalog_product_entity(entity_id) ON DELETE CASCADE,
    quantity INTEGER NOT NULL DEFAULT 1,
    variant_data JSONB,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_cart_product_cart ON sales_cart_product(cart_id);
CREATE INDEX idx_cart_product_product ON sales_cart_product(product_id);

-- ============================================
-- ORDER TABLES
-- ============================================

CREATE TABLE sales_order (
    order_id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES customer_entity(entity_id) ON DELETE SET NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_cost DECIMAL(10, 2) DEFAULT 0,
    tax DECIMAL(10, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    final_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    customer_email VARCHAR(255),
    customer_phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_order_user ON sales_order(user_id);
CREATE INDEX idx_order_number ON sales_order(order_number);
CREATE INDEX idx_order_status ON sales_order(status);
CREATE INDEX idx_order_email ON sales_order(customer_email);
CREATE INDEX idx_order_phone ON sales_order(customer_phone);

CREATE TABLE sales_order_product (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    product_id INTEGER REFERENCES catalog_product_entity(entity_id) ON DELETE SET NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INTEGER NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    variant_data JSONB,
    subtotal DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_order_product_order ON sales_order_product(order_id);

CREATE TABLE sales_order_address (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    pincode VARCHAR(20) NOT NULL,
    country VARCHAR(100) DEFAULT 'India',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_order_address_order ON sales_order_address(order_id);

CREATE TABLE sales_order_billing (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(255),
    payment_status VARCHAR(50) DEFAULT 'pending',
    coupon_code VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_order_billing_order ON sales_order_billing(order_id);

CREATE TABLE sales_order_shipping_method (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES sales_order(order_id) ON DELETE CASCADE,
    shipping_method VARCHAR(100) NOT NULL,
    shipping_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_order_shipping_order ON sales_order_shipping_method(order_id);

-- ============================================
-- AUTO-UPDATE TRIGGERS
-- ============================================

CREATE TRIGGER update_customer_entity_updated_at BEFORE UPDATE ON customer_entity FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_catalog_brand_entity_updated_at BEFORE UPDATE ON catalog_brand_entity FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_catalog_brand_attribute_updated_at BEFORE UPDATE ON catalog_brand_attribute FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_catalog_category_entity_updated_at BEFORE UPDATE ON catalog_category_entity FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_catalog_category_attribute_updated_at BEFORE UPDATE ON catalog_category_attribute FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_catalog_product_entity_updated_at BEFORE UPDATE ON catalog_product_entity FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_catalog_product_attribute_updated_at BEFORE UPDATE ON catalog_product_attribute FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_catalog_product_image_updated_at BEFORE UPDATE ON catalog_product_image FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_catalog_category_products_updated_at BEFORE UPDATE ON catalog_category_products FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_sales_cart_updated_at BEFORE UPDATE ON sales_cart FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_sales_cart_product_updated_at BEFORE UPDATE ON sales_cart_product FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_sales_order_updated_at BEFORE UPDATE ON sales_order FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_sales_order_product_updated_at BEFORE UPDATE ON sales_order_product FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_sales_order_address_updated_at BEFORE UPDATE ON sales_order_address FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_sales_order_billing_updated_at BEFORE UPDATE ON sales_order_billing FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_sales_order_shipping_method_updated_at BEFORE UPDATE ON sales_order_shipping_method FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
