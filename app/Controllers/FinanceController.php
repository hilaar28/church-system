<?php
/**
 * Finance Controller
 */

class FinanceController extends Controller {
    /**
     * Finance overview
     */
    public function index() {
        $year = (int) ($this->input('year', date('Y')));
        
        // Get totals
        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total FROM donations");
        $totalDonations = $this->db->first()->total;
        
        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE is_approved = 1");
        $totalExpenses = $this->db->first()->total;
        
        $this->data['totalDonations'] = $totalDonations;
        $this->data['totalExpenses'] = $totalExpenses;
        $this->data['balance'] = $totalDonations - $totalExpenses;
        $this->data['year'] = $year;
        
        // Monthly breakdown for the year
        $this->db->query(
            "SELECT 
            DATE_FORMAT(donation_date, '%m') as month,
            SUM(CASE WHEN donation_type = 'tithe' THEN amount ELSE 0 END) as tithes,
            SUM(CASE WHEN donation_type = 'offering' THEN amount ELSE 0 END) as offerings,
            SUM(CASE WHEN donation_type IN ('donation', 'special_offering') THEN amount ELSE 0 END) as other
            FROM donations
            WHERE YEAR(donation_date) = ?
            GROUP BY DATE_FORMAT(donation_date, '%m')
            ORDER BY month",
            [$year]
        );
        $this->data['donationsByMonth'] = $this->db->results();
        
        // Monthly expenses
        $this->db->query(
            "SELECT 
            DATE_FORMAT(expense_date, '%m') as month,
            SUM(amount) as total
            FROM expenses
            WHERE YEAR(expense_date) = ? AND is_approved = 1
            GROUP BY DATE_FORMAT(expense_date, '%m')
            ORDER BY month",
            [$year]
        );
        $this->data['expensesByMonth'] = $this->db->results();
        
        // Donation types breakdown
        $this->db->query(
            "SELECT donation_type, SUM(amount) as total FROM donations GROUP BY donation_type"
        );
        $this->data['donationsByType'] = $this->db->results();
        
        // Expense types breakdown
        $this->db->query(
            "SELECT expense_type, SUM(amount) as total FROM expenses WHERE is_approved = 1 GROUP BY expense_type"
        );
        $this->data['expensesByType'] = $this->db->results();
        
        $this->view('finance.index');
    }

    /**
     * Financial summary
     */
    public function summary() {
        $month = $this->input('month', date('m'));
        $year = (int) ($this->input('year', date('Y')));
        $pdf = $this->input('pdf', false);
        
        $startDate = "{$year}-{$month}-01";
        $endDate = date("Y-m-t", strtotime($startDate));
        
        // Donations
        $this->db->query(
            "SELECT 
            COALESCE(SUM(amount), 0) as total,
            SUM(CASE WHEN donation_type = 'tithe' THEN amount ELSE 0 END) as tithes,
            SUM(CASE WHEN donation_type = 'offering' THEN amount ELSE 0 END) as offerings,
            SUM(CASE WHEN donation_type = 'donation' THEN amount ELSE 0 END) as donations,
            SUM(CASE WHEN donation_type = 'special_offering' THEN amount ELSE 0 END) as special_offerings,
            SUM(CASE WHEN donation_type = 'building_fund' THEN amount ELSE 0 END) as building_fund,
            SUM(CASE WHEN donation_type = 'mission' THEN amount ELSE 0 END) as mission
            FROM donations
            WHERE donation_date BETWEEN ? AND ?",
            [$startDate, $endDate]
        );
        $this->data['donations'] = $this->db->first();
        
        // Expenses
        $this->db->query(
            "SELECT 
            COALESCE(SUM(amount), 0) as total,
            SUM(CASE WHEN expense_type = 'pastor_expenses' THEN amount ELSE 0 END) as pastor_expenses,
            SUM(CASE WHEN expense_type = 'rentals' THEN amount ELSE 0 END) as rentals,
            SUM(CASE WHEN expense_type = 'rates' THEN amount ELSE 0 END) as rates,
            SUM(CASE WHEN expense_type = 'improvements' THEN amount ELSE 0 END) as improvements,
            SUM(CASE WHEN expense_type = 'levies' THEN amount ELSE 0 END) as levies,
            SUM(CASE WHEN expense_type = 'utilities' THEN amount ELSE 0 END) as utilities,
            SUM(CASE WHEN expense_type = 'supplies' THEN amount ELSE 0 END) as supplies,
            SUM(CASE WHEN expense_type = 'other' THEN amount ELSE 0 END) as other
            FROM expenses
            WHERE expense_date BETWEEN ? AND ? AND is_approved = 1",
            [$startDate, $endDate]
        );
        $this->data['expenses'] = $this->db->first();
        
        // Calculate balance
        $this->data['donationsTotal'] = $this->data['donations']->total;
        $this->data['expensesTotal'] = $this->data['expenses']->total;
        $this->data['balance'] = $this->data['donationsTotal'] - $this->data['expensesTotal'];
        
        $this->data['month'] = $month;
        $this->data['year'] = $year;
        $this->data['monthName'] = date('F', strtotime($startDate));
        $this->data['pdf'] = $pdf;
        
        if ($pdf) {
            $this->view('finance.summary-pdf');
        } else {
            $this->view('finance.summary');
        }
    }

    /**
     * Generate report
     */
    public function report() {
        $startDate = $this->input('start_date', date('Y-01-01'));
        $endDate = $this->input('end_date', date('Y-12-31'));
        $pdf = $this->input('pdf', false);
        
        // Donations by month
        $this->db->query(
            "SELECT DATE_FORMAT(donation_date, '%Y-%m') as month,
            SUM(amount) as total
            FROM donations
            WHERE donation_date BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(donation_date, '%Y-%m')
            ORDER BY month",
            [$startDate, $endDate]
        );
        $donationsMonthly = $this->db->results();
        
        // Expenses by month
        $this->db->query(
            "SELECT DATE_FORMAT(expense_date, '%Y-%m') as month,
            SUM(amount) as total
            FROM expenses
            WHERE expense_date BETWEEN ? AND ? AND is_approved = 1
            GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
            ORDER BY month",
            [$startDate, $endDate]
        );
        $expensesMonthly = $this->db->results();
        
        // Calculate totals
        $totalDonations = array_sum(array_column($donationsMonthly, 'total'));
        $totalExpenses = array_sum(array_column($expensesMonthly, 'total'));
        
        $this->data['donationsMonthly'] = $donationsMonthly;
        $this->data['expensesMonthly'] = $expensesMonthly;
        $this->data['totalDonations'] = $totalDonations;
        $this->data['totalExpenses'] = $totalExpenses;
        $this->data['netBalance'] = $totalDonations - $totalExpenses;
        $this->data['start_date'] = $startDate;
        $this->data['end_date'] = $endDate;
        $this->data['pdf'] = $pdf;
        
        if ($pdf) {
            $this->view('finance.report-pdf');
        } else {
            $this->view('finance.report');
        }
    }
}
