<?php
/**
 * User Model
 */

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'role',
        'first_name',
        'last_name',
        'phone',
        'profile_image',
        'is_active',
        'remember_token',
        'token_expiry',
        'last_login'
    ];

    /**
     * Get full name
     */
    public function getFullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get members
     */
    public function members() {
        $this->db->select('members', '*', ['user_id' => $this->id]);
        return $this->db->results();
    }

    /**
     * Get role label
     */
    public function getRoleLabel() {
        $roles = [
            'admin' => 'Administrator',
            'finance' => 'Finance',
            'secretariat' => 'Secretariat',
            'pastor' => 'Pastor',
            'leader' => 'Leader',
            'member' => 'Member'
        ];
        
        return $roles[$this->role] ?? $this->role;
    }

    /**
     * Get active users
     */
    public function getActive($order = []) {
        $this->db->select($this->table, '*', ['is_active' => 1], $order);
        return $this->db->results();
    }

    /**
     * Get users by role
     */
    public function getByRole($role) {
        $this->db->select($this->table, '*', ['role' => $role]);
        return $this->db->results();
    }

    /**
     * Search users
     */
    public function search($term) {
        $this->db->query(
            "SELECT * FROM {$this->table} WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?",
            ["%{$term}%", "%{$term}%", "%{$term}%"]
        );
        return $this->db->results();
    }
}
