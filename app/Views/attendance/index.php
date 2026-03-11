<?php 
$page_title = 'Attendance - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Attendance</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="fas fa-plus me-1"></i> Add Service
        </button>
        <a href="http://localhost/church-system/public/attendance/record" class="btn btn-sm btn-primary me-2">
            <i class="fas fa-pen me-1"></i> Record Attendance
        </a>
        <a href="http://localhost/church-system/public/attendance/report" class="btn btn-sm btn-info">
            <i class="fas fa-chart-bar me-1"></i> Reports
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <h5 class="card-title">Total Services</h5>
                <h2><?= $stats['total_services'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <h5 class="card-title">Total Attendance</h5>
                <h2><?= $stats['total_attendance'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <h5 class="card-title">Present</h5>
                <h2><?= $stats['present'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <h5 class="card-title">Visitors</h5>
                <h2><?= $stats['visitors'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/attendance" class="row g-3">
            <div class="col-md-3">
                <input type="date" class="form-control" name="date" value="<?= e($date ?? '') ?>" placeholder="Filter by date">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="service_type">
                    <option value="">All Types</option>
                    <option value="sunday_service" <?= ($service_type ?? '') == 'sunday_service' ? 'selected' : '' ?>>Sunday Service</option>
                    <option value="wednesday_service" <?= ($service_type ?? '') == 'wednesday_service' ? 'selected' : '' ?>>Wednesday Service</option>
                    <option value="youth" <?= ($service_type ?? '') == 'youth' ? 'selected' : '' ?>>Youth</option>
                    <option value="children" <?= ($service_type ?? '') == 'children' ? 'selected' : '' ?>>Children</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="http://localhost/church-system/public/attendance" class="btn btn-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Services Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($services)): ?>
        <p class="text-muted">No services found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Service Name</th>
                        <th>Type</th>
                        <th>Time</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?= formatDate($service['service_date']) ?></td>
                        <td><?= e($service['service_name']) ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $service['service_type'])) ?></td>
                        <td><?= formatTime($service['start_time']) ?></td>
                        <td>
                            <span class="badge bg-primary"><?= $service['attendance_count'] ?? 0 ?></span>
                        </td>
                        <td>
                            <a href="http://localhost/church-system/public/attendance/record?service_id=<?= $service['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-pen"></i> Record
                            </a>
                        </td>
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
?>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="http://localhost/church-system/public/attendance/add-service">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    
                    <div class="mb-3">
                        <label for="service_name" class="form-label">Service Name *</label>
                        <input type="text" class="form-control" id="service_name" name="service_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="service_type" class="form-label">Service Type</label>
                        <select class="form-select" id="service_type" name="service_type">
                            <option value="sunday_service">Sunday Service</option>
                            <option value="wednesday_service">Wednesday Service</option>
                            <option value="youth">Youth</option>
                            <option value="children">Children</option>
                            <option value="midweek">Midweek</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="service_date" class="form-label">Date *</label>
                        <input type="date" class="form-control" id="service_date" name="service_date" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" value="09:00">
                        </div>
                        <div class="col-md-6">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" value="11:00">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APP_PATH . '/Views/layouts/main.php'; ?>
