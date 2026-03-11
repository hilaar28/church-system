<?php
/**
 * Settings Controller
 */

class SettingsController extends Controller {
    /**
     * Settings index
     */
    public function index() {
        // Get all settings
        $this->db->query("SELECT * FROM settings ORDER BY setting_key");
        $settings = $this->db->results();
        
        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting['setting_key']] = $setting['setting_value'];
        }
        
        $this->data['settings'] = $settingsArray;
        
        $this->view('settings.index');
    }

    /**
     * Users management
     */
    public function users() {
        $page = (int) ($this->input('page') ?? 1);
        
        // Get total
        $this->db->query("SELECT COUNT(*) as total FROM users");
        $total = $this->db->first()->total;
        
        // Get users
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $this->db->query(
            "SELECT * FROM users ORDER BY created_at DESC LIMIT " . ITEMS_PER_PAGE . " OFFSET {$offset}"
        );
        
        $this->data['users'] = $this->db->results();
        $this->data['pagination'] = paginate($total, ITEMS_PER_PAGE, $page);
        
        $this->view('settings.users');
    }

    /**
     * Save settings
     */
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/settings');
        }
        
        $settings = [
            'site_name' => $this->input('site_name'),
            'currency_symbol' => $this->input('currency_symbol'),
            'currency_code' => $this->input('currency_code'),
            'date_format' => $this->input('date_format'),
            'church_address' => $this->input('church_address'),
            'church_phone' => $this->input('church_phone'),
            'church_email' => $this->input('church_email')
        ];
        
        foreach ($settings as $key => $value) {
            $this->db->query(
                "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = ?",
                [$key, $value, $value]
            );
        }
        
        $this->flash('success', 'Settings saved successfully');
        $this->redirect('/settings');
    }

    /**
     * Add user
     */
    public function addUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings/users');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/settings/users');
        }
        
        $data = [
            'username' => $this->input('username'),
            'email' => $this->input('email'),
            'password' => $this->input('password'),
            'role' => $this->input('role') ?: 'member',
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
            'phone' => $this->input('phone')
        ];
        
        // Validate
        $validator = new Validator($data);
        $validator->rules([
            'username' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required',
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2'
        ]);
        
        if (!$validator->validate()) {
            $this->flash('error', implode(', ', $validator->getFlatErrors()));
            $this->redirect('/settings/users');
        }
        
        // Check if username or email exists
        $this->db->query("SELECT id FROM users WHERE username = ? OR email = ?", [$data['username'], $data['email']]);
        if ($this->db->first()) {
            $this->flash('error', 'Username or email already exists');
            $this->redirect('/settings/users');
        }
        
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $this->db->query(
            "INSERT INTO users (username, email, password_hash, role, first_name, last_name, phone, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)",
            [$data['username'], $data['email'], $passwordHash, $data['role'], $data['first_name'], $data['last_name'], $data['phone']]
        );
        
        $this->flash('success', 'User created successfully');
        $this->redirect('/settings/users');
    }

    /**
     * Edit user
     */
    public function editUser() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/settings/users');
        }
        
        $this->db->query("SELECT * FROM users WHERE id = ?", [$id]);
        $user = $this->db->first();
        
        if (!$user) {
            $this->flash('error', 'User not found');
            $this->redirect('/settings/users');
        }
        
        $this->data['user'] = $user;
        
        $this->view('settings.user-form');
    }

    /**
     * Update user
     */
    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings/users');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/settings/users');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/settings/users');
        }
        
        $firstName = $this->input('first_name');
        $lastName = $this->input('last_name');
        $email = $this->input('email');
        $role = $this->input('role');
        $phone = $this->input('phone');
        $isActive = $this->input('is_active') ? 1 : 0;
        $password = $this->input('password');
        
        // Check email exists
        $this->db->query("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $id]);
        if ($this->db->first()) {
            $this->flash('error', 'Email already exists');
            $this->redirect('/settings/users');
        }
        
        if ($password) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $this->db->query(
                "UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, phone = ?, is_active = ?, password_hash = ? WHERE id = ?",
                [$firstName, $lastName, $email, $role, $phone, $isActive, $passwordHash, $id]
            );
        } else {
            $this->db->query(
                "UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, phone = ?, is_active = ? WHERE id = ?",
                [$firstName, $lastName, $email, $role, $phone, $isActive, $id]
            );
        }
        
        $this->flash('success', 'User updated successfully');
        $this->redirect('/settings/users');
    }

    /**
     * Delete user
     */
    public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/settings/users');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->flash('error', 'Invalid user ID');
            $this->redirect('/settings/users');
        }
        
        // Prevent deleting own account
        if ($id == $this->user()->id) {
            $this->flash('error', 'You cannot delete your own account');
            $this->redirect('/settings/users');
        }
        
        $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
        
        $this->flash('success', 'User deleted successfully');
        $this->redirect('/settings/users');
    }

    /**
     * Activity log
     */
    public function activity() {
        $page = (int) ($this->input('page') ?? 1);
        
        $this->db->query("SELECT COUNT(*) as total FROM activity_log");
        $total = $this->db->first()->total;
        
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $this->db->query(
            "SELECT al.*, u.username, u.first_name, u.last_name
            FROM activity_log al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC
            LIMIT " . ITEMS_PER_PAGE . " OFFSET {$offset}"
        );
        
        $this->data['activities'] = $this->db->results();
        $this->data['pagination'] = paginate($total, ITEMS_PER_PAGE, $page);
        
        $this->view('settings.activity');
    }
}
