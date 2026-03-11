<?php 
$page_title = ($page_title ?? 'Add Group') . ' - ' . SITE_NAME;
$auth = new Auth();
$isEdit = isset($group) && !empty($group['id']);
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $page_title ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/groups" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Groups
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="http://localhost/church-system/public/groups/save">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $group['id'] ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Group Name *</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= old('name', $group['name'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="group_type" class="form-label">Group Type *</label>
                    <select class="form-select" id="group_type" name="group_type" required>
                        <option value="">Select Type</option>
                        <option value="sunday_school" <?= ($group['group_type'] ?? '') == 'sunday_school' ? 'selected' : '' ?>>
                            Sunday School
                        </option>
                        <option value="small_group" <?= ($group['group_type'] ?? '') == 'small_group' ? 'selected' : '' ?>>
                            Small Group
                        </option>
                        <option value="ministry_team" <?= ($group['group_type'] ?? '') == 'ministry_team' ? 'selected' : '' ?>>
                            Ministry Team
                        </option>
                        <option value="cell_group" <?= ($group['group_type'] ?? '') == 'cell_group' ? 'selected' : '' ?>>
                            Cell Group
                        </option>
                        <option value="bible_study" <?= ($group['group_type'] ?? '') == 'bible_study' ? 'selected' : '' ?>>
                            Bible Study
                        </option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $group['description'] ?? '') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="meeting_day" class="form-label">Meeting Day</label>
                    <select class="form-select" id="meeting_day" name="meeting_day">
                        <option value="">Select Day</option>
                        <option value="Sunday" <?= ($group['meeting_day'] ?? '') == 'Sunday' ? 'selected' : '' ?>>Sunday</option>
                        <option value="Monday" <?= ($group['meeting_day'] ?? '') == 'Monday' ? 'selected' : '' ?>>Monday</option>
                        <option value="Tuesday" <?= ($group['meeting_day'] ?? '') == 'Tuesday' ? 'selected' : '' ?>>Tuesday</option>
                        <option value="Wednesday" <?= ($group['meeting_day'] ?? '') == 'Wednesday' ? 'selected' : '' ?>>Wednesday</option>
                        <option value="Thursday" <?= ($group['meeting_day'] ?? '') == 'Thursday' ? 'selected' : '' ?>>Thursday</option>
                        <option value="Friday" <?= ($group['meeting_day'] ?? '') == 'Friday' ? 'selected' : '' ?>>Friday</option>
                        <option value="Saturday" <?= ($group['meeting_day'] ?? '') == 'Saturday' ? 'selected' : '' ?>>Saturday</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="meeting_time" class="form-label">Meeting Time</label>
                    <input type="time" class="form-control" id="meeting_time" name="meeting_time" 
                           value="<?= old('meeting_time', $group['meeting_time'] ?? '') ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="meeting_location" class="form-label">Meeting Location</label>
                    <input type="text" class="form-control" id="meeting_location" name="meeting_location" 
                           value="<?= old('meeting_location', $group['meeting_location'] ?? '') ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="leader_id" class="form-label">Group Leader</label>
                    <select class="form-select" id="leader_id" name="leader_id">
                        <option value="">Select Leader</option>
                        <?php if (!empty($potential_leaders)): ?>
                            <?php foreach ($potential_leaders as $leader): ?>
                                <option value="<?= $leader['id'] ?>" 
                                        <?= ($group['leader_id'] ?? '') == $leader['id'] ? 'selected' : '' ?>>
                                    <?= e($leader['first_name'] . ' ' . $leader['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="capacity" class="form-label">Capacity</label>
                    <input type="number" class="form-control" id="capacity" name="capacity" 
                           value="<?= old('capacity', $group['capacity'] ?? '') ?>" min="1">
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                           value="1" <?= ($group['is_active'] ?? 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_active">
                        Active Group
                    </label>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> <?= $isEdit ? 'Update Group' : 'Create Group' ?>
                </button>
                <a href="http://localhost/church-system/public/groups" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
$scripts = '';
require APP_PATH . '/Views/layouts/main.php';
?>
