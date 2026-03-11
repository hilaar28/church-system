<?php
/**
 * Auth Controller
 */

class AuthController extends Controller {
    /**
     * Login page
     */
    public function login() {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        
        // Handle POST login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->authenticate();
            return;
        }
        
        $this->view('auth.login');
    }

    /**
     * Handle login
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $email = $this->input('email');
        $password = $this->input('password');
        $remember = $this->input('remember') ? true : false;
        
        // Validate
        if (empty($email) || empty($password)) {
            $this->flash('error', 'Please enter email and password');
            $this->redirect('/login');
        }
        
        // Attempt login
        if ($this->auth->attempt($email, $password, $remember)) {
            $this->flash('success', 'Welcome back!');
            $this->redirect('/dashboard');
        }
        
        $this->flash('error', 'Invalid email or password');
        $this->redirect('/login');
    }

    /**
     * Logout
     */
    public function logout() {
        $this->auth->logout();
        $this->flash('success', 'You have been logged out');
        $this->redirect('/login');
    }

    /**
     * Register page
     */
    public function register() {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        
        $this->view('auth.register');
    }

    /**
     * Handle registration
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }
        
        // Verify CSRF
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/register');
        }
        
        $data = [
            'username' => $this->input('username'),
            'email' => $this->input('email'),
            'password' => $this->input('password'),
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
            'phone' => $this->input('phone'),
            'role' => 'member'
        ];
        
        // Validate
        $validator = new Validator($data);
        $validator->rules([
            'username' => 'required|min:3|max:50|alphanumeric',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50'
        ]);
        
        if (!$validator->validate()) {
            saveOld($data);
            $this->flash('error', implode(', ', $validator->getFlatErrors()));
            $this->redirect('/register');
        }
        
        // Check if email exists
        $db = Database::getInstance();
        $db->select('users', '*', ['email' => $data['email']]);
        if ($db->count() > 0) {
            saveOld($data);
            $this->flash('error', 'Email already registered');
            $this->redirect('/register');
        }
        
        // Register user
        $userId = $this->auth->register($data);
        
        if ($userId) {
            $this->flash('success', 'Registration successful! Please login.');
            $this->redirect('/login');
        }
        
        saveOld($data);
        $this->flash('error', 'Registration failed. Please try again.');
        $this->redirect('/register');
    }

    /**
     * Forgot password page
     */
    public function forgotPassword() {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        
        $this->view('auth.forgot-password');
    }

    /**
     * Handle forgot password
     */
    public function handleForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/forgot-password');
        }
        
        $email = $this->input('email');
        
        if (empty($email)) {
            $this->flash('error', 'Please enter your email');
            $this->redirect('/forgot-password');
        }
        
        // Check if email exists
        $db = Database::getInstance();
        $db->select('users', '*', ['email' => $email]);
        $user = $db->first();
        
        if ($user) {
            // Generate reset token
            $token = generateRandomString(32);
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $db->insert('password_resets', [
                'user_id' => $user['id'],
                'token' => $token,
                'expires_at' => $expires
            ]);
            
            // In production, send email with reset link
            // For now, just show success
            $this->flash('success', 'Password reset link sent to your email');
        } else {
            $this->flash('error', 'Email not found');
        }
        
        $this->redirect('/forgot-password');
    }

    /**
     * Reset password page
     */
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $this->redirect('/forgot-password');
        }
        
        // Verify token
        $db = Database::getInstance();
        $db->select('password_resets', '*', [
            'token' => $token,
            'used' => false
        ]);
        
        $reset = $db->first();
        
        if (!$reset || strtotime($reset['expires_at']) < time()) {
            $this->flash('error', 'Invalid or expired reset token');
            $this->redirect('/forgot-password');
        }
        
        $this->data['token'] = $token;
        $this->data['user_id'] = $reset['user_id'];
        
        $this->view('auth.reset-password');
    }

    /**
     * Handle reset password
     */
    public function handleResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/forgot-password');
        }
        
        $token = $this->input('token');
        $userId = $this->input('user_id');
        $password = $this->input('password');
        $confirmPassword = $this->input('confirm_password');
        
        // Validate
        if (empty($password) || $password !== $confirmPassword) {
            $this->flash('error', 'Passwords do not match');
            $this->redirect('/reset-password?token=' . $token);
        }
        
        // Update password
        $auth = new Auth();
        $db = Database::getInstance();
        
        $db->update('users', [
            'password_hash' => $auth->hashPassword($password)
        ], ['id' => $userId]);
        
        // Mark token as used
        $db->update('password_resets', [
            'used' => true
        ], ['token' => $token]);
        
        $this->flash('success', 'Password reset successful! Please login.');
        $this->redirect('/login');
    }
}
