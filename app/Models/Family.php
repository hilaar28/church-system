<?php
/**
 * Family Model
 */

class Family extends Model {
    protected $table = 'families';
    protected $primaryKey = 'id';
    protected $fillable = [
        'family_name',
        'address',
        'city',
        'state',
        'postal_code',
        'phone',
        'head_of_family_id'
    ];

    /**
     * Get members
     */
    public function members() {
        $this->db->query(
            "SELECT m.*, fm.relationship FROM members m 
            JOIN family_members fm ON m.id = fm.member_id 
            WHERE fm.family_id = ?
            ORDER BY fm.relationship",
            [$this->id]
        );
        // Return as Member model instances
        $member = new Member();
        return $member->hydrateAll($this->db->results());
    }

    /**
     * Get head of family
     */
    public function headOfFamily() {
        if ($this->head_of_family_id) {
            $member = new Member();
            return $member->find($this->head_of_family_id);
        }
        return null;
    }

    /**
     * Add member to family
     */
    public function addMember($memberId, $relationship = 'other') {
        return $this->db->insert('family_members', [
            'family_id' => $this->id,
            'member_id' => $memberId,
            'relationship' => $relationship
        ]);
    }

    /**
     * Remove member from family
     */
    public function removeMember($memberId) {
        return $this->db->delete('family_members', [
            'family_id' => $this->id,
            'member_id' => $memberId
        ]);
    }

    /**
     * Get member count
     */
    public function getMemberCount() {
        $this->db->query(
            "SELECT COUNT(*) as count FROM family_members WHERE family_id = ?",
            [$this->id]
        );
        return $this->db->first()['count'] ?? 0;
    }
}
