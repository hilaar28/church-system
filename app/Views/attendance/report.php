<?php 
$page_title = 'Attendance Report - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $page_title ?></h1>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="http://localhost/church-system/public/attendance/report" class="row g-3">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" value="<?= e($start_date ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" value="<?= e($end_date ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="service_type" class="form-label">Service Type</label>
                <select class="form-select" name="service_type">
                    <option value="">All Types</option>
                    <option value="sunday_service" <?= ($service_type ?? '') == 'sunday_service' ? 'selected' : '' ?>>Sunday Service</option>
                    <option value="youth" <?= ($service_type ?? '') == 'youth' ? 'selected' : '' ?>>Youth</option>
                    <option value="midweek" <?= ($service_type ?? '') == 'midweek' ? 'selected' : '' ?>>Midweek</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Report Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($services)): ?>
        <p class="text-muted">No attendance records found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Service</th>
                        <th>Type</th>
                        <th>Total</th>
                        <th>Present</th>
                        <th>Visitors</th>
                        <th>Absent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?= formatDate($service['service_date']) ?></td>
                        <td><?= e($service['service_name']) ?></td>
                        <td><?= ucfirst($service['service_type']) ?></td>
                        <td><?= $service['total_attendance'] ?? 0 ?></td>
                        <td><?= $service['present'] ?? 0 ?></td>
                        <td><?= $service['visitors'] ?? 0 ?></td>
                        <td><?= $service['absent'] ?? 0 ?></td>
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
$scripts = '';
require APP_PATH . '/Views/layouts/main.php';
?>
