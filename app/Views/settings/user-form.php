<?php 
$page_title = 'Edit User - ' . SITE_NAME;
$auth = new Auth();
$isEdit = !empty($user);
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $isEdit ? 'Edit User' : 'Add User' ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/church-system/public/settings/users" class="btn btn-sm btn-outline-secondary">Back to Users</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="/church-system/public/settings/update-user">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="id" value="<?= $user->id ?? '' ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" value="<?= e($user->first_name ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control" name="last_name" value="<?= e($user->last_name ?? '') ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" name="username" value="<?= e($user->username ?? '') ?>" <?= $isEdit ? 'readonly' : '' ?> required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" value="<?= e($user->email ?? '') ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="<?= e($user->phone ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role *</label>
                            <select class="form-select" name="role" required>
                                <option value="admin" <?= ($user->role ?? '') == 'admin' ? 'selected' : '' ?>>Administrator</option>
                                <option value="finance" <?= ($user->role ?? '') == 'finance' ? 'selected' : '' ?>>Finance</option>
                                <option value="secretariat" <?= ($user->role ?? '') == 'secretariat' ? 'selected' : '' ?>>Secretariat</option>
                                <option value="pastor" <?= ($user->role ?? '') == 'pastor' ? 'selected' : '' ?>>Pastor</option>
                                <option value="leader" <?= ($user->role ?? '') == 'leader' ? 'selected' : '' ?>>Leader</option>
                                <option value="member" <?= ($user->role ?? '') == 'member' ? 'selected' : '' ?>>Member</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= $isEdit ? 'New Password (leave blank to keep current)' : 'Password *' ?></label>
                            <input type="password" class="form-control" name="password" <?= $isEdit ? '' : 'required' ?>>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($user->is_active ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    Active Account
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update User' : 'Create User' ?></button>
                    <a href="/church-system/public/settings/users" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
