<?php 
$page_title = ($page_title ?? 'Add Expense') . ' - ' . SITE_NAME;
$auth = new Auth();
$isEdit = isset($expense) && !empty($expense['id']);
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $page_title ?></h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="http://localhost/church-system/public/expenses/save">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $expense['id'] ?>">
            <?php endif; ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="category_id" class="form-label">Category *</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php if (isset($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($expense['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="amount" class="form-label">Amount *</label>
                    <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" 
                           value="<?= old('amount', $expense['amount'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="expense_type" class="form-label">Expense Type *</label>
                    <select class="form-select" id="expense_type" name="expense_type" required>
                        <option value="">Select Type</option>
                        <option value="pastor_expenses" <?= ($expense['expense_type'] ?? '') == 'pastor_expenses' ? 'selected' : '' ?>>Pastor Expenses</option>
                        <option value="rentals" <?= ($expense['expense_type'] ?? '') == 'rentals' ? 'selected' : '' ?>>Rentals</option>
                        <option value="utilities" <?= ($expense['expense_type'] ?? '') == 'utilities' ? 'selected' : '' ?>>Utilities</option>
                        <option value="supplies" <?= ($expense['expense_type'] ?? '') == 'supplies' ? 'selected' : '' ?>>Supplies</option>
                        <option value="maintenance" <?= ($expense['expense_type'] ?? '') == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        <option value="salaries" <?= ($expense['expense_type'] ?? '') == 'salaries' ? 'selected' : '' ?>>Salaries</option>
                        <option value="missions" <?= ($expense['expense_type'] ?? '') == 'missions' ? 'selected' : '' ?>>Missions</option>
                        <option value="events" <?= ($expense['expense_type'] ?? '') == 'events' ? 'selected' : '' ?>>Events</option>
                        <option value="other" <?= ($expense['expense_type'] ?? '') == 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="expense_date" class="form-label">Expense Date *</label>
                    <input type="date" class="form-control" id="expense_date" name="expense_date" 
                           value="<?= old('expense_date', $expense['expense_date'] ?? date('Y-m-d')) ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="vendor_name" class="form-label">Vendor Name</label>
                    <input type="text" class="form-control" id="vendor_name" name="vendor_name" 
                           value="<?= old('vendor_name', $expense['vendor_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="invoice_number" class="form-label">Invoice Number</label>
                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" 
                           value="<?= old('invoice_number', $expense['invoice_number'] ?? '') ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method">
                        <option value="cash" <?= ($expense['payment_method'] ?? 'cash') == 'cash' ? 'selected' : '' ?>>Cash</option>
                        <option value="check" <?= ($expense['payment_method'] ?? '') == 'check' ? 'selected' : '' ?>>Check</option>
                        <option value="card" <?= ($expense['payment_method'] ?? '') == 'card' ? 'selected' : '' ?>>Card</option>
                        <option value="bank_transfer" <?= ($expense['payment_method'] ?? '') == 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="check_number" class="form-label">Check Number</label>
                    <input type="text" class="form-control" id="check_number" name="check_number" 
                           value="<?= old('check_number', $expense['check_number'] ?? '') ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="2"><?= old('description', $expense['description'] ?? '') ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $expense['notes'] ?? '') ?></textarea>
            </div>
            
            <?php if ($isEdit): ?>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" 
                           <?= ($expense['is_approved'] ?? 0) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_approved">
                        Approved
                    </label>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between">
                <a href="http://localhost/church-system/public/expenses" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Update Expense' : 'Add Expense' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
$scripts = '';
require APP_PATH . '/Views/layouts/main.php';
?>
