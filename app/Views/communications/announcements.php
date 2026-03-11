<?php 
$page_title = 'Announcements - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Announcements</h1>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($announcements)): ?>
        <p class="text-muted">No announcements found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $ann): ?>
                    <tr>
                        <td><?= e($ann['title']) ?></td>
                        <td>
                            <span class="badge bg-<?= getStatusClass($ann['priority']) ?>">
                                <?= ucfirst($ann['priority']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($ann['is_published']): ?>
                                <span class="badge bg-success">Published</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td><?= formatDate($ann['created_at']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteAnnouncement(<?= $ann['id'] ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
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
