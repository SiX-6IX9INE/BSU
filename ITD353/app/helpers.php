<?php
/**
 * app/helpers.php
 * Global helper functions: escape, auth, csrf, flash, redirect, pagination
 */

// -------------------------------------------------------
// Output escaping (XSS prevention)
// -------------------------------------------------------
function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// -------------------------------------------------------
// Authentication helpers
// -------------------------------------------------------
function auth(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function isAdmin(): bool
{
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function loginUser(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'    => $user['id']    ?? null,
        'name'  => $user['name']  ?? ($user['username'] ?? 'ผู้ใช้'),
        'email' => $user['email'] ?? '',
        'role'  => $user['role']  ?? 'user',
    ];
}

function logoutUser(): void
{
    $_SESSION = [];
    session_destroy();
}

// -------------------------------------------------------
// CSRF
// -------------------------------------------------------
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        die('CSRF token mismatch.');
    }
}

// -------------------------------------------------------
// Flash messages
// -------------------------------------------------------
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function getFlash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

// -------------------------------------------------------
// Redirect
// -------------------------------------------------------
function redirect(string $path): never
{
    $url = str_starts_with($path, 'http') ? $path : BASE_URL . $path;
    header('Location: ' . $url);
    exit;
}

// -------------------------------------------------------
// URL helper
// -------------------------------------------------------
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

// -------------------------------------------------------
// Asset URL
// -------------------------------------------------------
function asset(string $path): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

// -------------------------------------------------------
// Upload URL for stored images
// -------------------------------------------------------
function uploadUrl(string $filename): string
{
    // เข้าถึงผ่าน public symlink หรือ controller ส่งไฟล์
    return BASE_URL . '/../storage/uploads/' . $filename;
}

// -------------------------------------------------------
// Pagination helper
// -------------------------------------------------------
function paginate(int $total, int $perPage, int $currentPage, string $baseUrl): array
{
    $totalPages = (int)ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages ?: 1));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'total'        => $total,
        'per_page'     => $perPage,
        'current_page' => $currentPage,
        'total_pages'  => $totalPages,
        'offset'       => $offset,
        'base_url'     => $baseUrl,
    ];
}

// -------------------------------------------------------
// Status badge label / color
// -------------------------------------------------------
function statusLabel(string $status): string
{
    return match($status) {
        'new'         => 'ใหม่',
        'reviewing'   => 'กำลังตรวจสอบ',
        'in_progress' => 'กำลังดำเนินการ',
        'resolved'    => 'แก้ไขแล้ว',
        'rejected'    => 'ปฏิเสธ',
        default       => $status,
    };
}

function statusClass(string $status): string
{
    return match($status) {
        'new'         => 'badge-new',
        'reviewing'   => 'badge-reviewing',
        'in_progress' => 'badge-inprogress',
        'resolved'    => 'badge-resolved',
        'rejected'    => 'badge-rejected',
        default       => 'badge-new',
    };
}

function urgencyLabel(string $urgency): string
{
    return match($urgency) {
        'low'    => 'ต่ำ',
        'medium' => 'ปานกลาง',
        'high'   => 'เร่งด่วน',
        default  => $urgency,
    };
}

function urgencyClass(string $urgency): string
{
    return match($urgency) {
        'low'    => 'urgency-low',
        'medium' => 'urgency-medium',
        'high'   => 'urgency-high',
        default  => 'urgency-medium',
    };
}

// -------------------------------------------------------
// Time ago (ภาษาไทย)
// -------------------------------------------------------
function timeAgo(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60)       return 'เมื่อกี้';
    if ($diff < 3600)     return (int)($diff/60) . ' นาทีที่แล้ว';
    if ($diff < 86400)    return (int)($diff/3600) . ' ชั่วโมงที่แล้ว';
    if ($diff < 2592000)  return (int)($diff/86400) . ' วันที่แล้ว';
    return date('d/m/Y', strtotime($datetime));
}

// -------------------------------------------------------
// IP address (behind proxy-aware)
// -------------------------------------------------------
function clientIp(): string
{
    foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','REMOTE_ADDR'] as $key) {
        $ip = $_SERVER[$key] ?? '';
        if ($ip) {
            $ip = explode(',', $ip)[0];
            return filter_var(trim($ip), FILTER_VALIDATE_IP) ? trim($ip) : '0.0.0.0';
        }
    }
    return '0.0.0.0';
}

// -------------------------------------------------------
// Rate limit check (returns true = OK to proceed)
// -------------------------------------------------------
function checkRateLimit(string $action = 'submit_issue'): bool
{
    $ip     = clientIp();
    $pdo    = db();
    $window = RATE_LIMIT_WINDOW;
    $max    = RATE_LIMIT_MAX;

    $stmt = $pdo->prepare(
        "SELECT id, hit_count, window_start FROM rate_limit
         WHERE ip = ? AND action = ? LIMIT 1"
    );
    $stmt->execute([$ip, $action]);
    $row = $stmt->fetch();

    if (!$row) {
        $pdo->prepare("INSERT INTO rate_limit (ip, action) VALUES (?, ?)")
            ->execute([$ip, $action]);
        return true;
    }

    $elapsed = time() - strtotime($row['window_start']);
    if ($elapsed > $window) {
        // reset window
        $pdo->prepare("UPDATE rate_limit SET hit_count=1, window_start=NOW() WHERE id=?")
            ->execute([$row['id']]);
        return true;
    }

    if ($row['hit_count'] >= $max) {
        return false;  // rate limited
    }

    $pdo->prepare("UPDATE rate_limit SET hit_count=hit_count+1 WHERE id=?")
        ->execute([$row['id']]);
    return true;
}

// -------------------------------------------------------
// Ticket ID generator  ISS-000042
// -------------------------------------------------------
function generateTicketId(int $id): string
{
    return 'ISS-' . str_pad($id, 6, '0', STR_PAD_LEFT);
}

// -------------------------------------------------------
// Render view helper
// -------------------------------------------------------
function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $path = APP_PATH . '/views/' . ltrim($template, '/') . '.php';
    if (!file_exists($path)) {
        http_response_code(404);
        echo '<h1>View not found: ' . e($template) . '</h1>';
        return;
    }
    require $path;
}

// -------------------------------------------------------
// JSON response
// -------------------------------------------------------
function jsonResponse(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// -------------------------------------------------------
// Simple dump (dev only)
// -------------------------------------------------------
function dd(mixed ...$vars): never
{
    if (APP_DEBUG) {
        foreach ($vars as $v) {
            echo '<pre style="background:#1e293b;color:#7dd3fc;padding:1rem;border-radius:8px;overflow:auto">';
            var_dump($v);
            echo '</pre>';
        }
    }
    exit;
}
