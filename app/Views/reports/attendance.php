<?php 
$page_title = 'Attendance Report - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Attendance Report</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/reports" class="btn btn-sm btn-outline-secondary">Back to Reports</a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="http://localhost/church-system/public/reports/attendance" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" value="<?= e($start_date) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" value="<?= e($end_date) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Service Type</label>
                <select class="form-select" name="service_type">
                    <option value="">All Types</option>
                    <option value="Sunday Service" <?= $service_type == 'Sunday Service' ? 'selected' : '' ?>>Sunday Service</option>
                    <option value="Wednesday Service" <?= $service_type == 'Wednesday Service' ? 'selected' : '' ?>>Wednesday Service</option>
                    <option value="Friday Service" <?= $service_type == 'Friday Service' ? 'selected' : '' ?>>Friday Service</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Services</h5>
                <h2><?= number_format($stats['total_services'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Present</h5>
                <h2><?= number_format($stats['total_present'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Total Visitors</h5>
                <h2><?= number_format($stats['total_visitors'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Total Attendance</h5>
                <h2><?= number_format($stats['total_attendance'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Services Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Service Attendance Details</h5>
    </div>
    <div class="card-body">
        <?php if (empty($services)): ?>
            <p class="text-muted">No service attendance records found for the selected period.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Service Name</th>
                            <th>Type</th>
                            <th>Present</th>
                            <th>Visitors</th>
                            <th>Absent</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= date('Y-m-d', strtotime($service['service_date'])) ?></td>
                            <td><?= e($service['service_name']) ?></td>
                            <td><?= e($service['service_type']) ?></td>
                            <td><?= number_format($service['present'] ?? 0) ?></td>
                            <td><?= number_format($service['visitors'] ?? 0) ?></td>
                            <td><?= number_format($service['absent'] ?? 0) ?></td>
                            <td><?= number_format($service['total_attendance'] ?? 0) ?></td>
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
