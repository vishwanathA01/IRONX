<?php
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function redirect(string $path): never {
    header('Location: ' . (str_starts_with($path, 'http') ? $path : BASE_URL . '/' . ltrim($path, '/')));
    exit;
}
function is_logged_in(): bool { return !empty($_SESSION['user']['id']); }
function current_user(): array { return $_SESSION['user'] ?? []; }
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">'; }
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(419); exit('Invalid or expired form token. Please go back and try again.');
        }
    }
}
function require_login(): void { if (!is_logged_in()) redirect('auth/login.php'); }
function require_role(string ...$roles): void {
    require_login();
    // Re-read the account role/status on protected requests. This ensures a
    // newly promoted admin can access the dashboard without signing in again,
    // and suspended/demoted accounts lose access immediately.
    global $pdo;
    $stmt = $pdo->prepare('SELECT id, name, email, role, status FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int) current_user()['id']]);
    $freshUser = $stmt->fetch();
    if (!$freshUser || $freshUser['status'] !== 'active') {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
        redirect('auth/login.php');
    }
    $_SESSION['user'] = [
        'id' => (int) $freshUser['id'],
        'name' => $freshUser['name'],
        'email' => $freshUser['email'],
        'role' => $freshUser['role'],
    ];
    if (!in_array($freshUser['role'], $roles, true)) {
        http_response_code(403);
        exit('Access denied. Your account does not have permission to view this page.');
    }
}
function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) { $_SESSION['flash'][$key] = $message; return null; }
    $value = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $value;
}
function money($amount): string { return '₹' . number_format((float)$amount, 2); }
function setting(PDO $pdo, string $key, string $default = ''): string {
    $stmt = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
    $stmt->execute([$key]); return $stmt->fetchColumn() ?: $default;
}
