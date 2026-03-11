<?php 
$page_title = 'User Management - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">User Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/church-system/public/settings" class="btn btn-sm btn-outline-secondary">Back to Settings</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Users</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
            Add New User
        </button>
    </div>
    <div class="card-body">
        <?php if (empty($users)): ?>
            <p class="text-muted">No users found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user->id ?></td>
                            <td><?= e($user->first_name . ' ' . $user->last_name) ?></td>
                            <td><?= e($user->username) ?></td>
                            <td><?= e($user->email) ?></td>
                            <td><span class="badge bg-<?= $user->role == 'admin' ? 'danger' : ($user->role == 'treasurer' ? 'warning' : 'primary') ?>"><?= e(ucfirst($user->role)) ?></span></td>
                            <td><span class="badge bg-<?= $user->is_active ? 'success' : 'secondary' ?>"><?= $user->is_active ? 'Active' : 'Inactive' ?></span></td>
                            <td><?= date('Y-m-d', strtotime($user->created_at)) ?></td>
                            <td>
                                <a href="/church-system/public/settings/edit-user?id=<?= $user->id ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <?php if ($user->id != $auth->user()->id): ?>
                                <form method="POST" action="/church-system/public/settings/delete-user" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                    <input type="hidden" name="id" value="<?= $user->id ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (!empty($pagination)): ?>
            <nav>
                <ul class="pagination">
                    <?php if ($pagination['prev']): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $pagination['prev'] ?>">Previous</a></li>
                    <?php endif; ?>
                    
                    <?php for ($i = $pagination['start']; $i <= $pagination['end']; $i++): ?>
                    <li class="page-item <?= $i == $pagination['current'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['next']): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $pagination['next'] ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/church-system/public/settings/add-user">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">First Name *</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Last Name *</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select class="form-select" name="role" required>
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                            <option value="treasurer">Treasurer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
