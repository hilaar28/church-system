<?php
/**
 * Authentication Library
 */

class Auth {
    private $db;
    private $sessionKey = 'user_id';
    private $rememberMeKey = 'remember_token';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Attempt to login user
     */
    public function attempt($email, $password, $remember = false) {
        $user = $this->findUserByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        if (!$this->verifyPassword($password, $user['password_hash'])) {
            return false;
        }
        
        if (!$user['is_active']) {
            return false;
        }
        
        // Login successful
        $this->login($user, $remember);
        
        // Update last login
        $this->updateLastLogin($user['id']);
        
        return true;
    }

    /**
     * Login user
     */
    public function login($user, $remember = false) {
        $_SESSION[$this->sessionKey] = $user['id'];
        
        if ($remember) {
            $this->createRememberToken($user['id']);
        }
        
        return true;
    }

    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION[$this->sessionKey])) {
            unset($_SESSION[$this->sessionKey]);
        }
        
        if (isset($_COOKIE[$this->rememberMeKey])) {
            $this->deleteRememberToken($_COOKIE[$this->rememberMeKey]);
            setcookie($this->rememberMeKey, '', time() - 3600, '/');
        }
        
        session_destroy();
        return true;
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        if (isset($_SESSION[$this->sessionKey])) {
            return true;
        }
        
        // Check remember me cookie
        if (isset($_COOKIE[$this->rememberMeKey])) {
            return $this->loginWithRememberToken($_COOKIE[$this->rememberMeKey]);
        }
        
        return false;
    }

    /**
     * Get current user
     */
    public function user() {
        if (!isset($_SESSION[$this->sessionKey])) {
            return null;
        }
        
        return $this->findUserById($_SESSION[$this->sessionKey]);
    }

    /**
     * Get current user ID
     */
    public function id() {
        return isset($_SESSION[$this->sessionKey]) ? $_SESSION[$this->sessionKey] : null;
    }

    /**
     * Check user role
     */
    public function hasRole($role) {
        $user = $this->user();
        
        if (!$user) {
            return false;
        }
        
        $roles = ['admin' => 3, 'leader' => 2, 'member' => 1];
        $userRole = $roles[$user['role']] ?? 0;
        $requiredRole = $roles[$role] ?? 0;
        
        return $userRole >= $requiredRole;
    }

    /**
     * Check permission
     */
    public function hasPermission($permission) {
        $user = $this->user();
        
        if (!$user) {
            return false;
        }
        
        // Admin has all permissions
        if ($user['role'] === 'admin') {
            return true;
        }
        
        // Define permissions by role
        $permissions = [
            'admin' => ['*'],
            'leader' => ['members.view', 'members.edit', 'groups.manage', 'attendance.record', 'volunteers.manage', 'reports.view'],
            'member' => ['profile.view', 'profile.edit', 'attendance.self']
        ];
        
        $userPermissions = $permissions[$user['role']] ?? [];
        
        return in_array($permission, $userPermissions) || in_array('*', $userPermissions);
    }

    /**
     * Find user by email
     */
    private function findUserByEmail($email) {
        $this->db->select('users', '*', ['email' => $email]);
        return $this->db->first();
    }

    /**
     * Find user by ID
     */
    private function findUserById($id) {
        $this->db->select('users', '*', ['id' => $id]);
        return $this->db->first();
    }

    /**
     * Verify password
     */
    private function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Hash password
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Create remember token
     */
    private function createRememberToken($userId) {
        $token = bin2hex(random_bytes(32));
        
        // Store in database
        $this->db->insert('users', [
            'remember_token' => $token,
            'token_expiry' => date('Y-m-d H:i:s', time() + REMEMBER_ME_EXPIRY)
        ]);
        
        // Update user with token
        $this->db->update('users', [
            'remember_token' => $token,
            'token_expiry' => date('Y-m-d H:i:s', time() + REMEMBER_ME_EXPIRY)
        ], ['id' => $userId]);
        
        // Set cookie
        setcookie($this->rememberMeKey, $token, time() + REMEMBER_ME_EXPIRY, '/', '', false, true);
    }

    /**
     * Delete remember token
     */
    private function deleteRememberToken($token) {
        $this->db->query("UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE remember_token = ?", [$token]);
    }

    /**
     * Login with remember token
     */
    private function loginWithRememberToken($token) {
        $this->db->query("SELECT * FROM users WHERE remember_token = ? AND token_expiry > NOW()", [$token]);
        $user = $this->db->first();
        
        if ($user) {
            $_SESSION[$this->sessionKey] = $user['id'];
            return true;
        }
        
        return false;
    }

    /**
     * Update last login
     */
    private function updateLastLogin($userId) {
        $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], ['id' => $userId]);
    }

    /**
     * Register new user
     */
    public function register($data) {
        $data['password_hash'] = $this->hashPassword($data['password']);
        $data['role'] = $data['role'] ?? 'member';
        $data['is_active'] = 1;
        
        unset($data['password']);
        
        if ($this->db->insert('users', $data)) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
}
