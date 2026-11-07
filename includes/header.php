<?php
require_once __DIR__ . '/../config/app.php';
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | IRONX</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/ironx-mark.png">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=6">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/dashboard.css?v=16">
</head><body>
