<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
        }
        .sidebar a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            border-radius: 4px;
            margin: 2px 0;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            background: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card {
            border-left: 4px solid;
        }
        .stat-card.primary { border-color: #3498db; }
        .stat-card.success { border-color: #2ecc71; }
        .stat-card.warning { border-color: #f39c12; }
        .stat-card.danger { border-color: #e74c3c; }
    </style>
</head>
<body>
    <?php if (isset($hideLayout) && $hideLayout): ?>
        <?= $content ?? '' ?>
    <?php else: ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php if ($auth->isLoggedIn()): ?>
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse show">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5><?= SITE_NAME ?></h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/dashboard" class="<?= strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? 'active' : '' ?>">
                                <i class="fas fa-home me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/members" class="<?= strpos($_SERVER['REQUEST_URI'], 'members') !== false ? 'active' : '' ?>">
                                <i class="fas fa-users me-2"></i> Members
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/families" class="<?= strpos($_SERVER['REQUEST_URI'], 'families') !== false ? 'active' : '' ?>">
                                <i class="fas fa-house-user me-2"></i> Families
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/groups" class="<?= strpos($_SERVER['REQUEST_URI'], 'groups') !== false ? 'active' : '' ?>">
                                <i class="fas fa-users-rectangle me-2"></i> Groups
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/attendance" class="<?= strpos($_SERVER['REQUEST_URI'], 'attendance') !== false ? 'active' : '' ?>">
                                <i class="fas fa-calendar-check me-2"></i> Attendance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/donations" class="<?= strpos($_SERVER['REQUEST_URI'], 'donations') !== false ? 'active' : '' ?>">
                                <i class="fas fa-hand-holding-usd me-2"></i> Donations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/expenses" class="<?= strpos($_SERVER['REQUEST_URI'], 'expenses') !== false ? 'active' : '' ?>">
                                <i class="fas fa-file-invoice-dollar me-2"></i> Expenses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/finance" class="<?= strpos($_SERVER['REQUEST_URI'], 'finance') !== false ? 'active' : '' ?>">
                                <i class="fas fa-chart-line me-2"></i> Finance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/events" class="<?= strpos($_SERVER['REQUEST_URI'], 'events') !== false ? 'active' : '' ?>">
                                <i class="fas fa-calendar-alt me-2"></i> Events
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/volunteers" class="<?= strpos($_SERVER['REQUEST_URI'], 'volunteers') !== false ? 'active' : '' ?>">
                                <i class="fas fa-hands-helping me-2"></i> Volunteers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/communications" class="<?= strpos($_SERVER['REQUEST_URI'], 'communications') !== false ? 'active' : '' ?>">
                                <i class="fas fa-envelope me-2"></i> Communications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/reports" class="<?= strpos($_SERVER['REQUEST_URI'], 'reports') !== false ? 'active' : '' ?>">
                                <i class="fas fa-chart-bar me-2"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/settings" class="<?= strpos($_SERVER['REQUEST_URI'], 'settings') !== false ? 'active' : '' ?>">
                                <i class="fas fa-cog me-2"></i> Settings
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="http://localhost/church-system/public/logout">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <?php endif; ?>
            
            <!-- Main content -->
            <main class="<?= $auth->isLoggedIn() ? 'col-md-9 ms-sm-auto col-lg-10' : 'col-12' ?> px-md-4 main-content">
                <!-- Flash messages -->
                <?php $successMsg = flash('success'); ?>
                <?php if ($successMsg): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $successMsg ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php $errorMsg = flash('error'); ?>
                <?php if ($errorMsg): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $errorMsg ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php $warningMsg = flash('warning'); ?>
                <?php if ($warningMsg): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= $warningMsg ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Page content -->
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <?= $scripts ?? '' ?>
</body>
</html>
