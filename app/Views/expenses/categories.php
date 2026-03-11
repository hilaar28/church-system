<?php 
$page_title = 'Expense Categories - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Expense Categories</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/expenses" class="btn btn-sm btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> Back to Expenses
        </a>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="fas fa-plus me-1"></i> Add Category
        </button>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($categories)): ?>
        <p class="text-muted">No expense categories found. Click "Add Category" to create one.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= e($cat->name) ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $cat->category_type)) ?></td>
                        <td><?= e($cat->description ?? '-') ?></td>
                        <td>
                            <?php if ($cat->is_active): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#categoryModal"
                                        data-id="<?= $cat->id ?>"
                                        data-name="<?= e($cat->name) ?>"
                                        data-description="<?= e($cat->description ?? '') ?>"
                                        data-category_type="<?= e($cat->category_type) ?>"
                                        data-is_active="<?= $cat->is_active ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($cat->is_active): ?>
                                <form method="POST" action="http://localhost/church-system/public/expenses/save-category" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                    <input type="hidden" name="id" value="<?= $cat->id ?>">
                                    <input type="hidden" name="name" value="<?= e($cat->name) ?>">
                                    <input type="hidden" name="description" value="<?= e($cat->description ?? '') ?>">
                                    <input type="hidden" name="category_type" value="<?= e($cat->category_type) ?>">
                                    <input type="hidden" name="is_active" value="0">
                                    <button type="submit" class="btn btn-outline-warning" title="Deactivate">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <form method="POST" action="http://localhost/church-system/public/expenses/save-category" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                    <input type="hidden" name="id" value="<?= $cat->id ?>">
                                    <input type="hidden" name="name" value="<?= e($cat->name) ?>">
                                    <input type="hidden" name="description" value="<?= e($cat->description ?? '') ?>">
                                    <input type="hidden" name="category_type" value="<?= e($cat->category_type) ?>">
                                    <input type="hidden" name="is_active" value="1">
                                    <button type="submit" class="btn btn-outline-success" title="Activate">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
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

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="http://localhost/church-system/public/expenses/save-category">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="id" id="category_id" value="">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_type" class="form-label">Category Type *</label>
                        <select class="form-select" id="category_type" name="category_type" required>
                            <option value="">Select Type</option>
                            <option value="pastor_expenses">Pastor Expenses</option>
                            <option value="rentals">Rentals</option>
                            <option value="rates">Rates</option>
                            <option value="improvements">Improvements</option>
                            <option value="levies">Levies</option>
                            <option value="utilities">Utilities</option>
                            <option value="supplies">Supplies</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle modal for editing
    const categoryModal = document.getElementById('categoryModal');
    categoryModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const description = button.getAttribute('data-description');
        const category_type = button.getAttribute('data-category_type');
        
        const modalTitle = categoryModal.querySelector('.modal-title');
        const idInput = categoryModal.querySelector('#category_id');
        const nameInput = categoryModal.querySelector('#name');
        const descriptionInput = categoryModal.querySelector('#description');
        const typeInput = categoryModal.querySelector('#category_type');
        
        if (id) {
            modalTitle.textContent = 'Edit Category';
            idInput.value = id;
            nameInput.value = name;
            descriptionInput.value = description;
            typeInput.value = category_type;
        } else {
            modalTitle.textContent = 'Add Category';
            idInput.value = '';
            nameInput.value = '';
            descriptionInput.value = '';
            typeInput.value = '';
        }
    });
});
</script>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
