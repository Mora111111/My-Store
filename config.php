<?php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'e-website');
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY'));
define('SMTP_USER', getenv('SMTP_USER') ?: 'amr.mansour.mohamed1@gmail.com');
define('SMTP_PASS', getenv('SMTP_PASS') ?: 'faqutwlhlpqqhbps');
define('SUPER_ADMIN_EMAIL', 'm24958604@gmail.com');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$scriptDir = rtrim($scriptDir, '/');
define('BASE_URL', $protocol . "://" . $_SERVER['HTTP_HOST'] . $scriptDir . "/");