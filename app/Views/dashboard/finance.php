<?php 
$page_title = 'Finance Dashboard - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-chart-line me-2"></i>Finance Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="me-3">Welcome, <?= e($user['first_name'] ?? 'User') ?>!</span>
        <span class="badge bg-success">Finance</span>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="http://localhost/church-system/public/donations/add" class="btn btn-success w-100">
                            <i class="fas fa-donate me-1"></i> Record Donation
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="http://localhost/church-system/public/expenses/add" class="btn btn-danger w-100">
                            <i class="fas fa-receipt me-1"></i> Add Expense
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="http://localhost/church-system/public/finance/summary" class="btn btn-primary w-100">
                            <i class="fas fa-chart-pie me-1"></i> Financial Summary
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="http://localhost/church-system/public/reports/donations" class="btn btn-info w-100">
                            <i class="fas fa-file-alt me-1"></i> Donations Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Overview Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Donations (YTD)</h6>
                        <h2 class="mb-0"><?= $currency . number_format($total_donations, 2) ?></h2>
                    </div>
                    <div class="text-white">
                        <i class="fas fa-hand-holding-usd fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Expenses (YTD)</h6>
                        <h2 class="mb-0"><?= $currency . number_format($total_expenses, 2) ?></h2>
                    </div>
                    <div class="text-white">
                        <i class="fas fa-credit-card fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-<?= $net_balance >= 0 ? 'primary' : 'warning' ?> text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Net Balance (YTD)</h6>
                        <h2 class="mb-0"><?= $currency . number_format($net_balance, 2) ?></h2>
                    </div>
                    <div class="text-white">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Approvals Alert -->
<?php if ($pending_approvals > 0): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong><?= $pending_approvals ?></strong> expense(s) awaiting approval.
            <a href="http://localhost/church-system/public/expenses" class="alert-link">Review now</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Donations and Expenses -->
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-donate me-2"></i>Recent Donations</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_donations)): ?>
                <p class="text-muted">No donations recorded yet.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_donations as $donation): ?>
                            <tr>
                                <td><?= e($donation['first_name'] . ' ' . $donation['last_name']) ?></td>
                                <td><?= $currency . number_format($donation['amount'], 2) ?></td>
                                <td><span class="badge bg-success"><?= ucfirst($donation['donation_type']) ?></span></td>
                                <td><?= formatDate($donation['donation_date']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Recent Expenses</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_expenses)): ?>
                <p class="text-muted">No expenses recorded yet.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_expenses as $expense): ?>
                            <tr>
                                <td><?= e($expense['category_name'] ?? 'N/A') ?></td>
                                <td><?= $currency . number_format($expense['amount'], 2) ?></td>
                                <td><?= formatDate($expense['expense_date']) ?></td>
                                <td>
                                    <?php if ($expense['is_approved']): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
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

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
