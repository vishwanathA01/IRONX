<?php
// Resolve the app URL from its actual filesystem location so the project works
// both when PHP serves the IRONX folder directly and when it is inside a parent folder.
$projectRoot = realpath(dirname(__DIR__));
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
$basePath = '';
if ($projectRoot && $documentRoot && str_starts_with($projectRoot, $documentRoot)) {
    $basePath = substr(str_replace('\\', '/', $projectRoot), strlen(str_replace('\\', '/', $documentRoot)));
}
define('APP_NAME', 'IRONX Fitness');
define('BASE_URL', rtrim($basePath, '/'));
define('CURRENCY', 'INR');
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
