<?php
/**
 * Donation Controller
 */

class DonationController extends Controller {
    /**
     * Donations list
     */
    public function index() {
        $page = (int) ($this->input('page') ?? 1);
        $search = $this->input('search', '');
        $type = $this->input('type', '');
        $startDate = $this->input('start_date', '');
        $endDate = $this->input('end_date', '');
        
        $where = [];
        $params = [];
        
        if ($type) {
            $where[] = "d.donation_type = ?";
            $params[] = $type;
        }
        
        if ($startDate) {
            $where[] = "d.donation_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $where[] = "d.donation_date <= ?";
            $params[] = $endDate;
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        // Get total for filtering
        $this->db->query("SELECT COUNT(*) as total FROM donations d {$whereClause}", $params);
        $total = $this->db->first()->total;
        
        // Get donations with pagination
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $this->db->query(
            "SELECT d.*, m.first_name, m.last_name, u.first_name as recorded_by_name 
            FROM donations d 
            LEFT JOIN members m ON d.member_id = m.id 
            LEFT JOIN users u ON d.recorded_by = u.id
            {$whereClause}
            ORDER BY d.donation_date DESC, d.id DESC
            LIMIT " . ITEMS_PER_PAGE . " OFFSET {$offset}",
            $params
        );
        
        $this->data['donations'] = $this->db->results();
        $this->data['pagination'] = paginate($total, ITEMS_PER_PAGE, $page);
        
        $this->data['search'] = $search;
        $this->data['type'] = $type;
        $this->data['start_date'] = $startDate;
        $this->data['end_date'] = $endDate;
        
        // Get statistics
        $this->db->query("SELECT 
            COALESCE(SUM(amount), 0) as total,
            SUM(CASE WHEN donation_type = 'tithe' THEN amount ELSE 0 END) as tithes,
            SUM(CASE WHEN donation_type = 'offering' THEN amount ELSE 0 END) as offerings,
            SUM(CASE WHEN donation_type = 'donation' THEN amount ELSE 0 END) as donations,
            SUM(CASE WHEN donation_type = 'special_offering' THEN amount ELSE 0 END) as special
            FROM donations");
        $this->data['stats'] = $this->db->first();
        
        $this->view('donations.index');
    }

    /**
     * Add donation page
     */
    public function add() {
        $this->data['page_title'] = 'Add Donation';
        
        // Get members for dropdown
        $member = $this->model('Member');
        $this->data['members'] = $member->all(['last_name', 'ASC']);
        
        // Get active campaigns
        $this->db->query("SELECT * FROM donation_campaigns WHERE is_active = 1");
        $this->data['campaigns'] = $this->db->results();
        
        $this->view('donations.form');
    }

    /**
     * Save donation
     */
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/donations');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/donations/add');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        $data = [
            'member_id' => $this->input('member_id') ?: null,
            'amount' => $this->input('amount'),
            'donation_type' => $this->input('donation_type') ?: 'offering',
            'campaign_id' => $this->input('campaign_id') ?: null,
            'payment_method' => $this->input('payment_method') ?: 'cash',
            'check_number' => $this->input('check_number'),
            'transaction_id' => $this->input('transaction_id'),
            'donation_date' => $this->input('donation_date') ?: date('Y-m-d'),
            'receipt_number' => $this->input('receipt_number'),
            'is_anonymous' => $this->input('is_anonymous') ? 1 : 0,
            'notes' => $this->input('notes'),
            'recorded_by' => $this->user()->id
        ];
        
        // Validate
        $validator = new Validator($data);
        $validator->rules([
            'amount' => 'required|numeric|min:0.01',
            'donation_type' => 'required'
        ]);
        
        if (!$validator->validate()) {
            saveOld($data);
            $this->flash('error', implode(', ', $validator->getFlatErrors()));
            $this->redirect($id ? '/donations/edit?id=' . $id : '/donations/add');
        }
        
        if ($id) {
            $this->db->query(
                "UPDATE donations SET member_id = ?, amount = ?, donation_type = ?, campaign_id = ?, 
                payment_method = ?, check_number = ?, transaction_id = ?, donation_date = ?, 
                receipt_number = ?, is_anonymous = ?, notes = ? WHERE id = ?",
                array_merge(array_values($data), [$id])
            );
            $this->flash('success', 'Donation updated successfully');
        } else {
            $this->db->query(
                "INSERT INTO donations (member_id, amount, donation_type, campaign_id, payment_method, 
                check_number, transaction_id, donation_date, receipt_number, is_anonymous, notes, recorded_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                array_values($data)
            );
            $this->flash('success', 'Donation added successfully');
        }
        
        $this->redirect('/donations');
    }

