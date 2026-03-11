<?php
/**
 * Member Model
 */

class Member extends Model {
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'profile_image',
        'marital_status',
        'wedding_date',
        'membership_date',
        'membership_status',
        'membership_type',
        'spiritual_birthdate',
        'baptized',
        'baptized_date',
        'notes',
        'emergency_contact_name',
        'emergency_contact_phone'
    ];

    /**
     * Get full name
     */
    public function getFullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get user
     */
    public function user() {
        if ($this->user_id) {
            $user = new User();
            return $user->find($this->user_id);
        }
        return null;
    }

    /**
     * Get family
     */
    public function family() {
        $this->db->query(
            "SELECT f.* FROM families f 
            JOIN family_members fm ON f.id = fm.family_id 
            WHERE fm.member_id = ?",
            [$this->id]
        );
        return $this->db->first();
    }

    /**
     * Get groups
     */
    public function groups() {
        $this->db->query(
            "SELECT g.*, gm.role as member_role FROM groups g 
            JOIN group_members gm ON g.id = gm.group_id 
            WHERE gm.member_id = ? AND g.is_active = 1",
            [$this->id]
        );
        return $this->db->results();
    }

    /**
     * Get attendance
     */
    public function attendance($limit = 10) {
        $this->db->query(
            "SELECT a.*, s.service_name, s.service_date, s.service_type 
            FROM attendance a 
            JOIN services s ON a.service_id = s.id 
            WHERE a.member_id = ? 
            ORDER BY s.service_date DESC 
            LIMIT ?",
            [$this->id, $limit]
        );
        return $this->db->results();
    }

    /**
     * Get donations
     */
    public function donations($limit = 10) {
        $this->db->query(
            "SELECT * FROM donations 
            WHERE member_id = ? 
            ORDER BY donation_date DESC 
            LIMIT ?",
            [$this->id, $limit]
        );
        return $this->db->results();
    }

    /**
     * Get total donations
     */
    public function getTotalDonations() {
        $this->db->query(
            "SELECT COALESCE(SUM(amount), 0) as total FROM donations WHERE member_id = ?",
            [$this->id]
        );
        return $this->db->first()['total'] ?? 0;
    }

    /**
     * Get membership status label
     */
    public function getStatusLabel() {
        $statuses = [
            'visitor' => 'Visitor',
            'member' => 'Member',
            'inactive' => 'Inactive',
            'transferred' => 'Transferred'
        ];
        
        return $statuses[$this->membership_status] ?? $this->membership_status;
    }

    /**
     * Get members by status
     */
    public function getByStatus($status) {
        $this->db->select($this->table, '*', ['membership_status' => $status], ['last_name', 'ASC']);
        return $this->db->results();
    }

    /**
     * Search members
     */
    public function search($term) {
        $this->db->query(
            "SELECT * FROM {$this->table} 
            WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?
            ORDER BY last_name, first_name",
            ["%{$term}%", "%{$term}%", "%{$term}%", "%{$term}%"]
        );
        return $this->db->results();
    }

    /**
     * Get members statistics
     */
    public function getStatistics() {
        $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN membership_status = 'member' THEN 1 ELSE 0 END) as members,
            SUM(CASE WHEN membership_status = 'visitor' THEN 1 ELSE 0 END) as visitors,
            SUM(CASE WHEN membership_status = 'inactive' THEN 1 ELSE 0 END) as inactive,
            SUM(CASE WHEN baptized = 1 THEN 1 ELSE 0 END) as baptized
            FROM {$this->table}");
        
        return $this->db->first();
    }
}
