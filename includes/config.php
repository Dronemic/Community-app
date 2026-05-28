<?php
// ============================================================
// includes/config.php
// ============================================================

define('DB_HOST',     'localhost');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_NAME',     'cityfix');
define('APP_NAME',    'CityFix');
define('APP_URL',     'http://localhost/community-app');
define('UPLOAD_DIR',  __DIR__ . '/../public/uploads/');
define('UPLOAD_URL',  APP_URL . '/public/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die('<p style="font-family:sans-serif;color:red;padding:2rem">DB Error: '.htmlspecialchars($e->getMessage()).'</p>');
        }
    }
    return $pdo;
}

if (session_status() === PHP_SESSION_NONE) session_start();

function isLoggedIn(): bool   { return isset($_SESSION['user_id']); }
function isAdmin(): bool       { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }
function currentUserId(): ?int { return $_SESSION['user_id'] ?? null; }
function currentUser(): array  { return $_SESSION['user'] ?? []; }

function requireLogin(): void {
    if (!isLoggedIn()) { header('Location: '.APP_URL.'/pages/login.php'); exit; }
}
function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) { header('Location: '.APP_URL.'/index.php'); exit; }
}

function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}

function csrfToken(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verifyCsrf(): void {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? ''))
        { http_response_code(403); die('Invalid CSRF token.'); }
}

// Create a notification for a user
function createNotification(int $userId, string $type, string $title, string $message, ?int $reportId = null): void {
    $db = db();
    $db->prepare("INSERT INTO notifications (user_id, report_id, type, title, message) VALUES (?,?,?,?,?)")
       ->execute([$userId, $reportId, $type, $title, $message]);
}

// Count unread notifications
function unreadNotifCount(): int {
    if (!isLoggedIn()) return 0;
    $s = db()->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
    $s->execute([currentUserId()]);
    return (int)$s->fetchColumn();
}

function statusBadgeClass(string $status): string {
    return match($status) {
        'pending'     => 'badge-pending',
        'in_progress' => 'badge-progress',
        'resolved'    => 'badge-resolved',
        'rejected'    => 'badge-rejected',
        default       => 'badge-pending',
    };
}
function statusLabel(string $status): string {
    return match($status) {
        'pending'     => 'Pending',
        'in_progress' => 'In Progress',
        'resolved'    => 'Resolved',
        'rejected'    => 'Rejected',
        default       => 'Pending',
    };
}
