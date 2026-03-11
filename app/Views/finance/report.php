<?php 
$page_title = 'Finance Report - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Finance Report</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/finance/report?start_date=<?= e($start_date ?? date('Y-01-01')) ?>&end_date=<?= e($end_date ?? date('Y-12-31')) ?>&pdf=1" target="_blank" class="btn btn-sm btn-danger me-2">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
        <a href="http://localhost/church-system/public/finance" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Finance
        </a>
    </div>
</div>

<!-- Date Range Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="http://localhost/church-system/public/finance/report" class="row g-3">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= e($start_date ?? date('Y-01-01')) ?>">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= e($end_date ?? date('Y-12-31')) ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Generate Report</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card success">
            <div class="card-body">
                <h5 class="card-title">Total Income</h5>
                <h2><?= formatCurrency($totalDonations ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card danger">
            <div class="card-body">
                <h5 class="card-title">Total Expenses</h5>
                <h2><?= formatCurrency($totalExpenses ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card <?= ($netBalance ?? 0) >= 0 ? 'primary' : 'danger' ?>">
            <div class="card-body">
                <h5 class="card-title">Net Balance</h5>
                <h2><?= formatCurrency($netBalance ?? 0) ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Comparison -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Monthly Comparison</h5>
            </div>
            <div class="card-body">
                <?php if (empty($donationsMonthly) && empty($expensesMonthly)): ?>
                <p class="text-muted">No data available for the selected period.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Income</th>
                                <th class="text-end">Expenses</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Combine months from both donations and expenses
                            $months = [];
                            foreach ($donationsMonthly as $d) {
                                $months[$d->month] = ['donations' => $d->total, 'expenses' => 0];
                            }
                            foreach ($expensesMonthly as $e) {
                                if (isset($months[$e->month])) {
                                    $months[$e->month]['expenses'] = $e->total;
                                } else {
                                    $months[$e->month] = ['donations' => 0, 'expenses' => $e->total];
                                }
                            }
                            ksort($months);
                            ?>
                            <?php foreach ($months as $month => $data): ?>
                            <tr>
                                <td><?= date('F Y', strtotime($month . '-01')) ?></td>
                                <td class="text-end text-success"><?= formatCurrency($data['donations']) ?></td>
                                <td class="text-end text-danger"><?= formatCurrency($data['expenses']) ?></td>
                                <td class="text-end <?= ($data['donations'] - $data['expenses']) >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= formatCurrency($data['donations'] - $data['expenses']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td>Total</td>
                                <td class="text-end text-success"><?= formatCurrency($totalDonations ?? 0) ?></td>
                                <td class="text-end text-danger"><?= formatCurrency($totalExpenses ?? 0) ?></td>
                                <td class="text-end <?= ($netBalance ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= formatCurrency($netBalance ?? 0) ?>
                                </td>
                            </tr>
                        </tfoot>
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
