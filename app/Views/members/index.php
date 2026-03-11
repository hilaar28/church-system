<?php 
$page_title = 'Members - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Members</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/members/add" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Add Member
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="http://localhost/church-system/public/members" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search members..." value="<?= e($search ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <?php foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>" <?= ($status ?? '') == $s ? 'selected' : '' ?>>
                        <?= ucfirst($s) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="http://localhost/church-system/public/members" class="btn btn-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Members Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($members)): ?>
        <p class="text-muted">No members found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Member Since</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                    <tr>
                        <td>
                            <a href="http://localhost/church-system/public/members/view?id=<?= $member['id'] ?>">
                                <?= e($member['first_name'] . ' ' . $member['last_name']) ?>
                            </a>
                        </td>
                        <td><?= e($member['email'] ?? '-') ?></td>
                        <td><?= e($member['phone'] ?? '-') ?></td>
                        <td>
                            <span class="badge bg-<?= getStatusClass($member['membership_status']) ?>">
                                <?= ucfirst($member['membership_status']) ?>
                            </span>
                        </td>
                        <td><?= formatDate($member['membership_date']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="http://localhost/church-system/public/members/view?id=<?= $member['id'] ?>" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="http://localhost/church-system/public/members/edit?id=<?= $member['id'] ?>" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Delete" 
                                        onclick="confirmDelete(<?= $member['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($pagination['has_prev']): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $pagination['prev'] ?>">Previous</a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?= $pagination['current'] == $i ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($pagination['has_next']): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $pagination['next'] ?>">Next</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this member? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="http://localhost/church-system/public/members/delete">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="id" id="deleteMemberId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
$scripts = '
<script>
function confirmDelete(id) {
    document.getElementById("deleteMemberId").value = id;
    var deleteModal = new bootstrap.Modal(document.getElementById("deleteModal"));
    deleteModal.show();
}
</script>
';
require APP_PATH . '/Views/layouts/main.php';
?>
