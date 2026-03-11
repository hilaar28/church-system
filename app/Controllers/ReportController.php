<?php
/**
 * Report Controller
 */

class ReportController extends Controller {
    /**
     * Reports index
     */
    public function index() {
        $this->view('reports.index');
    }

    /**
     * Attendance report
     */
    public function attendance() {
        $startDate = $this->input('start_date', date('Y-01-01'));
        $endDate = $this->input('end_date', date('Y-12-31'));
        $serviceType = $this->input('service_type', '');
        
        $where = "s.service_date BETWEEN ? AND ?";
        $params = [$startDate, $endDate];
        
        if ($serviceType) {
            $where .= " AND s.service_type = ?";
            $params[] = $serviceType;
        }
        
        // Services summary
        $this->db->query(
            "SELECT s.service_date, s.service_name, s.service_type, s.start_time,
            COUNT(a.id) as total_attendance,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN a.status = 'visitor' THEN 1 ELSE 0 END) as visitors,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent
            FROM services s
            LEFT JOIN attendance a ON s.id = a.service_id
            WHERE {$where}
            GROUP BY s.id
            ORDER BY s.service_date DESC",
            $params
        );
        $this->data['services'] = $this->db->results();
        
        // Statistics
        $this->db->query(
            "SELECT 
            COUNT(DISTINCT s.id) as total_services,
            COUNT(a.id) as total_attendance,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as total_present,
            SUM(CASE WHEN a.status = 'visitor' THEN 1 ELSE 0 END) as total_visitors
            FROM services s
            LEFT JOIN attendance a ON s.id = a.service_id
            WHERE {$where}",
            $params
        );
        $this->data['stats'] = $this->db->first();
        
        $this->data['start_date'] = $startDate;
        $this->data['end_date'] = $endDate;
        $this->data['service_type'] = $serviceType;
        
