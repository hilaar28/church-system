<?php 
$page_title = 'Member Dashboard - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-home me-2"></i>Member Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="me-3">Welcome, <?= e($user['first_name'] ?? 'Member') ?>!</span>
        <span class="badge bg-secondary">Member</span>
    </div>
</div>

<!-- Welcome Message -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4>Welcome to <?= SITE_NAME ?>!</h4>
                <p class="mb-0">Stay connected with the church community and stay updated with the latest events and announcements.</p>
            </div>
        </div>
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
                    <div class="col-md-4">
                        <a href="http://localhost/church-system/public/events" class="btn btn-primary w-100">
                            <i class="fas fa-calendar-alt me-1"></i> View Events
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="http://localhost/church-system/public/communications" class="btn btn-info w-100">
                            <i class="fas fa-bullhorn me-1"></i> Announcements
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="http://localhost/church-system/public/donations/add" class="btn btn-success w-100">
                            <i class="fas fa-donate me-1"></i> Give Tithe/Offering
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements -->
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Church Announcements</h5>
            </div>
            <div class="card-body">
                <?php if (empty($announcements)): ?>
                <p class="text-muted">No announcements at this time.</p>
                <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                <div class="border-bottom py-3">
                    <div class="d-flex justify-content-between">
                        <h5><?= e($announcement['title']) ?></h5>
                        <?php if ($announcement['priority'] === 'urgent'): ?>
                            <span class="badge bg-danger">Urgent</span>
                        <?php elseif ($announcement['priority'] === 'high'): ?>
                            <span class="badge bg-warning">High</span>
                        <?php endif; ?>
                    </div>
                    <p class="mb-2"><?= e($announcement['content']) ?></p>
                    <small class="text-muted">
                        <i class="far fa-clock me-1"></i><?= formatDateTime($announcement['created_at']) ?>
                    </small>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Events</h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcoming_events)): ?>
                <p class="text-muted">No upcoming events.</p>
                <?php else: ?>
                <div class="list-group">
                    <?php foreach ($upcoming_events as $event): ?>
                    <a href="http://localhost/church-system/public/events/view?id=<?= $event['id'] ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?= e($event['title']) ?></h6>
                            <small><?= formatDate($event['start_datetime']) ?></small>
                        </div>
                        <small class="text-muted"><?= e($event['location'] ?? 'Location TBA') ?></small>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($member_profile): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>My Profile</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Name:</strong> <?= e($member_profile['first_name'] . ' ' . $member_profile['last_name']) ?></p>
                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-<?= getStatusClass($member_profile['membership_status']) ?>"><?= ucfirst($member_profile['membership_status']) ?></span></p>
                <p class="mb-0"><strong>Member Since:</strong> <?= formatDate($member_profile['membership_date']) ?></p>
                <hr>
                <a href="http://localhost/church-system/public/members/view?id=<?= $member_profile['id'] ?>" class="btn btn-sm btn-outline-primary w-100">
                    <i class="fas fa-eye me-1"></i> View My Profile
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
