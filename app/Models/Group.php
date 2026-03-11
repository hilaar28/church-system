<?php
/**
 * Group Model
 */

class Group extends Model {
    protected $table = 'groups';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'group_type',
        'meeting_day',
        'meeting_time',
        'meeting_location',
        'leader_id',
        'capacity',
        'is_active'
    ];

    /**
     * Get leader
     */
    public function leader() {
        if ($this->leader_id) {
            $member = new Member();
            return $member->find($this->leader_id);
        }
        return null;
    }

    /**
     * Get members
     */
    public function members() {
        $this->db->query(
            "SELECT m.*, gm.role as member_role, gm.joined_at 
            FROM members m 
            JOIN group_members gm ON m.id = gm.member_id 
            WHERE gm.group_id = ?
            ORDER BY gm.role, m.last_name",
            [$this->id]
        );
        // Return as Member model instances
        $member = new Member();
        return $member->hydrateAll($this->db->results());
    }

    /**
     * Add member
     */
    public function addMember($memberId, $role = 'member') {
        return $this->db->insert('group_members', [
            'group_id' => $this->id,
            'member_id' => $memberId,
            'role' => $role
        ]);
    }

    /**
     * Remove member
     */
    public function removeMember($memberId) {
        return $this->db->delete('group_members', [
            'group_id' => $this->id,
            'member_id' => $memberId
        ]);
    }

    /**
     * Get member count
     */
    public function getMemberCount() {
        $this->db->query(
            "SELECT COUNT(*) as count FROM group_members WHERE group_id = ?",
            [$this->id]
        );
        return $this->db->first()['count'] ?? 0;
    }

    /**
     * Check if member is in group
     */
    public function hasMember($memberId) {
        $this->db->query(
            "SELECT COUNT(*) as count FROM group_members WHERE group_id = ? AND member_id = ?",
            [$this->id, $memberId]
        );
        return ($this->db->first()['count'] ?? 0) > 0;
    }

    /**
     * Get attendance
     */
    public function attendance($limit = 10) {
        $this->db->query(
            "SELECT * FROM group_attendance 
            WHERE group_id = ? 
            ORDER BY meeting_date DESC 
            LIMIT ?",
            [$this->id, $limit]
        );
        return $this->db->results();
    }

    /**
     * Get active groups
     */
    public function getActive($order = []) {
        $this->db->select($this->table, '*', ['is_active' => 1], $order);
        return self::hydrateAll($this->db->results());
    }

    /**
     * Get groups by type
     */
    public function getByType($type) {
        $this->db->select($this->table, '*', ['group_type' => $type, 'is_active' => 1], ['name', 'ASC']);
        return self::hydrateAll($this->db->results());
    }
}