        $this->view('reports.attendance');
    }

    /**
     * Donations report
     */
    public function donations() {
        $startDate = $this->input('start_date', date('Y-01-01'));
        $endDate = $this->input('end_date', date('Y-12-31'));
        $pdf = $this->input('pdf', false);
        
        // By type
        $this->db->query(
            "SELECT donation_type, SUM(amount) as total, COUNT(*) as count
            FROM donations
            WHERE donation_date BETWEEN ? AND ?
            GROUP BY donation_type
            ORDER BY total DESC",
            [$startDate, $endDate]
        );
        $this->data['byType'] = $this->db->results();
        
        // Monthly
        $this->db->query(
            "SELECT DATE_FORMAT(donation_date, '%Y-%m') as month,
            SUM(amount) as total
            FROM donations
            WHERE donation_date BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(donation_date, '%Y-%m')
            ORDER BY month",
            [$startDate, $endDate]
        );
        $this->data['monthly'] = $this->db->results();
        
        // Top donors
        $this->db->query(
            "SELECT m.id, m.first_name, m.last_name, 
            SUM(d.amount) as total, COUNT(d.id) as count
            FROM donations d
            JOIN members m ON d.member_id = m.id
            WHERE d.donation_date BETWEEN ? AND ?
            GROUP BY m.id
            ORDER BY total DESC
            LIMIT 20",
            [$startDate, $endDate]
        );
        $this->data['topDonors'] = $this->db->results();
        
        // Total
        $this->db->query(
            "SELECT COALESCE(SUM(amount), 0) as total FROM donations WHERE donation_date BETWEEN ? AND ?",
            [$startDate, $endDate]
        );
        $this->data['total'] = $this->db->first()->total;
        
        $this->data['start_date'] = $startDate;
        $this->data['end_date'] = $endDate;
        $this->data['pdf'] = $pdf;
        
        if ($pdf) {
            $this->view('reports.donations-pdf');
        } else {
            $this->view('reports.donations');
        }
    }

    /**
     * Expenses report
     */
    public function expenses() {
        $startDate = $this->input('start_date', date('Y-01-01'));
        $endDate = $this->input('end_date', date('Y-12-31'));
        $pdf = $this->input('pdf', false);
        
        // By type
        $this->db->query(
            "SELECT expense_type, SUM(amount) as total, COUNT(*) as count
            FROM expenses
            WHERE expense_date BETWEEN ? AND ? AND is_approved = 1
            GROUP BY expense_type
            ORDER BY total DESC",
            [$startDate, $endDate]
        );
        $this->data['byType'] = $this->db->results();
        
        // Monthly
        $this->db->query(
            "SELECT DATE_FORMAT(expense_date, '%Y-%m') as month,
            SUM(amount) as total
            FROM expenses
            WHERE expense_date BETWEEN ? AND ? AND is_approved = 1
            GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
            ORDER BY month",
            [$startDate, $endDate]
        );
        $this->data['monthly'] = $this->db->results();
        
        // Pending approvals
        $this->db->query(
            "SELECT e.*, ec.name as category_name
            FROM expenses e
            LEFT JOIN expense_categories ec ON e.category_id = ec.id
            WHERE e.is_approved = 0
            ORDER BY e.expense_date DESC"
        );
        $this->data['pending'] = $this->db->results();
        
        // Total
        $this->db->query(
            "SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE expense_date BETWEEN ? AND ? AND is_approved = 1",
            [$startDate, $endDate]
        );
        $this->data['total'] = $this->db->first()->total;
        
        $this->data['start_date'] = $startDate;
        $this->data['end_date'] = $endDate;
        $this->data['pdf'] = $pdf;
        
        if ($pdf) {
            $this->view('reports.expenses-pdf');
        } else {
            $this->view('reports.expenses');
        }
    }

    /**
     * Members report
     */
    public function members() {
        // Status breakdown
        $this->db->query(
            "SELECT membership_status, COUNT(*) as count
            FROM members
            GROUP BY membership_status"
        );
        $this->data['byStatus'] = $this->db->results();
        
        // Type breakdown
        $this->db->query(
            "SELECT membership_type, COUNT(*) as count
            FROM members
            GROUP BY membership_type"
        );
        $this->data['byType'] = $this->db->results();
        
        // Gender breakdown
        $this->db->query(
            "SELECT gender, COUNT(*) as count
            FROM members
            WHERE gender IS NOT NULL
            GROUP BY gender"
        );
        $this->data['byGender'] = $this->db->results();
        
        // Marital status
        $this->db->query(
            "SELECT marital_status, COUNT(*) as count
            FROM members
            WHERE marital_status IS NOT NULL
            GROUP BY marital_status"
        );
        $this->data['byMarital'] = $this->db->results();
        
        // Baptized
        $this->db->query(
            "SELECT baptized, COUNT(*) as count
            FROM members
            GROUP BY baptized"
        );
        $this->data['baptized'] = $this->db->results();
        
        // Total
        $this->db->query("SELECT COUNT(*) as total FROM members");
        $this->data['total'] = $this->db->first()->total;
        
        // New members this year
        $this->db->query(
            "SELECT COUNT(*) as count FROM members WHERE YEAR(created_at) = ?",
            [date('Y')]
        );
        $this->data['newThisYear'] = $this->db->first()->count;
        
        $this->view('reports.members');
    }

    /**
     * Export report (CSV)
     */
    public function export() {
        $type = $this->input('type', 'members');
        $format = $this->input('format', 'csv');
        
        $filename = $type . '_report_' . date('YmdHis');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        switch ($type) {
            case 'members':
                $this->db->query("SELECT first_name, last_name, email, phone, membership_status, membership_date FROM members ORDER BY last_name, first_name");
                $headers = ['First Name', 'Last Name', 'Email', 'Phone', 'Status', 'Join Date'];
                break;
            case 'donations':
                $this->db->query(
                    "SELECT d.donation_date, m.first_name, m.last_name, d.donation_type, d.amount, d.payment_method 
                    FROM donations d 
                    LEFT JOIN members m ON d.member_id = m.id 
                    ORDER BY d.donation_date DESC"
                );
                $headers = ['Date', 'First Name', 'Last Name', 'Type', 'Amount', 'Payment Method'];
                break;
            case 'expenses':
                $this->db->query(
                    "SELECT e.expense_date, ec.name as category, e.amount, e.description, e.payment_method 
                    FROM expenses e 
                    LEFT JOIN expense_categories ec ON e.category_id = ec.id 
                    WHERE e.is_approved = 1
                    ORDER BY e.expense_date DESC"
                );
                $headers = ['Date', 'Category', 'Amount', 'Description', 'Payment Method'];
                break;
            default:
                fclose($output);
                exit;
        }
        
        fputcsv($output, $headers);
        
        $results = $this->db->results();
        foreach ($results as $row) {
            fputcsv($output, (array) $row);
        }
        
        fclose($output);
        exit;
    }
}
