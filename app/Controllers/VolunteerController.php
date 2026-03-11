<?php
/**
 * Volunteer Controller
 */

class VolunteerController extends Controller {
    /**
     * Volunteer opportunities list
     */
    public function index() {
        $page = (int) ($this->input('page') ?? 1);
        
        // Get total
        $this->db->query("SELECT COUNT(*) as total FROM volunteer_opportunities WHERE is_active = 1");
        $total = $this->db->first()->total;
        
        // Get opportunities
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $this->db->query(
            "SELECT vo.*, 
            (SELECT COUNT(*) FROM volunteer_assignments WHERE opportunity_id = vo.id) as assigned
            FROM volunteer_opportunities vo
            WHERE vo.is_active = 1
            ORDER BY vo.start_datetime ASC
            LIMIT " . ITEMS_PER_PAGE . " OFFSET {$offset}"
        );
        
        $this->data['opportunities'] = $this->db->results();
        $this->data['pagination'] = paginate($total, ITEMS_PER_PAGE, $page);
        
        $this->view('volunteers.index');
    }

    /**
     * Opportunities page
     */
    public function opportunities() {
        $this->index();
    }

    /**
     * Add opportunity
     */
    public function addOpportunity() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/volunteers');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/volunteers');
        }
        
        $data = [
            'title' => $this->input('title'),
            'description' => $this->input('description'),
            'event_id' => $this->input('event_id') ?: null,
            'service_id' => $this->input('service_id') ?: null,
            'start_datetime' => $this->input('start_datetime'),
            'end_datetime' => $this->input('end_datetime'),
            'location' => $this->input('location'),
            'slots_available' => $this->input('slots_available') ?: null,
            'is_active' => 1
        ];
        
        // Validate
        $validator = new Validator($data);
        $validator->rules([
            'title' => 'required|min:3|max:200',
            'start_datetime' => 'required'
        ]);
        
        if (!$validator->validate()) {
            $this->flash('error', implode(', ', $validator->getFlatErrors()));
            $this->redirect('/volunteers');
        }
        
        $this->db->query(
            "INSERT INTO volunteer_opportunities (title, description, event_id, service_id, 
            start_datetime, end_datetime, location, slots_available, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array_values($data)
        );
        
        $this->flash('success', 'Opportunity added successfully');
        $this->redirect('/volunteers');
    }

    /**
     * Assign volunteer
     */
    public function assign() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/volunteers');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/volunteers');
        }
        
        $opportunityId = (int) ($this->input('opportunity_id') ?? 0);
        $memberId = (int) ($this->input('member_id') ?? 0);
        $role = $this->input('role', 'Volunteer');
        
        if (!$opportunityId || !$memberId) {
            $this->flash('error', 'Invalid opportunity or member');
            $this->redirect('/volunteers');
        }
        
        // Check if already assigned
        $this->db->query(
            "SELECT * FROM volunteer_assignments WHERE opportunity_id = ? AND member_id = ?",
            [$opportunityId, $memberId]
        );
        if ($this->db->first()) {
            $this->flash('error', 'Already assigned to this opportunity');
            $this->redirect('/volunteers');
        }
        
        // Check slots
        $this->db->query("SELECT slots_available, slots_filled FROM volunteer_opportunities WHERE id = ?", [$opportunityId]);
        $opportunity = $this->db->first();
        
        if ($opportunity['slots_available'] && $opportunity['slots_filled'] >= $opportunity['slots_available']) {
            $this->flash('error', 'No slots available');
            $this->redirect('/volunteers');
        }
        
        $this->db->query(
            "INSERT INTO volunteer_assignments (opportunity_id, member_id, role, assigned_by, status) 
            VALUES (?, ?, ?, ?, 'assigned')",
            [$opportunityId, $memberId, $role, $this->user()->id]
        );
        
        // Update slots filled
        $this->db->query(
            "UPDATE volunteer_opportunities SET slots_filled = slots_filled + 1 WHERE id = ?",
            [$opportunityId]
        );
        
        $this->flash('success', 'Volunteer assigned successfully');
        $this->redirect('/volunteers');
    }

    /**
     * View opportunity details
     */
    public function viewOpportunity() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/volunteers');
        }
        
        $this->db->query(
            "SELECT vo.*, 
            (SELECT COUNT(*) FROM volunteer_assignments WHERE opportunity_id = vo.id) as assigned
            FROM volunteer_opportunities vo
            WHERE vo.id = ?",
            [$id]
        );
        $opportunity = $this->db->first();
        
        if (!$opportunity) {
            $this->flash('error', 'Opportunity not found');
            $this->redirect('/volunteers');
        }
        
        $this->data['opportunity'] = $opportunity;
        
        // Get assignments
        $this->db->query(
            "SELECT va.*, m.first_name, m.last_name, m.phone
            FROM volunteer_assignments va
            JOIN members m ON va.member_id = m.id
            WHERE va.opportunity_id = ?
            ORDER BY va.assigned_at DESC",
            [$id]
        );
        $this->data['assignments'] = $this->db->results();
        
        $this->view('volunteers.view');
    }

    /**
     * Update assignment status
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/volunteers');
        }
        
        $assignmentId = (int) ($this->input('assignment_id') ?? 0);
        $status = $this->input('status');
        
        if (!$assignmentId || !$status) {
            $this->flash('error', 'Invalid request');
            $this->redirect('/volunteers');
        }
        
        $this->db->query(
            "UPDATE volunteer_assignments SET status = ? WHERE id = ?",
            [$status, $assignmentId]
        );
        
        $this->flash('success', 'Status updated successfully');
        $this->redirect('/volunteers');
    }

    /**
     * Delete opportunity
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/volunteers');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->flash('error', 'Invalid opportunity ID');
            $this->redirect('/volunteers');
        }
        
        $this->db->query("UPDATE volunteer_opportunities SET is_active = 0 WHERE id = ?", [$id]);
        
        $this->flash('success', 'Opportunity deleted successfully');
        $this->redirect('/volunteers');
    }
}
