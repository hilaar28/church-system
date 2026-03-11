<?php 
$page_title = 'Secretariat Dashboard - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-users-cog me-2"></i>Secretariat Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="me-3">Welcome, <?= e($user['first_name'] ?? 'User') ?>!</span>
        <span class="badge bg-info">Secretariat</span>
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
                    <div class="col-md-3">
                        <a href="http://localhost/church-system/public/members/add" class="btn btn-primary w-100">
                            <i class="fas fa-user-plus me-1"></i> Add Member
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="http://localhost/church-system/public/families/add" class="btn btn-success w-100">
                            <i class="fas fa-house-user me-1"></i> Add Family
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="http://localhost/church-system/public/groups/add" class="btn btn-info w-100">
                            <i class="fas fa-users me-1"></i> Add Group
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="http://localhost/church-system/public/reports/members" class="btn btn-warning w-100">
                            <i class="fas fa-file-alt me-1"></i> Members Report
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
                        <h6 class="text-muted mb-2">Families</h6>
                        <h2 class="mb-0"><?= $total_families ?? 0 ?></h2>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-house-user fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Groups</h6>
                        <h2 class="mb-0"><?= $total_groups ?? 0 ?></h2>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-layer-group fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Members Alert -->
<?php if ($new_this_month > 0): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-user-plus me-2"></i>
            <strong><?= $new_this_month ?></strong> new member(s) joined this month!
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Members -->
<div class="row">
    <div class="col-md-8">
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
                                <th>Type</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_members as $member): ?>
                            <tr>
                                <td>
                                    <?= e($member['first_name'] . ' ' . $member['last_name']) ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= getStatusClass($member['membership_status']) ?>">
                                        <?= ucfirst($member['membership_status']) ?>
                                    </span>
                                </td>
                                <td><?= ucfirst($member['membership_type'] ?? 'regular') ?></td>
                                <td><?= formatDate($member['membership_date']) ?></td>
                                <td>
                                    <a href="http://localhost/church-system/public/members/view?id=<?= $member['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="http://localhost/church-system/public/members/edit?id=<?= $member['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
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
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Members by Status</h5>
            </div>
            <div class="card-body">
                <?php if (empty($members_by_status)): ?>
                <p class="text-muted">No data available.</p>
                <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($members_by_status as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= ucfirst($item['membership_status'] ?? 'Unknown') ?>
                        <span class="badge bg-primary rounded-pill"><?= $item['count'] ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
