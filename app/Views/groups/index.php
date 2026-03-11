<?php 
$page_title = 'Groups - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Groups</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/groups/add" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Add Group
        </a>
    </div>
</div>

<!-- Filter by Type -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="http://localhost/church-system/public/groups" class="row g-3">
            <div class="col-md-4">
                <select class="form-select" name="type">
                    <option value="">All Group Types</option>
                    <option value="sunday_school" <?= $type == 'sunday_school' ? 'selected' : '' ?>>Sunday School</option>
                    <option value="small_group" <?= $type == 'small_group' ? 'selected' : '' ?>>Small Group</option>
                    <option value="ministry_team" <?= $type == 'ministry_team' ? 'selected' : '' ?>>Ministry Team</option>
                    <option value="cell_group" <?= $type == 'cell_group' ? 'selected' : '' ?>>Cell Group</option>
                    <option value="bible_study" <?= $type == 'bible_study' ? 'selected' : '' ?>>Bible Study</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="http://localhost/church-system/public/groups" class="btn btn-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Groups Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($groups)): ?>
            <p class="text-muted">No groups found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Meeting Day</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Members</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groups as $group): ?>
                            <tr>
                                <td>
                                    <a href="http://localhost/church-system/public/groups/view?id=<?= $group['id'] ?>">
                                        <?= e($group['name']) ?>
                                    </a>
                                </td>
                                <td><?= ucfirst(str_replace('_', ' ', $group['group_type'])) ?></td>
                                <td><?= $group['meeting_day'] ?: '-' ?></td>
                                <td><?= $group['meeting_time'] ?: '-' ?></td>
                                <td><?= e($group['meeting_location'] ?: '-') ?></td>
                                <td>
                                    <a href="http://localhost/church-system/public/groups/members?id=<?= $group['id'] ?>">
                                        <?= $group['member_count'] ?? 0 ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($group['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="http://localhost/church-system/public/groups/edit?id=<?= $group['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="http://localhost/church-system/public/groups/members?id=<?= $group['id'] ?>" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    <form method="POST" action="/groups/delete" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <input type="hidden" name="id" value="<?= $group['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this group?')">
                                            <i class="fas fa-trash"></i>
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

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
