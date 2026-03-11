<?php 
$page_title = 'Expenses Report - ' . SITE_NAME;
$auth = new Auth();
$currency = $settings['currency_symbol'] ?? '$';
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Expenses Report</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/reports/expenses?start_date=<?= e($start_date) ?>&end_date=<?= e($end_date) ?>&pdf=1" target="_blank" class="btn btn-sm btn-danger me-2">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
        <a href="http://localhost/church-system/public/reports" class="btn btn-sm btn-outline-secondary">Back to Reports</a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="http://localhost/church-system/public/reports/expenses" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" value="<?= e($start_date) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" value="<?= e($end_date) ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Total -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Total Expenses (<?= e($start_date) ?> to <?= e($end_date) ?>)</h5>
                <h2><?= $currency . number_format($total, 2) ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- By Type -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Expenses by Category</h5>
            </div>
            <div class="card-body">
                <?php if (empty($byType)): ?>
                    <p class="text-muted">No expense records found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Count</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($byType as $item): ?>
                                <tr>
                                    <td><?= e($item->expense_type) ?></td>
                                    <td><?= number_format($item->count) ?></td>
                                    <td><?= $currency . number_format($item->total, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Monthly -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Monthly Expenses</h5>
            </div>
            <div class="card-body">
                <?php if (empty($monthly)): ?>
                    <p class="text-muted">No expense records found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthly as $item): ?>
                                <tr>
                                    <td><?= e($item->month) ?></td>
                                    <td><?= $currency . number_format($item->total, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Pending Approvals -->
<?php if (!empty($pending)): ?>
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Pending Approvals (<?= count($pending) ?>)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending as $expense): ?>
                    <tr>
                        <td><?= date('Y-m-d', strtotime($expense->expense_date)) ?></td>
                        <td><?= e($expense->description) ?></td>
                        <td><?= $currency . number_format($expense->amount, 2) ?></td>
                        <td><?= e($expense->category_name ?? 'N/A') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