    /**
     * Edit donation
     */
    public function edit() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/donations');
        }
        
        $this->db->query("SELECT * FROM donations WHERE id = ?", [$id]);
        $donation = $this->db->first();
        
        if (!$donation) {
            $this->flash('error', 'Donation not found');
            $this->redirect('/donations');
        }
        
        $this->data['donation'] = $donation;
        $this->data['page_title'] = 'Edit Donation';
        
        // Get members
        $member = $this->model('Member');
        $this->data['members'] = $member->all(['last_name', 'ASC']);
        
        // Get campaigns
        $this->db->query("SELECT * FROM donation_campaigns WHERE is_active = 1");
        $this->data['campaigns'] = $this->db->results();
        
        $this->view('donations.form');
    }

    /**
     * Donation report
     */
    public function report() {
        $startDate = $this->input('start_date', date('Y-01-01'));
        $endDate = $this->input('end_date', date('Y-12-31'));
        
        // Monthly breakdown
        $this->db->query(
            "SELECT DATE_FORMAT(donation_date, '%Y-%m') as month,
            SUM(amount) as total,
            SUM(CASE WHEN donation_type = 'tithe' THEN amount ELSE 0 END) as tithes,
            SUM(CASE WHEN donation_type = 'offering' THEN amount ELSE 0 END) as offerings,
            SUM(CASE WHEN donation_type = 'donation' THEN amount ELSE 0 END) as donations
            FROM donations 
            WHERE donation_date BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(donation_date, '%Y-%m')
            ORDER BY month DESC",
            [$startDate, $endDate]
        );
        $this->data['monthly'] = $this->db->results();
        
        // By type
        $this->db->query(
            "SELECT donation_type, SUM(amount) as total, COUNT(*) as count
            FROM donations 
            WHERE donation_date BETWEEN ? AND ?
            GROUP BY donation_type",
            [$startDate, $endDate]
        );
        $this->data['byType'] = $this->db->results();
        
        // Top donors
        $this->db->query(
            "SELECT m.id, m.first_name, m.last_name, SUM(d.amount) as total, COUNT(d.id) as count
            FROM donations d
            JOIN members m ON d.member_id = m.id
            WHERE d.donation_date BETWEEN ? AND ?
            GROUP BY m.id
            ORDER BY total DESC
            LIMIT 10",
            [$startDate, $endDate]
        );
        $this->data['topDonors'] = $this->db->results();
        
        $this->data['start_date'] = $startDate;
        $this->data['end_date'] = $endDate;
        
        // Total
        $this->db->query(
            "SELECT COALESCE(SUM(amount), 0) as total FROM donations WHERE donation_date BETWEEN ? AND ?",
            [$startDate, $endDate]
        );
        $this->data['total'] = $this->db->first()->total;
        
        $this->view('donations.report');
    }

    /**
     * Delete donation
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/donations');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->flash('error', 'Invalid donation ID');
            $this->redirect('/donations');
        }
        
        $this->db->query("DELETE FROM donations WHERE id = ?", [$id]);
        
        $this->flash('success', 'Donation deleted successfully');
        $this->redirect('/donations');
    }
}
