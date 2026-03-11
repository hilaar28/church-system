<?php 
$page_title = ($page_title ?? 'Add Member') . ' - ' . SITE_NAME;
$auth = new Auth();
$isEdit = isset($member) && !empty($member['id']);
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $page_title ?></h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="http://localhost/church-system/public/members/save">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= $member['id'] ?>">
            <?php endif; ?>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" 
                           value="<?= old('first_name', $member['first_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name *</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" 
                           value="<?= old('last_name', $member['last_name'] ?? '') ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= old('email', $member['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" 
                           value="<?= old('phone', $member['phone'] ?? '') ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                           value="<?= old('date_of_birth', $member['date_of_birth'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="">Select...</option>
                        <option value="male" <?= ($member['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= ($member['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= ($member['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="marital_status" class="form-label">Marital Status</label>
                    <select class="form-select" id="marital_status" name="marital_status">
                        <option value="">Select...</option>
                        <option value="single" <?= ($member['marital_status'] ?? '') == 'single' ? 'selected' : '' ?>>Single</option>
                        <option value="married" <?= ($member['marital_status'] ?? '') == 'married' ? 'selected' : '' ?>>Married</option>
                        <option value="divorced" <?= ($member['marital_status'] ?? '') == 'divorced' ? 'selected' : '' ?>>Divorced</option>
                        <option value="widowed" <?= ($member['marital_status'] ?? '') == 'widowed' ? 'selected' : '' ?>>Widowed</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="2"><?= old('address', $member['address'] ?? '') ?></textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city" 
                           value="<?= old('city', $member['city'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="state" class="form-label">State/Province</label>
                    <input type="text" class="form-control" id="state" name="state" 
                           value="<?= old('state', $member['state'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="postal_code" class="form-label">Postal Code</label>
                    <input type="text" class="form-control" id="postal_code" name="postal_code" 
                           value="<?= old('postal_code', $member['postal_code'] ?? '') ?>">
                </div>
            </div>
            
            <hr>
            <h5 class="mb-3">Membership Information</h5>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="membership_status" class="form-label">Status *</label>
                    <select class="form-select" id="membership_status" name="membership_status" required>
                        <option value="visitor" <?= ($member['membership_status'] ?? '') == 'visitor' ? 'selected' : '' ?>>Visitor</option>
                        <option value="member" <?= ($member['membership_status'] ?? 'member') == 'member' ? 'selected' : '' ?>>Member</option>
                        <option value="inactive" <?= ($member['membership_status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="transferred" <?= ($member['membership_status'] ?? '') == 'transferred' ? 'selected' : '' ?>>Transferred</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="membership_type" class="form-label">Type</label>
                    <select class="form-select" id="membership_type" name="membership_type">
                        <option value="regular" <?= ($member['membership_type'] ?? 'regular') == 'regular' ? 'selected' : '' ?>>Regular</option>
                        <option value="associate" <?= ($member['membership_type'] ?? '') == 'associate' ? 'selected' : '' ?>>Associate</option>
                        <option value="auxiliary" <?= ($member['membership_type'] ?? '') == 'auxiliary' ? 'selected' : '' ?>>Auxiliary</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="membership_date" class="form-label">Member Since</label>
                    <input type="date" class="form-control" id="membership_date" name="membership_date" 
                           value="<?= old('membership_date', $member['membership_date'] ?? date('Y-m-d')) ?>">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="baptized" name="baptized" 
                               <?= ($member['baptized'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="baptized">
                            Baptized
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="baptized_date" class="form-label">Baptized Date</label>
                    <input type="date" class="form-control" id="baptized_date" name="baptized_date" 
                           value="<?= old('baptized_date', $member['baptized_date'] ?? '') ?>">
                </div>
            </div>
            
            <hr>
            <h5 class="mb-3">Emergency Contact</h5>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="emergency_contact_name" class="form-label">Contact Name</label>
                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" 
                           value="<?= old('emergency_contact_name', $member['emergency_contact_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                    <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" 
                           value="<?= old('emergency_contact_phone', $member['emergency_contact_phone'] ?? '') ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $member['notes'] ?? '') ?></textarea>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="http://localhost/church-system/public/members" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Update Member' : 'Add Member' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
$scripts = '';
require APP_PATH . '/Views/layouts/main.php';
?>
