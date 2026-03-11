<?php 
$page_title = 'Members Report - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Members Report</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/reports" class="btn btn-sm btn-outline-secondary">Back to Reports</a>
        <a href="http://localhost/church-system/public/reports/export?type=members" class="btn btn-sm btn-success ms-2">Export CSV</a>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Members</h5>
                <h2><?= number_format($total) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">New This Year</h5>
                <h2><?= number_format($newThisYear) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Baptized Members</h5>
                <h2><?php 
                    $baptizedCount = 0;
                    foreach ($baptized as $b) {
                        if (strtolower($b->baptized) === 'yes') {
                            $baptizedCount = $b->count;
                        }
                    }
                    echo number_format($baptizedCount);
                ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- By Status -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">By Membership Status</h5>
            </div>
            <div class="card-body">
                <?php if (empty($byStatus)): ?>
                    <p class="text-muted">No member records found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($byStatus as $item): ?>
                                <tr>
                                    <td><?= e($item->membership_status ?? 'Unknown') ?></td>
                                    <td><?= number_format($item->count) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- By Type -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">By Membership Type</h5>
            </div>
            <div class="card-body">
                <?php if (empty($byType)): ?>
                    <p class="text-muted">No member records found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($byType as $item): ?>
                                <tr>
                                    <td><?= e($item->membership_type ?? 'Unknown') ?></td>
                                    <td><?= number_format($item->count) ?></td>
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

<div class="row">
    <!-- By Gender -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">By Gender</h5>
            </div>
            <div class="card-body">
                <?php if (empty($byGender)): ?>
                    <p class="text-muted">No gender data available.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Gender</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($byGender as $item): ?>
                                <tr>
                                    <td><?= e($item->gender ?? 'Not Specified') ?></td>
                                    <td><?= number_format($item->count) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- By Marital Status -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">By Marital Status</h5>
            </div>
            <div class="card-body">
                <?php if (empty($byMarital)): ?>
                    <p class="text-muted">No marital status data available.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($byMarital as $item): ?>
                                <tr>
                                    <td><?= e($item->marital_status ?? 'Not Specified') ?></td>
                                    <td><?= number_format($item->count) ?></td>
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
