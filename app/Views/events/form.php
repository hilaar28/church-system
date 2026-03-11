<?php 
$page_title = ($page_title ?? 'Add Event') . ' - ' . SITE_NAME;
$auth = new Auth();
$isEdit = isset($event) && !empty($event['id']);
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $page_title ?></h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="http://localhost/church-system/public/events/save">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $event['id'] ?>">
            <?php endif; ?>
            
            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="title" class="form-label">Event Title *</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?= old('title', $event['title'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="event_type" class="form-label">Event Type</label>
                    <select class="form-select" id="event_type" name="event_type">
                        <option value="service" <?= ($event['event_type'] ?? 'service') == 'service' ? 'selected' : '' ?>>Worship Service</option>
                        <option value="sunday_school" <?= ($event['event_type'] ?? '') == 'sunday_school' ? 'selected' : '' ?>>Sunday School</option>
                        <option value="youth" <?= ($event['event_type'] ?? '') == 'youth' ? 'selected' : '' ?>>Youth Group</option>
                        <option value="women" <?= ($event['event_type'] ?? '') == 'women' ? 'selected' : '' ?>>Women's Ministry</option>
                        <option value="men" <?= ($event['event_type'] ?? '') == 'men' ? 'selected' : '' ?>>Men's Ministry</option>
                        <option value="children" <?= ($event['event_type'] ?? '') == 'children' ? 'selected' : '' ?>>Children's Ministry</option>
                        <option value="outreach" <?= ($event['event_type'] ?? '') == 'outreach' ? 'selected' : '' ?>>Outreach</option>
                        <option value="fellowship" <?= ($event['event_type'] ?? '') == 'fellowship' ? 'selected' : '' ?>>Fellowship</option>
                        <option value="meeting" <?= ($event['event_type'] ?? '') == 'meeting' ? 'selected' : '' ?>>Meeting</option>
                        <option value="other" <?= ($event['event_type'] ?? '') == 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $event['description'] ?? '') ?></textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="start_datetime" class="form-label">Start Date & Time *</label>
                    <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime" 
                           value="<?= old('start_datetime', isset($event['start_datetime']) ? date('Y-m-d\TH:i', strtotime($event['start_datetime'])) : '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="end_datetime" class="form-label">End Date & Time</label>
                    <input type="datetime-local" class="form-control" id="end_datetime" name="end_datetime" 
                           value="<?= old('end_datetime', isset($event['end_datetime']) ? date('Y-m-d\TH:i', strtotime($event['end_datetime'])) : '') ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" 
                           value="<?= old('location', $event['location'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="max_attendees" class="form-label">Maximum Attendees</label>
                    <input type="number" class="form-control" id="max_attendees" name="max_attendees" 
                           value="<?= old('max_attendees', $event['max_attendees'] ?? '') ?>" min="0">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_all_day" name="is_all_day" 
                               <?= ($event['is_all_day'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_all_day">
                            All Day Event
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" 
                               <?= ($event['is_recurring'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_recurring">
                            Recurring Event
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="registration_required" name="registration_required" 
                               <?= ($event['registration_required'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="registration_required">
                            Registration Required
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3" id="recurrence_pattern_row" style="<?= ($event['is_recurring'] ?? 0) ? '' : 'display:none;' ?>">
                <div class="col-md-12">
                    <label for="recurrence_pattern" class="form-label">Recurrence Pattern</label>
                    <select class="form-select" id="recurrence_pattern" name="recurrence_pattern">
                        <option value="">Select Pattern</option>
                        <option value="daily" <?= ($event['recurrence_pattern'] ?? '') == 'daily' ? 'selected' : '' ?>>Daily</option>
                        <option value="weekly" <?= ($event['recurrence_pattern'] ?? '') == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                        <option value="biweekly" <?= ($event['recurrence_pattern'] ?? '') == 'biweekly' ? 'selected' : '' ?>>Bi-Weekly</option>
                        <option value="monthly" <?= ($event['recurrence_pattern'] ?? '') == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3" id="registration_deadline_row" style="<?= ($event['registration_required'] ?? 0) ? '' : 'display:none;' ?>">
                <div class="col-md-12">
                    <label for="registration_deadline" class="form-label">Registration Deadline</label>
                    <input type="date" class="form-control" id="registration_deadline" name="registration_deadline" 
                           value="<?= old('registration_deadline', $event['registration_deadline'] ?? '') ?>">
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="http://localhost/church-system/public/events" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Update Event' : 'Add Event' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isRecurring = document.getElementById('is_recurring');
    const recurrenceRow = document.getElementById('recurrence_pattern_row');
    const registrationRequired = document.getElementById('registration_required');
    const registrationRow = document.getElementById('registration_deadline_row');
    
    isRecurring.addEventListener('change', function() {
        recurrenceRow.style.display = this.checked ? '' : 'none';
    });
    
    registrationRequired.addEventListener('change', function() {
        registrationRow.style.display = this.checked ? '' : 'none';
    });
});
</script>

<?php 
$content = ob_get_clean();
$scripts = '';
require APP_PATH . '/Views/layouts/main.php';
?>
