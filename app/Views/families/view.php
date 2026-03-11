<?php 
$page_title = 'View Family - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= e($family['family_name']) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/families/edit?id=<?= $family['id'] ?>" class="btn btn-sm btn-primary me-2">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="http://localhost/church-system/public/families" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Families
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Family Details</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Family Name:</dt>
                    <dd class="col-sm-9"><?= e($family['family_name']) ?></dd>
                    
                    <dt class="col-sm-3">Head of Family:</dt>
                    <dd class="col-sm-9"><?= !empty($family['head_of_family_name']) ? e($family['head_of_family_name']) : 'Not specified' ?></dd>
                    
                    <dt class="col-sm-3">Address:</dt>
                    <dd class="col-sm-9"><?= e($family['address'] ?: 'Not specified') ?></dd>
                    
                    <dt class="col-sm-3">City:</dt>
                    <dd class="col-sm-9"><?= e($family['city'] ?: 'Not specified') ?></dd>
                    
                    <dt class="col-sm-3">State:</dt>
                    <dd class="col-sm-9"><?= e($family['state'] ?: 'Not specified') ?></dd>
                    
                    <dt class="col-sm-3">Postal Code:</dt>
                    <dd class="col-sm-9"><?= e($family['postal_code'] ?: 'Not specified') ?></dd>
                    
                    <dt class="col-sm-3">Phone:</dt>
                    <dd class="col-sm-9"><?= e($family['phone'] ?: 'Not specified') ?></dd>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Family Members</h5>
            </div>
            <div class="card-body">
                <h3 class="mb-0"><?= count($members) ?? 0 ?></h3>
                <p class="text-muted mb-0">Total members</p>
            </div>
        </div>
    </div>
</div>

<!-- Family Members List -->
<?php if (!empty($members)): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Members</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Relationship</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td>
                                        <a href="http://localhost/church-system/public/members/view?id=<?= $member['id'] ?>">
                                            <?= e($member['first_name'] . ' ' . $member['last_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= ucfirst($member['relationship'] ?? 'Member') ?></td>
                                    <td>
                                        <span class="badge bg-<?= getStatusClass($member['membership_status']) ?>">
                                            <?= ucfirst($member['membership_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="http://localhost/church-system/public/members/view?id=<?= $member['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
