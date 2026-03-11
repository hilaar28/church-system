<?php 
$page_title = 'Expenses - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Expenses</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="http://localhost/church-system/public/expenses/add" class="btn btn-sm btn-primary me-2">
            <i class="fas fa-plus me-1"></i> Add Expense
        </a>
        <a href="http://localhost/church-system/public/expenses/categories" class="btn btn-sm btn-secondary">
            <i class="fas fa-tags me-1"></i> Categories
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <h5 class="card-title">Total Expenses</h5>
                <h2><?= formatCurrency($stats->total ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <h5 class="card-title">Pastor Expenses</h5>
                <h2><?= formatCurrency($stats->pastor ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card info">
            <div class="card-body">
                <h5 class="card-title">Rentals</h5>
                <h2><?= formatCurrency($stats->rentals ?? 0) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <h5 class="card-title">Utilities</h5>
                <h2><?= formatCurrency($stats->utilities ?? 0) ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="http://localhost/church-system/public/expenses" class="row g-3">
            <div class="col-md-2">
                <select class="form-select" name="category">
                    <option value="">All Categories</option>
                    <option value="pastor_expenses" <?= ($category ?? '') == 'pastor_expenses' ? 'selected' : '' ?>>Pastor Expenses</option>
                    <option value="rentals" <?= ($category ?? '') == 'rentals' ? 'selected' : '' ?>>Rentals</option>
                    <option value="utilities" <?= ($category ?? '') == 'utilities' ? 'selected' : '' ?>>Utilities</option>
                    <option value="supplies" <?= ($category ?? '') == 'supplies' ? 'selected' : '' ?>>Supplies</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="start_date" value="<?= e($start_date ?? '') ?>">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="end_date" value="<?= e($end_date ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="http://localhost/church-system/public/expenses" class="btn btn-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Expenses Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($expenses)): ?>
        <p class="text-muted">No expenses found.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <td><?= formatDate($expense->expense_date) ?></td>
                        <td><?= e($expense->description ?? '-') ?></td>
                        <td><?= ucfirst(str_replace('_', ' ', $expense->expense_type)) ?></td>
                        <td><?= formatCurrency($expense->amount) ?></td>
                        <td>
                            <?php if ($expense->is_approved): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="http://localhost/church-system/public/expenses/edit?id=<?= $expense->id ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (!$expense->is_approved): ?>
                                <form method="POST" action="http://localhost/church-system/public/expenses/approve" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                    <input type="hidden" name="id" value="<?= $expense->id ?>">
                                    <button type="submit" class="btn btn-outline-success" title="Approve">
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

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
