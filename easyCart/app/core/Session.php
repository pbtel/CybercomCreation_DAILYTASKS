<?php

/**
 * Session Management Class
 * Handles all session operations
 */
class Session
{

    /**
     * Start session if not already started
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set session variable
     */
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get session variable
     */
    public static function get($key, $default = null)
    {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Check if session variable exists
     */
    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session variable
     */
    public static function remove($key)
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy session
     */
    public static function destroy()
    {
        self::start();
        session_destroy();
    }

    /**
     * Set flash message
     */
    public static function setFlash($type, $message)
    {
        self::set('flash_message', [
            'type' => $type,
            'message' => $message
        ]);
    }

    /**
     * Get flash message
     */
    public static function getFlash()
    {
        $flash = self::get('flash_message');
        self::remove('flash_message');
        return $flash;
    }

    /**
     * Check if flash message exists
     */
    public static function hasFlash()
    {
        return self::has('flash_message');
    }

    /**
     * Get user data
     */
    public static function getUser()
    {
        return self::get('user', [
            'logged_in' => false,
            'id' => null,
            'name' => 'Guest',
            'email' => null
        ]);
    }

    /**
     * Set user data
     */
    public static function setUser($userData)
    {
        self::set('user', $userData);
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn()
    {
        $user = self::getUser();
        return $user['logged_in'] === true;
    }

    /**
     * Logout user
     */
    public static function logout()
    {
        self::remove('user');
    }
    /**
     * Get cart count
     */
    public static function getCartCount()
    {
        require_once __DIR__ . '/../models/Model_Cart.php';
        $cartModel = new Model_Cart();
        return $cartModel->getCount();
    }
    /**
     * Clear all application data (cart, checkout, etc.)
     * Used during logout to reset guest state
     */
    public static function clearAppData()
    {
        self::remove('guest_cart');
        self::remove('user_carts');
        self::remove('applied_coupon');
        self::remove('checkout_data');
        self::remove('selected_shipping_method');
        self::remove('pending_checkout_data');
        self::remove('session_type');
    }
}
