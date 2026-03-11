<?php 
$page_title = 'Pastor Dashboard - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-cross me-2"></i>Pastor Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="me-3">Welcome, Pastor <?= e($user['last_name'] ?? 'User') ?>!</span>
        <span class="badge bg-purple" style="background-color: #6f42c1;">Pastor</span>
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
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/attendance/record" class="btn btn-primary w-100">
                            <i class="fas fa-clipboard-check me-1"></i> Take Attendance
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/events/add" class="btn btn-success w-100">
                            <i class="fas fa-calendar-plus me-1"></i> Add Event
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/communications/announcements" class="btn btn-info w-100">
                            <i class="fas fa-bullhorn me-1"></i> Announcements
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/volunteers" class="btn btn-warning w-100">
                            <i class="fas fa-hands-helping me-1"></i> Volunteers
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/reports/attendance" class="btn btn-secondary w-100">
                            <i class="fas fa-chart-line me-1"></i> Attendance Report
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/groups" class="btn btn-purple w-100" style="background-color: #6f42c1; border-color: #6f42c1; color: white;">
                            <i class="fas fa-users me-1"></i> Groups
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Total Members</h6>
                        <h2 class="mb-0"><?= $stats['total'] ?? 0 ?></h2>
                    </div>
                    <div class="text-white">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Active Members</h6>
                        <h2 class="mb-0"><?= $stats['members'] ?? 0 ?></h2>
                    </div>
                    <div class="text-white">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Services This Week</h6>
                        <h2 class="mb-0"><?= $services_this_week ?? 0 ?></h2>
                    </div>
                    <div class="text-white">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Volunteers</h6>
                        <h2 class="mb-0"><?= $volunteers_count ?? 0 ?></h2>
                    </div>
                    <div class="text-white">
                        <i class="fas fa-hands-helping fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Events -->
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Events</h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcoming_events)): ?>
                <p class="text-muted">No upcoming events.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcoming_events as $event): ?>
                            <tr>
                                <td>
                                    <a href="http://localhost/church-system/public/events/view?id=<?= $event['id'] ?>">
                                        <?= e($event['title']) ?>
                                    </a>
                                </td>
                                <td><?= formatDateTime($event['start_datetime']) ?></td>
                                <td><?= e($event['location'] ?? '-') ?></td>
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
                <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Recent Announcements</h5>
            </div>
            <div class="card-body">
                <?php if (empty($announcements)): ?>
                <p class="text-muted">No announcements.</p>
                <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                <div class="border-bottom py-2">
                    <h6><?= e($announcement['title']) ?></h6>
                    <p class="mb-0 text-muted small"><?= e(truncate($announcement['content'], 100)) ?></p>
                    <small class="text-muted"><?= formatDateTime($announcement['created_at']) ?></small>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Active Groups -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Active Groups Overview</h5>
            </div>
            <div class="card-body">
                <?php if (empty($active_groups)): ?>
                <p class="text-muted">No active groups.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Group Name</th>
                                <th>Type</th>
                                <th>Meeting Time</th>
                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($active_groups as $group): ?>
                            <tr>
                                <td><?= e($group['name']) ?></td>
                                <td><span class="badge bg-info"><?= ucfirst($group['group_type']) ?></span></td>
                                <td>
                                    <?php if ($group['meeting_day']): ?>
                                        <?= ucfirst($group['meeting_day']) ?> at <?= date('H:i', strtotime($group['meeting_time'] ?? '00:00')) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= $group['member_count'] ?? 0 ?></td>
                                <td>
                                    <a href="http://localhost/church-system/public/groups/view?id=<?= $group['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
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
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
