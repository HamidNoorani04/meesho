<?php
require __DIR__.'/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === env('ADMIN_USERNAME') && $password === env('ADMIN_PASSWORD')) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: ' . url('admin.php'));
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        .login-box h2 { margin-bottom: 24px; color: #1f2937; text-align: center; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #4b5563; font-weight: 500; }
        .form-group input { width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; transition: border-color 0.3s; }
        .form-group input:focus { outline: none; border-color: #667eea; }
        .error { background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .btn-login { width: 100%; padding: 14px; background: #667eea; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s; }
        .btn-login:hover { background: #5568d3; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #667eea; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔐 Admin Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo e($error); ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
        <div class="back-link">
            <a href="<?php echo url('index.php'); ?>">← Back to Shop</a>
        </div>
    </div>
</body>
</html>
