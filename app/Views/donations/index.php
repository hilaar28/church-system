<?php 
$page_title = 'Donations - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Donations</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/donations/add" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Add Donation
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <h5 class="card-title">Total Donations</h5>
                <h2><?= formatCurrency($stats['total'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <h5 class="card-title">Tithes</h5>
                <h2><?= formatCurrency($stats['tithes'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card info">
            <div class="card-body">
                <h5 class="card-title">Offerings</h5>
                <h2><?= formatCurrency($stats['offerings'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <h5 class="card-title">Special Offerings</h5>
                <h2><?= formatCurrency($stats['special'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="http://localhost/church-system/public/donations" class="row g-3">
            <div class="col-md-2">
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="tithe" <?= ($type ?? '') == 'tithe' ? 'selected' : '' ?>>Tithe</option>
                    <option value="offering" <?= ($type ?? '') == 'offering' ? 'selected' : '' ?>>Offering</option>
                    <option value="donation" <?= ($type ?? '') == 'donation' ? 'selected' : '' ?>>Donation</option>
                    <option value="special_offering" <?= ($type ?? '') == 'special_offering' ? 'selected' : '' ?>>Special Offering</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="start_date" value="<?= e($start_date ?? '') ?>" placeholder="Start Date">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="end_date" value="<?= e($end_date ?? '') ?>" placeholder="End Date">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="http://localhost/church-system/public/donations" class="btn btn-secondary w-100">Clear</a>
            </div>
            <div class="col-md-2">
                <a href="http://localhost/church-system/public/donations/report" class="btn btn-info w-100">Reports</a>
            </div>
        </form>
    </div>
</div>

<!-- Donations Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($donations)): ?>
        <p class="text-muted">No donations found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Member</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $donation): ?>
                    <tr>
                        <td><?= formatDate($donation['donation_date']) ?></td>
                        <td>
                            <?php if ($donation['first_name']): ?>
                                <?= e($donation['first_name'] . ' ' . $donation['last_name']) ?>
                            <?php else: ?>
                                <span class="text-muted">Anonymous</span>
                            <?php endif; ?>
                        </td>
                        <td><?= ucfirst(str_replace('_', ' ', $donation['donation_type'])) ?></td>
                        <td><?= formatCurrency($donation['amount']) ?></td>
                        <td><?= ucfirst($donation['payment_method']) ?></td>
                        <td><?= e($donation['recorded_by_name'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
