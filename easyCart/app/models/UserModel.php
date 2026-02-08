<?php

/**
 * User Model
 * Handles all user-related operations
 */
class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Verify user login
     */
    public function verifyLogin($email, $password)
    {
        $sql = "SELECT entity_id, name, email, password_hash FROM customer_entity WHERE email = $1";
        $result = $this->db->query($sql, [$email]);
        $user = $this->db->fetch($result);

        if ($user) {
            // Verify hashed password
            if (password_verify($password, $user['password_hash'])) {
                return [
                    'success' => true,
                    'user_id' => $user['entity_id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ];
            }

            // Backward compatibility for plain text passwords
            if ($password === $user['password_hash'] && substr($user['password_hash'], 0, 1) !== '$') {
                // Update to hashed password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $this->updatePassword($user['entity_id'], $hashedPassword);

                return [
                    'success' => true,
                    'user_id' => $user['entity_id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ];
            }
        }

        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    /**
     * Register new user
     */
    public function register($firstName, $lastName, $email, $password)
    {
        // Check if user exists
        $sql = "SELECT entity_id FROM customer_entity WHERE email = $1";
        $result = $this->db->query($sql, [$email]);
        $existing = $this->db->fetch($result);

        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $fullName = $firstName . ' ' . $lastName;

        // Insert user
        $sql = "INSERT INTO customer_entity (email, password_hash, name, created_at) 
                VALUES ($1, $2, $3, NOW()) RETURNING entity_id";
        $result = $this->db->query($sql, [$email, $hashedPassword, $fullName]);
        $user = $this->db->fetch($result);

        if ($user) {
            return [
                'success' => true,
                'user_id' => $user['entity_id'],
                'name' => $fullName,
                'email' => $email
            ];
        }

        return ['success' => false, 'message' => 'Failed to create user'];
    }

    /**
     * Get user by ID
     */
    public function getById($userId)
    {
        $sql = "SELECT entity_id as user_id, name, email, created_at FROM customer_entity WHERE entity_id = $1";
        $result = $this->db->query($sql, [$userId]);
        return $this->db->fetch($result);
    }

    /**
     * Get user by email
     */
    public function getByEmail($email)
    {
        $sql = "SELECT entity_id as user_id, name, email, created_at FROM customer_entity WHERE email = $1";
        $result = $this->db->query($sql, [$email]);
        return $this->db->fetch($result);
    }

    /**
     * Update user password
     */
    private function updatePassword($userId, $hashedPassword)
    {
        $sql = "UPDATE customer_entity SET password_hash = $1 WHERE entity_id = $2";
        return $this->db->query($sql, [$hashedPassword, $userId]);
    }

    /**
     * Login user (set session)
     */
    public function login($userId, $name, $email)
    {
        // Merge guest cart before logging in
        require_once __DIR__ . '/CartModel.php';
        $cartModel = new CartModel();
        $cartModel->mergeGuestCart($userId);

        // Set user session
        Session::set('user', [
            'logged_in' => true,
            'user_id' => $userId,
            'name' => $name,
            'email' => $email
        ]);

        Session::set('session_type', 'user');

        return true;
    }

    /**
     * Logout user
     */
    public function logout()
    {
        Session::set('user', [
            'logged_in' => false,
            'user_id' => null,
            'name' => null,
            'email' => null
        ]);

        // Initialize new guest session
        Session::set('session_type', 'guest');
        Session::set('guest_id', 'guest_' . session_id());
        Session::set('guest_cart', []);

        return true;
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        $user = Session::get('user', ['logged_in' => false]);
        return $user['logged_in'] === true;
    }

    /**
     * Get current user data
     */
    public function getCurrentUser()
    {
        return Session::get('user', [
            'logged_in' => false,
            'user_id' => null,
            'name' => 'Guest',
            'email' => null
        ]);
    }

    /**
     * Require login (redirect if not logged in)
     */
    public function requireLogin($redirectUrl = null)
    {
        if (!$this->isLoggedIn()) {
            $loginUrl = BASE_URL . '/login';
            if ($redirectUrl) {
                $loginUrl .= '?redirect=' . urlencode($redirectUrl);
            }
            Session::setFlash('info', 'Please login to access this page.');
            header('Location: ' . $loginUrl);
            exit;
        }
    }
}
