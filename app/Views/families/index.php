<?php 
$page_title = 'Families - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Families</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/families/add" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Add Family
        </a>
    </div>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="http://localhost/church-system/public/families" class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Search families..." value="<?= e($search ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
            <div class="col-md-2">
                <a href="http://localhost/church-system/public/families" class="btn btn-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Families Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($families)): ?>
        <p class="text-muted">No families found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Family Name</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Members</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($families as $family): ?>
                    <tr>
                        <td>
                            <a href="http://localhost/church-system/public/families/view?id=<?= $family['id'] ?>">
                                <?= e($family['family_name']) ?>
                            </a>
                        </td>
                        <td><?= e($family['phone'] ?? '-') ?></td>
                        <td><?= e($family['city'] ?? '-') ?></td>
                        <td>
                            <?php 
                            $this->db->query("SELECT COUNT(*) as count FROM family_members WHERE family_id = ?", [$family['id']]);
                            echo $this->db->first()['count'] ?? 0;
                            ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="http://localhost/church-system/public/families/view?id=<?= $family['id'] ?>" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="http://localhost/church-system/public/families/edit?id=<?= $family['id'] ?>" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Delete" 
                                        onclick="confirmDelete(<?= $family['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this family?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="http://localhost/church-system/public/families/delete">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="id" id="deleteFamilyId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
$scripts = '<script>
function confirmDelete(id) {
    document.getElementById("deleteFamilyId").value = id;
    var deleteModal = new bootstrap.Modal(document.getElementById("deleteModal"));
    deleteModal.show();
}
</script>';
require APP_PATH . '/Views/layouts/main.php';
?>
