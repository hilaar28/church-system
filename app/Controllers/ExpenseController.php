<?php
/**
 * Expense Controller
 */

class ExpenseController extends Controller {
    /**
     * Expenses list
     */
    public function index() {
        $page = (int) ($this->input('page') ?? 1);
        $category = $this->input('category', '');
        $startDate = $this->input('start_date', '');
        $endDate = $this->input('end_date', '');
        
        $where = [];
        $params = [];
        
        if ($category) {
            $where[] = "e.expense_type = ?";
            $params[] = $category;
        }
        
        if ($startDate) {
            $where[] = "e.expense_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $where[] = "e.expense_date <= ?";
            $params[] = $endDate;
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        // Get total
        $this->db->query("SELECT COUNT(*) as total FROM expenses e {$whereClause}", $params);
        $total = $this->db->first()->total;
        
        // Get expenses
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $this->db->query(
            "SELECT e.*, ec.name as category_name, u.first_name as recorded_by_name
            FROM expenses e
            LEFT JOIN expense_categories ec ON e.category_id = ec.id
            LEFT JOIN users u ON e.recorded_by = u.id
            {$whereClause}
            ORDER BY e.expense_date DESC, e.id DESC
            LIMIT " . ITEMS_PER_PAGE . " OFFSET {$offset}",
            $params
        );
        
        $this->data['expenses'] = $this->db->results();
        $this->data['pagination'] = paginate($total, ITEMS_PER_PAGE, $page);
        
        $this->data['category'] = $category;
        $this->data['start_date'] = $startDate;
        $this->data['end_date'] = $endDate;
        
        // Get statistics
        $this->db->query("SELECT 
            COALESCE(SUM(amount), 0) as total,
            SUM(CASE WHEN expense_type = 'pastor_expenses' THEN amount ELSE 0 END) as pastor,
            SUM(CASE WHEN expense_type = 'rentals' THEN amount ELSE 0 END) as rentals,
            SUM(CASE WHEN expense_type = 'utilities' THEN amount ELSE 0 END) as utilities,
            SUM(CASE WHEN expense_type = 'supplies' THEN amount ELSE 0 END) as supplies
            FROM expenses");
        $this->data['stats'] = $this->db->first();
        
        $this->view('expenses.index');
    }

    /**
     * Add expense page
     */
    public function add() {
        $this->data['page_title'] = 'Add Expense';
        
        // Get categories
        $this->db->query("SELECT * FROM expense_categories WHERE is_active = 1 ORDER BY name");
        $this->data['categories'] = $this->db->results();
        
        $this->view('expenses.form');
    }

    /**
     * Save expense
     */
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/expenses');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/expenses/add');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        $data = [
            'category_id' => $this->input('category_id'),
            'amount' => $this->input('amount'),
            'expense_type' => $this->input('expense_type'),
            'description' => $this->input('description'),
            'vendor_name' => $this->input('vendor_name'),
            'invoice_number' => $this->input('invoice_number'),
            'expense_date' => $this->input('expense_date') ?: date('Y-m-d'),
            'payment_method' => $this->input('payment_method') ?: 'cash',
            'check_number' => $this->input('check_number'),
            'notes' => $this->input('notes'),
            'recorded_by' => $this->user()->id,
            'is_approved' => $this->input('is_approved') ? 1 : 0
        ];
        
        // Validate
        $validator = new Validator($data);
        $validator->rules([
            'category_id' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'expense_type' => 'required'
        ]);
        
        if (!$validator->validate()) {
            saveOld($data);
            $this->flash('error', implode(', ', $validator->getFlatErrors()));
            $this->redirect($id ? '/expenses/edit?id=' . $id : '/expenses/add');
        }
        
        if ($id) {
            $this->db->query(
                "UPDATE expenses SET category_id = ?, amount = ?, expense_type = ?, description = ?, 
                vendor_name = ?, invoice_number = ?, expense_date = ?, payment_method = ?, 
                check_number = ?, notes = ?, is_approved = ? WHERE id = ?",
                array_merge(array_values($data), [$id])
            );
            $this->flash('success', 'Expense updated successfully');
        } else {
            $this->db->query(
                "INSERT INTO expenses (category_id, amount, expense_type, description, vendor_name, 
                invoice_number, expense_date, payment_method, check_number, notes, recorded_by, is_approved) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                array_values($data)
            );
            $this->flash('success', 'Expense added successfully');
        }
        
        $this->redirect('/expenses');
    }

    /**
     * Edit expense
     */
    public function edit() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/expenses');
        }
        
        $this->db->query("SELECT * FROM expenses WHERE id = ?", [$id]);
        $expense = $this->db->first();
        
        if (!$expense) {
            $this->flash('error', 'Expense not found');
            $this->redirect('/expenses');
        }
        
        $this->data['expense'] = $expense;
        $this->data['page_title'] = 'Edit Expense';
        
        // Get categories
        $this->db->query("SELECT * FROM expense_categories WHERE is_active = 1 ORDER BY name");
        $this->data['categories'] = $this->db->results();
        
        $this->view('expenses.form');
    }

    /**
     * Categories management
     */
    public function categories() {
        $this->db->query("SELECT * FROM expense_categories ORDER BY name");
        $this->data['categories'] = $this->db->results();
        
        $this->view('expenses.categories');
    }

    /**
     * Save category
     */
    public function saveCategory() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/expenses/categories');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/expenses/categories');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        $data = [
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'category_type' => $this->input('category_type'),
            'is_active' => $this->input('is_active') ?? 1
        ];
        
        if ($id) {
            $this->db->query(
                "UPDATE expense_categories SET name = ?, description = ?, category_type = ?, is_active = ? WHERE id = ?",
                array_merge(array_values($data), [$id])
            );
            $this->flash('success', 'Category updated successfully');
        } else {
            $this->db->query(
                "INSERT INTO expense_categories (name, description, category_type, is_active) VALUES (?, ?, ?, ?)",
                array_values($data)
            );
            $this->flash('success', 'Category added successfully');
        }
        
        $this->redirect('/expenses/categories');
    }

    /**
     * Delete expense
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/expenses');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->flash('error', 'Invalid expense ID');
            $this->redirect('/expenses');
        }
        
        $this->db->query("DELETE FROM expenses WHERE id = ?", [$id]);
        
        $this->flash('success', 'Expense deleted successfully');
        $this->redirect('/expenses');
    }

    /**
     * Approve expense
     */
    public function approve() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/expenses');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->flash('error', 'Invalid expense ID');
            $this->redirect('/expenses');
        }
        
        $this->db->query(
            "UPDATE expenses SET is_approved = 1, approved_by = ?, approved_at = NOW() WHERE id = ?",
            [$this->user()->id, $id]
        );
        
        $this->flash('success', 'Expense approved successfully');
        $this->redirect('/expenses');
    }
}
