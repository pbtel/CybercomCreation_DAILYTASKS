<?php

/**
 * Auth Controller
 * Handles user authentication (login, signup, logout)
 */
class AuthController extends Controller
{

    /**
     * Display login page
     */
    public function login()
    {
        // Redirect if already logged in
        $userModel = $this->model('UserModel');
        if ($userModel->isLoggedIn()) {
            $this->redirect('home');
            return;
        }

        // Pass data to view
        $data = [
            'pageTitle' => 'Login',
            'redirect' => $this->get('redirect', 'home')
        ];

        $this->view('auth/login', $data);
    }

    /**
     * Process login
     */
    public function loginProcess()
    {
        if (!$this->isPost()) {
            $this->redirect('login');
            return;
        }

        // Get form data
        $email = $this->post('email');
        $password = $this->post('password');
        $redirect = $this->post('redirect', 'home');

        // Validate
        if (!$email || !$password) {
            Session::setFlash('error', 'Please enter email and password');
            $this->redirect('login');
            return;
        }

        // Verify login
        $userModel = $this->model('UserModel');
        $result = $userModel->verifyLogin($email, $password);

        if ($result['success']) {
            // Login user
            $userModel->login($result['user_id'], $result['name'], $result['email']);

            // Merge guest cart
            $cartModel = $this->model('CartModel');
            $cartModel->mergeGuestCart($result['user_id']);

            Session::setFlash('success', 'Welcome back, ' . $result['name'] . '!');
            $this->redirect($redirect);
        } else {
            Session::setFlash('error', $result['message']);
            // Preserve redirect param
            $redirectUrl = 'login';
            if ($redirect && $redirect !== 'home') {
                $redirectUrl .= '?redirect=' . urlencode($redirect);
            }
            $this->redirect($redirectUrl);
        }
    }

    /**
     * Display signup page
     */
    public function signup()
    {
        // Redirect if already logged in
        $userModel = $this->model('UserModel');
        if ($userModel->isLoggedIn()) {
            $this->redirect('home');
            return;
        }

        // Pass data to view
        $data = [
            'pageTitle' => 'Sign Up',
            'redirect' => $this->get('redirect', 'home')
        ];

        $this->view('auth/signup', $data);
    }

    /**
     * Process signup
     */
    public function signupProcess()
    {
        if (!$this->isPost()) {
            $this->redirect('signup');
            return;
        }

        $redirect = $this->post('redirect', 'home');

        // Get form data
        $firstName = $this->post('first_name');
        $lastName = $this->post('last_name');
        $email = $this->post('email');
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm_password');

        // Validate
        if (!$firstName || !$lastName || !$email || !$password) {
            Session::setFlash('error', 'Please fill all required fields');
            $this->redirect('signup');
            return;
        }

        if ($password !== $confirmPassword) {
            Session::setFlash('error', 'Passwords do not match');
            $this->redirect('signup');
            return;
        }

        if (strlen($password) < 6) {
            Session::setFlash('error', 'Password must be at least 6 characters');
            $this->redirect('signup');
            return;
        }

        // Register user
        $userModel = $this->model('UserModel');
        $result = $userModel->register($firstName, $lastName, $email, $password);

        if ($result['success']) {
            // Auto-login after registration
            $userModel->login($result['user_id'], $result['name'], $result['email']);

            // Merge guest cart
            $cartModel = $this->model('CartModel');
            $cartModel->mergeGuestCart($result['user_id']);

            Session::setFlash('success', 'Account created successfully! Welcome, ' . $result['name'] . '!');
            $this->redirect($redirect);
        } else {
            Session::setFlash('error', $result['message']);
            // Preserve redirect param
            $redirectUrl = 'signup';
            if ($redirect && $redirect !== 'home') {
                $redirectUrl .= '?redirect=' . urlencode($redirect);
            }
            $this->redirect($redirectUrl);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $userModel = $this->model('UserModel');
        $userModel->logout();

        Session::setFlash('success', 'You have been logged out');
        $this->redirect('home');
    }
}
