<?php 
$page_title = 'Reports - ' . SITE_NAME;
$auth = new Auth();
?>

<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Reports</h1>
</div>

<div class="row">
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                <h5 class="card-title">Members Report</h5>
                <p class="card-text text-muted">View member statistics by status, type, gender, and more.</p>
                <a href="http://localhost/church-system/public/reports/members" class="btn btn-primary">View Report</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-calendar-check fa-3x mb-3 text-success"></i>
                <h5 class="card-title">Attendance Report</h5>
                <p class="card-text text-muted">Track service attendance and visitor statistics.</p>
                <a href="http://localhost/church-system/public/reports/attendance" class="btn btn-success">View Report</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-hand-holding-usd fa-3x mb-3 text-info"></i>
                <h5 class="card-title">Donations Report</h5>
                <p class="card-text text-muted">Analyze donation trends and top donors.</p>
                <a href="http://localhost/church-system/public/reports/donations" class="btn btn-info">View Report</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-warning"></i>
                <h5 class="card-title">Expenses Report</h5>
                <p class="card-text text-muted">Review expense categories and monthly spending.</p>
                <a href="http://localhost/church-system/public/reports/expenses" class="btn btn-warning">View Report</a>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/main.php';
?>
