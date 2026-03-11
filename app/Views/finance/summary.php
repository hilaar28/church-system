<?php 
$page_title = 'Monthly Summary - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Monthly Financial Summary</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/finance/summary?month=<?= e($month ?? date('m')) ?>&year=<?= e($year ?? date('Y')) ?>&pdf=1" target="_blank" class="btn btn-sm btn-danger me-2">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
        <a href="http://localhost/church-system/public/finance" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Finance
        </a>
    </div>
</div>

<!-- Month/Year Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="http://localhost/church-system/public/finance/summary" class="row g-3">
            <div class="col-md-3">
                <label for="month" class="form-label">Month</label>
                <select class="form-select" id="month" name="month">
                    <?php for($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= ($month ?? date('m')) == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="year" class="form-label">Year</label>
                <select class="form-select" id="year" name="year">
                    <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= ($year ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">View Summary</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary for <?= $monthName ?? date('F') ?> <?= $year ?? date('Y') ?> -->
<h4 class="mb-3">Summary for <?= e($monthName ?? date('F')) ?> <?= e($year ?? date('Y')) ?></h4>

<!-- Income Breakdown -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Income</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td>Tithes</td>
                                <td class="text-end fw-bold"><?= formatCurrency($donations->tithes ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Offerings</td>
                                <td class="text-end fw-bold"><?= formatCurrency($donations->offerings ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Donations</td>
                                <td class="text-end fw-bold"><?= formatCurrency($donations->donations ?? 0) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td>Special Offerings</td>
                                <td class="text-end fw-bold"><?= formatCurrency($donations->special_offerings ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Building Fund</td>
                                <td class="text-end fw-bold"><?= formatCurrency($donations->building_fund ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Mission</td>
                                <td class="text-end fw-bold"><?= formatCurrency($donations->mission ?? 0) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Total Income</h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5 class="text-success"><?= formatCurrency($donationsTotal ?? 0) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Expenses Breakdown -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Expenses</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td>Pastor Expenses</td>
                                <td class="text-end fw-bold"><?= formatCurrency($expenses->pastor_expenses ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Rentals</td>
                                <td class="text-end fw-bold"><?= formatCurrency($expenses->rentals ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Rates</td>
                                <td class="text-end fw-bold"><?= formatCurrency($expenses->rates ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Improvements</td>
                                <td class="text-end fw-bold"><?= formatCurrency($expenses->improvements ?? 0) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td>Levies</td>
                                <td class="text-end fw-bold"><?= formatCurrency($expenses->levies ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Utilities</td>
                                <td class="text-end fw-bold"><?= formatCurrency($expenses->utilities ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Supplies</td>
                                <td class="text-end fw-bold"><?= formatCurrency($expenses->supplies ?? 0) ?></td>
                            </tr>
                            <tr>
                                <td>Other</td>
                                <td class="text-end fw-bold"><?= formatCurrency($expenses->other ?? 0) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Total Expenses</h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5 class="text-danger"><?= formatCurrency($expensesTotal ?? 0) ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Net Balance -->
<div class="row">
    <div class="col-md-12">
        <div class="card bg-<?= ($balance ?? 0) >= 0 ? 'primary' : 'danger' ?> text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-0">Net Balance</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <h2 class="mb-0"><?= formatCurrency($balance ?? 0) ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
