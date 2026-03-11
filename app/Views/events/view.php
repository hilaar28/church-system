<?php 
$page_title = 'View Event - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= e($event['title']) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/events" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> Back to Events
        </a>
        <a href="http://localhost/church-system/public/events/edit?id=<?= $event['id'] ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-edit me-1"></i> Edit Event
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Event Details Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Event Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Description:</div>
                    <div class="col-md-8"><?= e($event['description'] ?? 'No description provided') ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Event Type:</div>
                    <div class="col-md-8"><span class="badge bg-primary"><?= ucfirst($event['event_type']) ?></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Date & Time:</div>
                    <div class="col-md-8">
                        <?= formatDateTime($event['start_datetime']) ?>
                        <?php if ($event['end_datetime']): ?>
                            - <?= formatDateTime($event['end_datetime']) ?>
                        <?php endif; ?>
                        <?php if ($event['is_all_day']): ?>
                            <span class="badge bg-info ms-1">All Day</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Location:</div>
                    <div class="col-md-8"><?= e($event['location'] ?? 'Not specified') ?></div>
                </div>
                <?php if ($event['max_attendees']): ?>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Max Attendees:</div>
                    <div class="col-md-8"><?= (int)$event['max_attendees'] ?></div>
                </div>
                <?php endif; ?>
                <?php if ($event['registration_required']): ?>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Registration Deadline:</div>
                    <div class="col-md-8"><?= $event['registration_deadline'] ? formatDateTime($event['registration_deadline']) : 'No deadline' ?></div>
                </div>
                <?php endif; ?>
                <?php if ($event['is_recurring']): ?>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Recurrence:</div>
                    <div class="col-md-8"><span class="badge bg-warning"><?= e($event['recurrence_pattern'] ?? 'Recurring') ?></span></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Registrations Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Registrations</h5>
                <span class="badge bg-info"><?= count($registrations) ?? 0 ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($registrations)): ?>
                <p class="text-muted">No registrations yet.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Guest Name</th>
                                <th>Guests Count</th>
                                <th>Registered At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registrations as $registration): ?>
                            <tr>
                                <td>
                                    <?php if ($registration['member_id']): ?>
                                        <?= e($registration['first_name'] . ' ' . $registration['last_name']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($registration['guest_name'] ?? '-') ?></td>
                                <td><?= (int)($registration['guests_count'] ?? 1) ?></td>
                                <td><?= formatDateTime($registration['registered_at']) ?></td>
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
        <!-- Quick Actions Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <?php if ($event['registration_required']): ?>
                <form method="post" action="http://localhost/church-system/public/events/register" class="mb-3">
                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <div class="mb-2">
                        <label for="guest_name" class="form-label">Guest Name (optional)</label>
                        <input type="text" class="form-control" id="guest_name" name="guest_name">
                    </div>
                    <div class="mb-2">
                        <label for="guests_count" class="form-label">Number of Guests</label>
                        <input type="number" class="form-control" id="guests_count" name="guests_count" value="1" min="1">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus me-1"></i> Register
                    </button>
                </form>
                <hr>
                <?php endif; ?>
                
                <form method="post" action="http://localhost/church-system/public/events/delete" onsubmit="return confirm('Are you sure you want to delete this event?');">
                    <input type="hidden" name="id" value="<?= $event['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-trash me-1"></i> Delete Event
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
