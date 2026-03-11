<?php 
$page_title = ($page_title ?? 'Record Attendance') . ' - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $page_title ?></h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Select Service</h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcomingServices)): ?>
                <p class="text-muted">No upcoming services found.</p>
                <?php else: ?>
                <div class="list-group">
                    <?php foreach ($upcomingServices as $service): ?>
                    <a href="http://localhost/church-system/public/attendance/record?service_id=<?= $service['id'] ?>" 
                       class="list-group-item list-group-item-action <?= (isset($service['id']) && $service['id'] == ($service['id'] ?? 0)) ? 'active' : '' ?>">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1"><?= e($service['service_name']) ?></h6>
                            <small><?= formatDate($service['service_date']) ?></small>
                        </div>
                        <small><?= e($service['service_type']) ?></small>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <?php if (isset($service) && $service): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?= e($service['service_name']) ?> - <?= formatDate($service['service_date']) ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="http://localhost/church-system/public/attendance/save">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                    
                    <?php if (empty($members)): ?>
                    <p class="text-muted">No members found to record attendance.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Visitor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?= e($member['first_name'] . ' ' . $member['last_name']) ?></td>
                                    <td>
                                        <input type="radio" name="status[<?= $member['id'] ?>]" value="present" 
                                               <?= (isset($attendance[$member['id']]['status']) && $attendance[$member['id']]['status'] == 'present') ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <input type="radio" name="status[<?= $member['id'] ?>]" value="absent" 
                                               <?= (isset($attendance[$member['id']]['status']) && $attendance[$member['id']]['status'] == 'absent') ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <input type="radio" name="status[<?= $member['id'] ?>]" value="visitor" 
                                               <?= (isset($attendance[$member['id']]['status']) && $attendance[$member['id']]['status'] == 'visitor') ? 'checked' : '' ?>>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="http://localhost/church-system/public/attendance" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Attendance</button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Please select a service from the left to record attendance.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
$scripts = '';
require APP_PATH . '/Views/layouts/main.php';
?>
