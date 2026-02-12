<?php

require_once __DIR__ . '/../core/Core_Model.php';

class Model_User extends Core_Model
{
    protected function _init()
    {
        $this->_resourceName = 'Resource_User';
    }

    /**
     * Authentication methods
     */
    public function isLoggedIn()
    {
        if (!Session::isLoggedIn()) {
            return false;
        }

        // Check if user still exists in DB
        $sessionUser = Session::getUser();
        $userId = $sessionUser['id'] ?? null;

        if ($userId) {
            $db = Database::getInstance();
            $query = (new Query())
                ->select(['entity_id'])
                ->from('customer_entity')
                ->where('entity_id', $userId);

            $result = $db->query((string) $query, $query->getParams());
            $user = $db->fetch($result);

            if (!$user) {
                // User was deleted from DB, force logout
                $this->logout();
                return false;
            }
        }

        return true;
    }

    public function requireLogin($redirect = 'home')
    {
        if (!$this->isLoggedIn()) {
            Session::setFlash('error', 'Please login to access this page');
            header('Location: ' . BASE_URL . '/login?redirect=' . urlencode($redirect));
            exit;
        }
    }

    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $sessionUser = Session::getUser();

        // Return standardized user data (with user_id for compatibility)
        return [
            'user_id' => $sessionUser['id'],
            'id' => $sessionUser['id'],
            'name' => $sessionUser['name'],
            'email' => $sessionUser['email']
        ];
    }

    public function login($userId, $name, $email)
    {
        // 1. Establish User context immediately
        Session::set('session_type', 'user');
        Session::setUser([
            'logged_in' => true,
            'id' => $userId,
            'user_id' => $userId, // For backward compatibility
            'name' => $name,
            'email' => $email
        ]);

        // 2. Perform deep merge of guest data into user account
        require_once __DIR__ . '/Model_Cart.php';
        $cartModel = new Model_Cart();
        $cartModel->mergeGuestCart($userId, $email);

        return true;
    }

    public function logout()
    {
        $user = $this->getCurrentUser();
        $userId = $user['user_id'] ?? null;

        require_once __DIR__ . '/Model_Cart.php';
        $cartModel = new Model_Cart();

        // 1. Deactivate old rows (is_active=false)
        $cartModel->deactivateGuestCart($userId);

        // 2. Clear Session (Removes user/session_type)
        Session::logout();

        // 3. IMMEDIATELY create new guest row (is_active=true)
        $cartModel->getOrCreateDbCart(session_id());

        return true;
    }

    /**
     * User actions
     */
    public function register($firstName, $lastName, $email, $password)
    {
        // Check if email already exists
        $existing = $this->getByEmail($email);
        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        $fullName = $firstName . ' ' . $lastName;
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'email' => $email,
            'password_hash' => $passwordHash,
            'name' => $fullName,
            'created_at' => (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s'),
            'updated_at' => (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format('Y-m-d H:i:s')
        ];

        try {
            $this->setData($data)->save();
            return [
                'success' => true,
                'user_id' => $this->getId(),
                'name' => $fullName,
                'email' => $email
            ];
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    public function verifyLogin($email, $password)
    {
        $userData = $this->getByEmail($email);

        if (!$userData) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        // Check password - field name from DB is password_hash
        $hashedPassword = $userData['password_hash'] ?? null;

        if ($hashedPassword && password_verify($password, $hashedPassword)) {
            return [
                'success' => true,
                'user_id' => $userData['entity_id'],
                'name' => $userData['name'],
                'email' => $userData['email']
            ];
        }

        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    /**
     * Compatibility methods
     */
    public function getByEmail($email)
    {
        $this->load($email, 'email');
        return $this->getData();
    }
}
