<?php 
$page_title = 'View Group - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= e($group['name']) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/groups/edit?id=<?= $group['id'] ?>" class="btn btn-sm btn-primary me-2">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="http://localhost/church-system/public/groups/members?id=<?= $group['id'] ?>" class="btn btn-sm btn-info me-2">
            <i class="fas fa-users me-1"></i> Manage Members
        </a>
        <a href="http://localhost/church-system/public/groups" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Groups
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Group Details</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Type:</dt>
                    <dd class="col-sm-9"><?= ucfirst(str_replace('_', ' ', $group['group_type'])) ?></dd>
                    
                    <dt class="col-sm-3">Description:</dt>
                    <dd class="col-sm-9"><?= nl2br(e($group['description'] ?? 'No description')) ?></dd>
                    
                    <dt class="col-sm-3">Meeting Day:</dt>
<dd class="col-sm-9"><?= $group['meeting_day'] ?: 'Not specified' ?></dd>
                    
                    <dt class="col-sm-3">Meeting Time:</dt>
                    <dd class="col-sm-9"><?= $group['meeting_time'] ?: 'Not specified' ?></dd>
                    
                    <dt class="col-sm-3">Location:</dt>
                    <dd class="col-sm-9"><?= e($group['meeting_location'] ?: 'Not specified') ?></dd>
                    
                    <dt class="col-sm-3">Capacity:</dt>
                    <dd class="col-sm-9"><?= $group['capacity'] ?: 'No limit' ?></dd>
                    
                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9">
                        <?php if ($group['is_active']): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Leader</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($group['leader_name'])): ?>
                    <p class="mb-0"><?= e($group['leader_name']) ?></p>
                <?php else: ?>
                    <p class="text-muted mb-0">No leader assigned</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Members</h5>
            </div>
            <div class="card-body">
                <h3 class="mb-0"><?= $member_count ?? 0 ?></h3>
                <p class="text-muted mb-0">Total members</p>
                <a href="http://localhost/church-system/public/groups/members?id=<?= $group['id'] ?>" class="btn btn-sm btn-outline-primary mt-3">
                    View Members
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
