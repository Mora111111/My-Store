<?php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'e-website');
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'AIzaSyBOQ7Ytdg4ruRpHA7cN37kttrpuIs9kvCo');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
define('BASE_URL', $protocol . "://" . $_SERVER['HTTP_HOST'] . "/");