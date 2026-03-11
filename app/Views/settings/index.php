<?php 
$page_title = 'Settings - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Settings</h1>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">General Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/church-system/public/settings/save">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Site Name</label>
                            <input type="text" class="form-control" name="site_name" value="<?= e($settings['site_name'] ?? 'Church Management System') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Currency Symbol</label>
                            <input type="text" class="form-control" name="currency_symbol" value="<?= e($settings['currency_symbol'] ?? '$') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Currency Code</label>
                            <input type="text" class="form-control" name="currency_code" value="<?= e($settings['currency_code'] ?? 'USD') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Format</label>
                            <input type="text" class="form-control" name="date_format" value="<?= e($settings['date_format'] ?? 'Y-m-d') ?>">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5>Church Information</h5>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="church_address" rows="2"><?= e($settings['church_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="church_phone" value="<?= e($settings['church_phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="church_email" value="<?= e($settings['church_email'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User Management</h5>
                <a href="/church-system/public/settings/users" class="btn btn-sm btn-primary">Manage Users</a>
            </div>
            <div class="card-body">
                <p>Manage system users, roles, and permissions.</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Activity Log</h5>
            </div>
            <div class="card-body">
                <a href="/church-system/public/settings/activity" class="btn btn-secondary">View Activity Log</a>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
