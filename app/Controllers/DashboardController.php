<?php
/**
 * Dashboard Controller
 */

class DashboardController extends Controller {
    /**
     * Dashboard index - routes to role-based dashboard
     */
    public function index() {
        $user = $this->user();
        $role = $user['role'] ?? 'member';
        
        // Route to role-specific dashboard
        switch ($role) {
            case 'finance':
                $this->financeDashboard();
                break;
            case 'secretariat':
                $this->secretariatDashboard();
                break;
            case 'pastor':
                $this->pastorDashboard();
                break;
            case 'member':
                $this->memberDashboard();
                break;
            case 'admin':
            case 'leader':
            default:
                $this->adminDashboard();
                break;
        }
    }
    
    /**
     * Finance Dashboard
     */
    private function financeDashboard() {
        $user = $this->user();
        $currency = '$';
        
        // Get financial statistics
        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total FROM donations WHERE YEAR(donation_date) = YEAR(CURDATE())");
        $result = $this->db->first();
        $this->data['total_donations'] = $result ? $result['total'] : 0;
        
        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE YEAR(expense_date) = YEAR(CURDATE())");
        $result = $this->db->first();
        $this->data['total_expenses'] = $result ? $result['total'] : 0;
        
        $this->data['net_balance'] = $this->data['total_donations'] - $this->data['total_expenses'];
        
        // Monthly donations
        $this->db->query("
            SELECT MONTH(donation_date) as month, SUM(amount) as total 
            FROM donations 
            WHERE YEAR(donation_date) = YEAR(CURDATE())
            GROUP BY MONTH(donation_date)
            ORDER BY month
        ");
        $this->data['monthly_donations'] = $this->db->results();
        
        // Monthly expenses
        $this->db->query("
            SELECT MONTH(expense_date) as month, SUM(amount) as total 
            FROM expenses 
            WHERE YEAR(expense_date) = YEAR(CURDATE())
            GROUP BY MONTH(expense_date)
            ORDER BY month
        ");
        $this->data['monthly_expenses'] = $this->db->results();
        
        // Recent donations
        $this->db->query("SELECT d.*, m.first_name, m.last_name FROM donations d LEFT JOIN members m ON d.member_id = m.id ORDER BY d.donation_date DESC LIMIT 10");
        $this->data['recent_donations'] = $this->db->results();
        
        // Recent expenses
        $this->db->query("SELECT e.*, ec.name as category_name FROM expenses e LEFT JOIN expense_categories ec ON e.category_id = ec.id ORDER BY e.expense_date DESC LIMIT 10");
        $this->data['recent_expenses'] = $this->db->results();
        
        // Pending approvals
        $this->db->query("SELECT COUNT(*) as count FROM expenses WHERE is_approved = 0");
        $result = $this->db->first();
        $this->data['pending_approvals'] = $result ? $result['count'] : 0;
        
        $this->data['user'] = $user;
        $this->data['currency'] = $currency;
        
        $this->view('dashboard.finance');
    }
    
    /**
     * Secretariat Dashboard
     */
    private function secretariatDashboard() {
        $user = $this->user();
        
        // Get member statistics
        $member = $this->model('Member');
        $stats = $member->getStatistics();
        $this->data['stats'] = $stats;
        
        // Recent members
        $this->db->query("SELECT * FROM members ORDER BY created_at DESC LIMIT 10");
        $this->data['recent_members'] = $this->db->results();
        
        // Members by status
        $this->db->query("SELECT membership_status, COUNT(*) as count FROM members GROUP BY membership_status");
        $this->data['members_by_status'] = $this->db->results();
        
        // Families
        $this->db->query("SELECT COUNT(*) as total FROM families");
        $result = $this->db->first();
        $this->data['total_families'] = $result ? $result['total'] : 0;
        
        // Groups
        $this->db->query("SELECT COUNT(*) as total FROM groups WHERE is_active = 1");
        $result = $this->db->first();
        $this->data['total_groups'] = $result ? $result['total'] : 0;
        
        // New members this month
        $this->db->query("SELECT COUNT(*) as count FROM members WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $result = $this->db->first();
        $this->data['new_this_month'] = $result ? $result['count'] : 0;
        
        $this->data['user'] = $user;
        
        $this->view('dashboard.secretariat');
    }
    
    /**
     * Pastor Dashboard
     */
    private function pastorDashboard() {
        $user = $this->user();
        
        // Attendance statistics
        $this->db->query("SELECT COUNT(*) as total FROM attendance WHERE MONTH(recorded_at) = MONTH(CURDATE())");
        $result = $this->db->first();
        $this->data['attendance_this_month'] = $result ? $result['total'] : 0;
        
        // This week's services
        $this->db->query("SELECT COUNT(*) as total FROM services WHERE service_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $result = $this->db->first();
        $this->data['services_this_week'] = $result ? $result['total'] : 0;
        
        // Upcoming events
        $this->db->query("SELECT * FROM events WHERE start_datetime >= NOW() AND is_active = 1 ORDER BY start_datetime ASC LIMIT 5");
        $this->data['upcoming_events'] = $this->db->results();
        
        // Recent announcements
        $this->db->query("SELECT * FROM announcements WHERE is_published = 1 ORDER BY created_at DESC LIMIT 5");
        $this->data['announcements'] = $this->db->results();
        
        // Member statistics
        $member = $this->model('Member');
        $stats = $member->getStatistics();
        $this->data['stats'] = $stats;
        
        // Groups overview
        $this->db->query("SELECT g.*, (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count FROM groups g WHERE g.is_active = 1");
        $this->data['active_groups'] = $this->db->results();
        
        // Volunteers
        $this->db->query("SELECT COUNT(*) as total FROM volunteer_assignments WHERE status = 'assigned'");
        $result = $this->db->first();
        $this->data['volunteers_count'] = $result ? $result['total'] : 0;
        
        $this->data['user'] = $user;
        
        $this->view('dashboard.pastor');
    }
    
    /**
     * Member Dashboard
     */
    private function memberDashboard() {
        $user = $this->user();
        
        // Get announcements visible to members
        $this->db->query("SELECT * FROM announcements WHERE is_published = 1 AND (valid_until IS NULL OR valid_until >= NOW()) ORDER BY created_at DESC LIMIT 5");
        $this->data['announcements'] = $this->db->results();
        
        // Get upcoming events
        $this->db->query("SELECT * FROM events WHERE start_datetime >= NOW() AND is_active = 1 ORDER BY start_datetime ASC LIMIT 5");
        $this->data['upcoming_events'] = $this->db->results();
        
        // Get member's own profile if linked
        $this->db->query("SELECT * FROM members WHERE user_id = ?", [$user['id']]);
        $this->data['member_profile'] = $this->db->first();
        
        $this->data['user'] = $user;
        
        $this->view('dashboard.member');
    }
    
    /**
     * Admin/Leader Dashboard (default)
     */
    private function adminDashboard() {
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
