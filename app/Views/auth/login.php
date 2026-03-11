<?php 
$page_title = 'Login - ' . SITE_NAME;
$hideLayout = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h3><?= SITE_NAME ?></h3>
            <p class="text-muted">Sign in to your account</p>
        </div>
        
        <?php if (flash('error')): ?>
        <div class="alert alert-danger"><?= flash('error') ?></div>
        <?php endif; ?>
        
        <?php if (flash('success')): ?>
        <div class="alert alert-success"><?= flash('success') ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/church-system/public/index.php?route=login">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="http://localhost/church-system/public/forgot-password">Forgot Password?</a>
        </div>
        
        <div class="text-center mt-2">
            <small class="text-muted">Default: admin@church.org / admin123</small>
        </div>
    </div>
</body>
</html>
