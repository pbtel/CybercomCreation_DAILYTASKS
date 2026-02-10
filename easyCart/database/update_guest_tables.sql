-- Address table for guest carts
CREATE TABLE IF NOT EXISTS sales_cart_address (
    address_id SERIAL PRIMARY KEY,
    cart_id INT NOT NULL,
    full_name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),
    address_line1 TEXT,
    address_line2 TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(20),
    country VARCHAR(100) DEFAULT 'India',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES sales_cart(cart_id) ON DELETE CASCADE
);

-- Payment info for guest carts
CREATE TABLE IF NOT EXISTS sales_cart_billing (
    billing_id SERIAL PRIMARY KEY,
    cart_id INT NOT NULL,
    payment_method VARCHAR(50),
    payment_status VARCHAR(20) DEFAULT 'pending',
    coupon_code VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES sales_cart(cart_id) ON DELETE CASCADE
);

-- Shipping method for guest carts
CREATE TABLE IF NOT EXISTS sales_cart_shipping_method (
    shipping_id SERIAL PRIMARY KEY,
    cart_id INT NOT NULL,
    shipping_method VARCHAR(50),
    shipping_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES sales_cart(cart_id) ON DELETE CASCADE
);
