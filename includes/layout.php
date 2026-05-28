<?php
// ============================================================
// includes/layout.php
// ============================================================

function pageHeader(string $title = '', bool $showNav = true, string $pageClass = ''): void {
    $flash    = getFlash();
    $appName  = APP_NAME;
    $loggedIn = isLoggedIn();
    $admin    = isAdmin();
    $user     = currentUser();
    $notifs   = unreadNotifCount();
    $fullTitle = $title ? "$title — $appName" : $appName;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($fullTitle) ?></title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="<?= $pageClass ?>">

<?php if ($showNav && $loggedIn): ?>
<header class="topbar">
  <div class="topbar-inner">
    <a class="topbar-brand" href="<?= APP_URL ?>/index.php">
      <span class="brand-logo">C</span><?= $appName ?>
    </a>
    <div class="topbar-right">
      <?php if (!$admin): ?>
      <a href="<?= APP_URL ?>/pages/citizen/notifications.php" class="icon-btn <?= $notifs > 0 ? 'has-badge' : '' ?>" data-badge="<?= $notifs ?>">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </a>
      <?php endif; ?>
      <a href="<?= APP_URL ?>/pages/<?= $admin ? 'admin/dashboard.php' : 'citizen/profile.php' ?>" class="avatar-btn">
        <?php if (!empty($user['avatar'])): ?>
          <img src="<?= UPLOAD_URL . htmlspecialchars($user['avatar']) ?>" alt="">
        <?php else: ?>
          <span><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></span>
        <?php endif; ?>
      </a>
    </div>
  </div>
</header>
<?php endif; ?>

<main class="main-wrap <?= $pageClass ?>">
<?php if ($flash): ?>
<div class="flash flash-<?= $flash['type'] ?>">
  <?= htmlspecialchars($flash['msg']) ?>
  <button onclick="this.parentElement.remove()">×</button>
</div>
<?php endif; ?>
<?php
}

function pageFooter(bool $showBottomNav = true): void {
    $loggedIn = isLoggedIn();
    $admin    = isAdmin();
    $current  = basename($_SERVER['PHP_SELF']);
    $dir      = basename(dirname($_SERVER['PHP_SELF']));
?>
</main>

<?php if ($showBottomNav && $loggedIn && !$admin): ?>
<nav class="bottom-nav">
  <a href="<?= APP_URL ?>/index.php" class="bnav-item <?= ($current === 'index.php') ? 'active' : '' ?>">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
    <span>Home</span>
  </a>
  <a href="<?= APP_URL ?>/pages/map.php" class="bnav-item <?= ($current === 'map.php') ? 'active' : '' ?>">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
    <span>Map</span>
  </a>
  <a href="<?= APP_URL ?>/pages/citizen/submit.php" class="bnav-item bnav-report">
    <span class="report-fab">+</span>
  </a>
  <a href="<?= APP_URL ?>/pages/citizen/my-reports.php" class="bnav-item <?= ($current === 'my-reports.php') ? 'active' : '' ?>">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    <span>My Reports</span>
  </a>
  <a href="<?= APP_URL ?>/pages/citizen/profile.php" class="bnav-item <?= ($current === 'profile.php') ? 'active' : '' ?>">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    <span>Profile</span>
  </a>
</nav>
<?php endif; ?>

<script>
const APP_URL    = '<?= APP_URL ?>';
const CSRF_TOKEN = '<?= csrfToken() ?>';
</script>
<script src="<?= APP_URL ?>/public/js/app.js"></script>
</body>
</html>
<?php
}
