<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/layout.php';
requireLogin();
if (isAdmin()) { header('Location: ' . APP_URL . '/pages/admin/dashboard.php'); exit; }

$db = db();

// Stats
$stats = $db->query("
    SELECT
        COUNT(*) AS total,
        SUM(status='pending')     AS pending,
        SUM(status='in_progress') AS in_progress,
        SUM(status='resolved')    AS resolved
    FROM reports
")->fetch();

// Recent reports with category info
$reports = $db->query("
    SELECT r.*, u.name AS author, c.name AS cat_name, c.icon AS cat_icon, c.color AS cat_color
    FROM reports r
    JOIN users u ON r.user_id = u.id
    JOIN categories c ON r.category_id = c.id
    ORDER BY r.created_at DESC
    LIMIT 15
")->fetchAll();

pageHeader('Home Dashboard');
?>

<!-- Stats -->
<div class="stats-row" style="margin-top:.25rem">
  <div class="stat-card stat-blue">
    <div class="stat-num"><?= $stats['total'] ?></div>
    <div class="stat-label">Total</div>
  </div>
  <div class="stat-card stat-orange">
    <div class="stat-num"><?= $stats['pending'] ?></div>
    <div class="stat-label">Pending</div>
  </div>
  <div class="stat-card" style="--c:#3b82f6">
    <div class="stat-num" style="color:#3b82f6"><?= $stats['in_progress'] ?></div>
    <div class="stat-label">In Progress</div>
  </div>
  <div class="stat-card stat-green">
    <div class="stat-num"><?= $stats['resolved'] ?></div>
    <div class="stat-label">Resolved</div>
  </div>
</div>

<!-- Recent reports -->
<div class="section-header">
  <h2>Recent Reports</h2>
  <a href="<?= APP_URL ?>/pages/citizen/submit.php">New +</a>
</div>

<div class="reports-list">
<?php if (empty($reports)): ?>
  <div class="empty-state">
    <div class="icon">📋</div>
    <p>No reports yet. Be the first!</p>
    <a href="<?= APP_URL ?>/pages/citizen/submit.php" class="btn btn-primary">Report a problem</a>
  </div>
<?php else: ?>
  <?php foreach ($reports as $r): ?>
  <a class="report-card" href="<?= APP_URL ?>/pages/report.php?id=<?= $r['id'] ?>">
    <div class="report-cat-icon" style="background:<?= htmlspecialchars($r['cat_color']) ?>22">
      <span style="font-size:1.2rem"><?= $r['cat_icon'] ?></span>
    </div>
    <div class="report-info">
      <div class="report-title"><?= htmlspecialchars($r['title']) ?></div>
      <div class="report-meta">
        <span><?= htmlspecialchars($r['cat_name']) ?></span>
        <span>·</span>
        <span><?= htmlspecialchars($r['location'] ?: 'No location') ?></span>
        <span>·</span>
        <span><?= date('M j', strtotime($r['created_at'])) ?></span>
      </div>
    </div>
    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:5px;flex-shrink:0">
      <span class="badge badge-<?= $r['status'] === 'in_progress' ? 'progress' : $r['status'] ?>">
        <?= statusLabel($r['status']) ?>
      </span>
      <span class="report-arrow">›</span>
    </div>
  </a>
  <?php endforeach; ?>
<?php endif; ?>
</div>

<script>const APP_URL='<?= APP_URL ?>';const CSRF_TOKEN='<?= csrfToken() ?>';</script>
<?php pageFooter(); ?>
