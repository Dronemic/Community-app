<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/layout.php';
if (isLoggedIn()) { header('Location: ' . APP_URL . '/index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $db    = db();
    $stmt  = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['user']    = $user;
        setFlash('success', 'Welcome back, ' . $user['name'] . '!');
        header('Location: ' . ($user['role'] === 'admin'
            ? APP_URL . '/pages/admin/dashboard.php'
            : APP_URL . '/index.php'));
        exit;
    }
    $error = 'Invalid email or password.';
}
pageHeader('Login', false, 'auth-page');
?>
<div class="auth-tabs">
  <a class="auth-tab active" href="<?= APP_URL ?>/pages/login.php">Login</a>
  <a class="auth-tab" href="<?= APP_URL ?>/pages/register.php">Sign Up</a>
</div>
<div class="auth-body">
  <h2>Welcome back</h2>
  <p class="sub">Log in to your CityFix account</p>

  <?php if ($error): ?>
    <div class="flash flash-error"><?= htmlspecialchars($error) ?><button onclick="this.parentElement.remove()">×</button></div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <div class="form-group">
      <label>Email</label>
      <input class="form-control" name="email" type="email" placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
    </div>
    <div class="form-group">
      <label>Password</label>
      <input class="form-control" name="password" type="password" placeholder="••••••••" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block" style="margin-top:.5rem">Login</button>
  </form>

  <div style="text-align:center;margin-top:1.25rem">
    <a href="<?= APP_URL ?>/pages/register.php" class="btn btn-outline btn-block">Sign Up</a>
  </div>

  <p style="text-align:center;margin-top:1.5rem;font-size:.78rem;color:#9ca3af">
    Admin: admin@cityfix.com / Admin@1234
  </p>
</div>
<?php pageFooter(false); ?>
