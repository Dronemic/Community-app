<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/layout.php';
if (isLoggedIn()) { header('Location: ' . APP_URL . '/index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if (strlen($name) < 2)                          $errors[] = 'Name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($pass) < 6)                          $errors[] = 'Password must be at least 6 characters.';
    if ($pass !== $pass2)                           $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $db = db();
        $chk = $db->prepare('SELECT id FROM users WHERE email=?');
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $db->prepare('INSERT INTO users (name,email,phone,password_hash,role) VALUES (?,?,?,?,?)')
               ->execute([$name,$email,$phone,$hash,'citizen']);
            setFlash('success','Account created! Please log in.');
            header('Location: ' . APP_URL . '/pages/login.php');
            exit;
        }
    }
}
pageHeader('Sign Up', false, 'auth-page');
?>
<div class="auth-tabs">
  <a class="auth-tab" href="<?= APP_URL ?>/pages/login.php">Login</a>
  <a class="auth-tab active" href="<?= APP_URL ?>/pages/register.php">Sign Up</a>
</div>
<div class="auth-body">
  <h2>Create account</h2>
  <p class="sub">Join CityFix and start reporting issues</p>

  <?php if ($errors): ?>
    <div class="flash flash-error">
      <ul style="margin:0;padding-left:1.2rem"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <div class="form-group">
      <label>Full name</label>
      <input class="form-control" name="name" type="text" placeholder="Jean Dupont" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
    </div>
    <div class="form-group">
      <label>Email</label>
      <input class="form-control" name="email" type="email" placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </div>
    <div class="form-group">
      <label>Phone <span style="color:#9ca3af;font-weight:400">(optional)</span></label>
      <input class="form-control" name="phone" type="tel" placeholder="+237 6XX XXX XXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input class="form-control" name="password" type="password" placeholder="Min. 6 characters" required>
    </div>
    <div class="form-group">
      <label>Confirm password</label>
      <input class="form-control" name="password2" type="password" placeholder="Repeat password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block" style="margin-top:.5rem">Create Account</button>
  </form>
</div>
<?php pageFooter(false); ?>
