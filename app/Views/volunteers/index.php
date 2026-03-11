<?php 
$page_title = 'Volunteers - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Volunteer Opportunities</h1>
</div>

<!-- Opportunities Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($opportunities)): ?>
        <p class="text-muted">No volunteer opportunities found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Slots</th>
                        <th>Assigned</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($opportunities as $opp): ?>
                    <tr>
                        <td><?= e($opp['title']) ?></td>
                        <td><?= formatDateTime($opp['start_datetime']) ?></td>
                        <td><?= e($opp['location'] ?? '-') ?></td>
                        <td><?= $opp['slots_available'] ?? 'Unlimited' ?></td>
                        <td><?= $opp['assigned'] ?? 0 ?></td>
                        <td>
                            <a href="http://localhost/church-system/public/volunteers/viewOpportunity?id=<?= $opp['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
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

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
