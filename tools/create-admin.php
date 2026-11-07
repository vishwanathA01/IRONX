<?php
/**
 * Run only from Terminal: php tools/create-admin.php
 * Creates a new admin or promotes an existing account and securely hashes
 * the password. No web-accessible admin bootstrap endpoint is provided.
 */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/app.php';
if (!function_exists('readline')) { fwrite(STDERR, "PHP readline extension is required.\n"); exit(1); }
$email = strtolower(trim(readline('Admin email: ')));
$name = trim(readline('Admin full name: '));
$password = readline('Admin password (minimum 10 characters): ');
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($name) < 2 || strlen($password) < 10) {
    fwrite(STDERR, "Invalid input. Check email, name and password length.\n"); exit(1);
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$find = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$find->execute([$email]);
$existing = $find->fetchColumn();
if ($existing) {
    $update = $pdo->prepare("UPDATE users SET name = ?, password_hash = ?, role = 'admin', status = 'active' WHERE id = ?");
    $update->execute([$name, $hash, $existing]);
    echo "Existing account promoted and password reset for {$email}.\n";
} else {
    $insert = $pdo->prepare("INSERT INTO users (name,email,password_hash,role,status) VALUES (?,?,?,'admin','active')");
    $insert->execute([$name, $email, $hash]);
    echo "Administrator account created for {$email}.\n";
}
echo "Sign in at /admin/login.php\n";
