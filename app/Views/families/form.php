<?php 
$page_title = ($page_title ?? 'Add Family') . ' - ' . SITE_NAME;
$auth = new Auth();
$isEdit = isset($family) && !empty($family['id']);
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $page_title ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/families" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Families
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="http://localhost/church-system/public/families/save">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $family['id'] ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="family_name" class="form-label">Family Name *</label>
                    <input type="text" class="form-control" id="family_name" name="family_name" 
                           value="<?= old('family_name', $family['family_name'] ?? '') ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="head_of_family_id" class="form-label">Head of Family</label>
                    <select class="form-select" id="head_of_family_id" name="head_of_family_id">
                        <option value="">Select Member</option>
                        <?php if (!empty($members)): ?>
                            <?php foreach ($members as $member): ?>
                                <option value="<?= $member['id'] ?>" 
                                        <?= ($family['head_of_family_id'] ?? '') == $member['id'] ? 'selected' : '' ?>>
                                    <?= e($member['first_name'] . ' ' . $member['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="2"><?= old('address', $family['address'] ?? '') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city" 
                           value="<?= old('city', $family['city'] ?? '') ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="state" class="form-label">State/Province</label>
                    <input type="text" class="form-control" id="state" name="state" 
                           value="<?= old('state', $family['state'] ?? '') ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="postal_code" class="form-label">Postal Code</label>
                    <input type="text" class="form-control" id="postal_code" name="postal_code" 
                           value="<?= old('postal_code', $family['postal_code'] ?? '') ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" 
                       value="<?= old('phone', $family['phone'] ?? '') ?>">
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> <?= $isEdit ? 'Update Family' : 'Create Family' ?>
                </button>
                <a href="http://localhost/church-system/public/families" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
$scripts = '';
require APP_PATH . '/Views/layouts/main.php';
?>
