<?php 
$page_title = 'Events - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Events</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/events/add" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Add Event
        </a>
    </div>
</div>

<!-- Events Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($events)): ?>
        <p class="text-muted">No events found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Registrations</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                    <tr>
                        <td>
                            <a href="http://localhost/church-system/public/events/view?id=<?= $event['id'] ?>">
                                <?= e($event['title']) ?>
                            </a>
                        </td>
                        <td><?= ucfirst($event['event_type']) ?></td>
                        <td><?= formatDateTime($event['start_datetime']) ?></td>
                        <td><?= e($event['location'] ?? '-') ?></td>
                        <td>
                            <span class="badge bg-info"><?= $event['registrations'] ?? 0 ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="http://localhost/church-system/public/events/view?id=<?= $event['id'] ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="http://localhost/church-system/public/events/edit?id=<?= $event['id'] ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
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
