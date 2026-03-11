<?php 
$page_title = 'Finance - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Finance Overview</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/finance/summary" class="btn btn-sm btn-info me-2">
            <i class="fas fa-file-alt me-1"></i> Monthly Summary
        </a>
        <a href="http://localhost/church-system/public/finance/report" class="btn btn-sm btn-secondary">
            <i class="fas fa-chart-bar me-1"></i> Reports
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card success">
            <div class="card-body">
                <h5 class="card-title">Total Income</h5>
                <h2><?= formatCurrency($totalDonations) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card danger">
            <div class="card-body">
                <h5 class="card-title">Total Expenses</h5>
                <h2><?= formatCurrency($totalExpenses) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card <?= $balance >= 0 ? 'primary' : 'danger' ?>">
            <div class="card-body">
                <h5 class="card-title">Net Balance</h5>
                <h2><?= formatCurrency($balance) ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Year Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/finance" class="row g-3">
            <div class="col-md-3">
                <select class="form-select" name="year">
                    <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= ($year ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">View</button>
            </div>
        </form>
    </div>
</div>

<!-- Donations by Type -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Donations by Type</h5>
            </div>
            <div class="card-body">
                <?php if (empty($donationsByType)): ?>
                <p class="text-muted">No donations data.</p>
                <?php else: ?>
                <table class="table">
                    <tbody>
                        <?php foreach ($donationsByType as $item): ?>
                        <tr>
                            <td><?= ucfirst(str_replace('_', ' ', $item->donation_type)) ?></td>
                            <td class="text-end"><?= formatCurrency($item->total) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Expenses by Type</h5>
            </div>
            <div class="card-body">
                <?php if (empty($expensesByType)): ?>
                <p class="text-muted">No expenses data.</p>
                <?php else: ?>
                <table class="table">
                    <tbody>
                        <?php foreach ($expensesByType as $item): ?>
                        <tr>
                            <td><?= ucfirst(str_replace('_', ' ', $item->expense_type)) ?></td>
                            <td class="text-end"><?= formatCurrency($item->total) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
