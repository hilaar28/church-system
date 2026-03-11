<?php 
$page_title = 'Group Members - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Members: <?= e($group['name']) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/groups/view?id=<?= $group['id'] ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Group
        </a>
    </div>
</div>

<div class="row">
    <!-- Current Members -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Current Members (<?= count($members) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($members)): ?>
                    <p class="text-muted">No members in this group yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Joined</th>
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
                                        <td><?= ucfirst($member['member_role'] ?? 'Member') ?></td>
                                        <td><?= isset($member['joined_at']) ? date('M d, Y', strtotime($member['joined_at'])) : '-' ?></td>
                                        <td>
                                            <form method="POST" action="http://localhost/church-system/public/groups/removeMember" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                                <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                                                <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Remove this member from the group?')">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            </form>
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
    
    <!-- Add Members -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Add Members</h5>
            </div>
            <div class="card-body">
                <?php if (empty($available_members)): ?>
                    <p class="text-muted">No available members to add.</p>
                <?php else: ?>
                    <form method="POST" action="http://localhost/church-system/public/groups/addMember">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
                        
                        <div class="mb-3">
                            <label for="member_id" class="form-label">Select Member</label>
                            <select class="form-select" id="member_id" name="member_id" required>
                                <option value="">Choose member...</option>
                                <?php foreach ($available_members as $member): ?>
                                    <option value="<?= $member['id'] ?>">
                                        <?= e($member['first_name'] . ' ' . $member['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role">
                                <option value="member">Member</option>
                                <option value="leader">Leader</option>
                                <option value="assistant">Assistant</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-user-plus me-1"></i> Add to Group
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
