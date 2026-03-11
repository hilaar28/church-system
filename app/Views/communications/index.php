<?php 
$page_title = 'Communications - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Communications</h1>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <h5 class="card-title">Total Messages</h5>
                <h2><?= $stats['total'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <h5 class="card-title">Sent</h5>
                <h2><?= $stats['sent'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <h5 class="card-title">Pending</h5>
                <h2><?= $stats['pending'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <h5 class="card-title">Failed</h5>
                <h2><?= $stats['failed'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Messages</h5>
            </div>
            <div class="card-body">
                <a href="http://localhost/church-system/public/communications/messages" class="btn btn-primary">
                    <i class="fas fa-envelope me-1"></i> View Messages
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Announcements</h5>
            </div>
            <div class="card-body">
                <a href="http://localhost/church-system/public/communications/announcements" class="btn btn-info">
                    <i class="fas fa-bullhorn me-1"></i> Manage Announcements
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
