<?php
session_start();
define('ROOT_DIR', __DIR__);
define('APP_DIR', __DIR__ . '/app');
define('CORE_DIR', __DIR__ . '/core');

require_once __DIR__ . '/config.php';

spl_autoload_register(function ($class) {
    $directories = [
        __DIR__ . '/core',
        __DIR__ . '/app/Models',
        __DIR__ . '/app/Controllers'
    ];
    foreach ($directories as $dir) {
        $file = $dir . '/' . $class . '.php';
        if (file_exists($file)) {
            echo "AUTOLOAD OK: $file<br>\n";
            require_once $file;
            return;
        }
    }
    echo "AUTOLOAD FAILED for class: $class (searched: " . implode(', ', $directories) . ")<br>\n";
});

echo "<h2>Diagnostic Report</h2>";
echo "<pre>";

// 1. PHP version
echo "PHP Version: " . phpversion() . "\n";

// 2. File existence checks
$checks = [
    'config.php' => __DIR__ . '/config.php',
    'core/Router.php' => __DIR__ . '/core/Router.php',
    'core/Session.php' => __DIR__ . '/core/Session.php',
    'core/CSRF.php' => __DIR__ . '/core/CSRF.php',
    'core/Database.php' => __DIR__ . '/core/Database.php',
    'app/Controllers/HomeController.php' => __DIR__ . '/app/Controllers/HomeController.php',
    'app/Models/Product.php' => __DIR__ . '/app/Models/Product.php',
    'app/Models/Order.php' => __DIR__ . '/app/Models/Order.php',
    'app/Models/User.php' => __DIR__ . '/app/Models/User.php',
    'public/index.php (LEFTOVER)' => __DIR__ . '/public/index.php',
];

echo "\n--- File Existence ---\n";
foreach ($checks as $label => $path) {
    $exists = file_exists($path) ? "EXISTS" : "MISSING";
    $color = $exists === "EXISTS" ? "" : " ** PROBLEM **";
    echo str_pad($label, 40) . " $exists$color\n";
}

// 3. Test autoloading core classes
echo "\n--- Testing Core Class Autoloading ---\n";
$coreClasses = ['Session', 'Database', 'Router', 'CSRF'];
foreach ($coreClasses as $class) {
    try {
        $exists = class_exists($class, true);
        echo str_pad($class, 20) . " " . ($exists ? "LOADED OK" : "NOT FOUND") . "\n";
    } catch (Throwable $e) {
        echo str_pad($class, 20) . " ERROR: " . $e->getMessage() . "\n";
    }
}

// 4. Test controller autoloading
echo "\n--- Testing Controller Class Autoloading ---\n";
$controllerClasses = ['HomeController', 'ProductController', 'AuthController', 'AdminController', 'CheckoutController', 'AiController', 'ServiceController', 'UserProfileController', 'PageController'];
foreach ($controllerClasses as $class) {
    try {
        $exists = class_exists($class, true);
        echo str_pad($class, 25) . " " . ($exists ? "LOADED OK" : "NOT FOUND") . "\n";
    } catch (Throwable $e) {
        echo str_pad($class, 25) . " ERROR: " . $e->getMessage() . "\n";
    }
}

// 5. Test model autoloading
echo "\n--- Testing Model Class Autoloading ---\n";
$modelClasses = ['Product', 'Order', 'User', 'Comment', 'Message', 'Setting'];
foreach ($modelClasses as $class) {
    try {
        $exists = class_exists($class, true);
        echo str_pad($class, 25) . " " . ($exists ? "LOADED OK" : "NOT FOUND") . "\n";
    } catch (Throwable $e) {
        echo str_pad($class, 25) . " ERROR: " . $e->getMessage() . "\n";
    }
}

// 6. Verify Router dispatches correctly
echo "\n--- Testing Router Instantiation ---\n";
try {
    $router = new Router();
    echo "Router instantiated: OK\n";

    echo "\n--- Testing CSRF Class ---\n";
    $token = CSRF::generate();
    echo "CSRF::generate() returned token: " . substr($token, 0, 16) . "...\n";
    echo "CSRF::validate(token): " . (CSRF::validate($token) ? "VALID" : "INVALID") . "\n";

    echo "\n--- Testing HomeController Method ---\n";
    $hc = new HomeController();
    echo "HomeController instantiated: OK\n";
    echo "HomeController has method index: " . (method_exists($hc, 'index') ? "YES" : "NO") . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "In file: " . $e->getFile() . " on line " . $e->getLine() . "\n";
}

// 7. Config check
echo "\n--- Database Config ---\n";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : "UNDEFINED") . "\n";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : "UNDEFINED") . "\n";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : "UNDEFINED") . "\n";

echo "\n--- Path Constants ---\n";
echo "ROOT_DIR: " . ROOT_DIR . "\n";
echo "APP_DIR: " . APP_DIR . "\n";
echo "CORE_DIR: " . CORE_DIR . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";

echo "\n--- Loaded Classes ---\n";
$loaded = get_declared_classes();
$ourClasses = array_filter($loaded, function($c) {
    return preg_match('/^(Home|Product|Order|User|Auth|Admin|Checkout|Page|Service|Ai|Comment|Message|Setting|Router|Session|Database|CSRF)/', $c);
});
echo implode("\n", $ourClasses) . "\n";

echo "</pre>";
