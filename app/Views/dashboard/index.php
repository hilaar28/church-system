<?php 
$page_title = 'Dashboard - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-center pt-3-nowrap align-items pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="me-3">Welcome, <?= e($user['first_name'] ?? 'User') ?>!</span>
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
                        <a href="http://localhost/church-system/public/members/add" class="btn btn-primary w-100">
                            <i class="fas fa-user-plus me-1"></i> Add Member
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/families/add" class="btn btn-success w-100">
                            <i class="fas fa-house-user me-1"></i> Add Family
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/groups/add" class="btn btn-info w-100">
                            <i class="fas fa-users me-1"></i> Add Group
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/donations/add" class="btn btn-warning w-100">
                            <i class="fas fa-donate me-1"></i> Add Donation
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/expenses/add" class="btn btn-danger w-100">
                            <i class="fas fa-receipt me-1"></i> Add Expense
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="http://localhost/church-system/public/events/add" class="btn btn-secondary w-100">
                            <i class="fas fa-calendar-plus me-1"></i> Add Event
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
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Members</h6>
                        <h2 class="mb-0"><?= $stats['total'] ?? 0 ?></h2>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Active Members</h6>
                        <h2 class="mb-0"><?= $stats['members'] ?? 0 ?></h2>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Visitors</h6>
                        <h2 class="mb-0"><?= $stats['visitors'] ?? 0 ?></h2>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-user-plus fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Baptized</h6>
                        <h2 class="mb-0"><?= $stats['baptized'] ?? 0 ?></h2>
                    </div>
                    <div class="text-danger">
                        <i class="fas fa-pray fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Members and Upcoming Events -->
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Recent Members</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_members)): ?>
                <p class="text-muted">No members found.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_members as $member): ?>
                            <tr>
                                <td>
                                    <a href="http://localhost/church-system/public/members/view?id=<?= $member['id'] ?>">
                                        <?= e($member['first_name'] . ' ' . $member['last_name']) ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-<?= getStatusClass($member['membership_status']) ?>">
                                        <?= ucfirst($member['membership_status']) ?>
                                    </span>
                                </td>
                                <td><?= formatDate($member['membership_date']) ?></td>
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

<!-- Announcements -->
<?php if (!empty($announcements)): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Announcements</h5>
            </div>
            <div class="card-body">
                <?php foreach ($announcements as $announcement): ?>
                <div class="border-bottom py-2">
                    <h6><?= e($announcement['title']) ?></h6>
                    <p class="mb-0 text-muted"><?= e(truncate($announcement['content'], 150)) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
$scripts = '';
require APP_PATH . '/Views/layouts/main.php';
?>
