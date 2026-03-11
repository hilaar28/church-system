<?php
/**
 * Dashboard Controller
 */

class DashboardController extends Controller {
    /**
     * Dashboard index
     */
    public function index() {
        $user = $this->user();
        
        // Get statistics
        $member = $this->model('Member');
        $stats = $member->getStatistics();
        
        $this->data['stats'] = $stats;
        $this->data['user'] = $user;
        
        // Get recent members
        $this->db->query("SELECT * FROM members ORDER BY created_at DESC LIMIT 5");
        $this->data['recent_members'] = $this->db->results();
        
        // Get upcoming events
        $this->db->query("SELECT * FROM events WHERE start_datetime >= NOW() AND is_active = 1 ORDER BY start_datetime ASC LIMIT 5");
        $this->data['upcoming_events'] = $this->db->results();
        
        // Get recent announcements
        $this->db->query("SELECT * FROM announcements WHERE is_published = 1 AND (valid_until IS NULL OR valid_until >= NOW()) ORDER BY created_at DESC LIMIT 3");
        $this->data['announcements'] = $this->db->results();
        
        // Get attendance for last 4 weeks
        $this->db->query("
            SELECT s.service_date, s.service_name, COUNT(a.id) as attendance_count 
            FROM services s 
            LEFT JOIN attendance a ON s.id = a.service_id AND a.status = 'present'
            WHERE s.service_date >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
            GROUP BY s.id
            ORDER BY s.service_date DESC
        ");
        $this->data['attendance_trend'] = $this->db->results();
        
        $this->view('dashboard.index');
    }
}
