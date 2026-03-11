<?php 
$page_title = 'Activity Log - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Activity Log</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/church-system/public/settings" class="btn btn-sm btn-outline-secondary">Back to Settings</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($activities)): ?>
            <p class="text-muted">No activity records found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td><?= date('Y-m-d H:i', strtotime($activity->created_at)) ?></td>
                            <td><?= e($activity->first_name . ' ' . $activity->last_name) ?></td>
                            <td><?= e($activity->action) ?></td>
                            <td><?= e($activity->details ?? '-') ?></td>
                            <td><?= e($activity->ip_address ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (!empty($pagination)): ?>
            <nav>
                <ul class="pagination">
                    <?php if ($pagination['prev']): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $pagination['prev'] ?>">Previous</a></li>
                    <?php endif; ?>
                    
                    <?php for ($i = $pagination['start']; $i <= $pagination['end']; $i++): ?>
                    <li class="page-item <?= $i == $pagination['current'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['next']): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $pagination['next'] ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
