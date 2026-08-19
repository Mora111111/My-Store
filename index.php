<?php
if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] !== 'zoro' || $_SERVER['PHP_AUTH_PW'] !== '654321') {
    header('WWW-Authenticate: Basic realm="Maintenance Mode"');
    header('HTTP/1.0 401 Unauthorized');
    die('<h2 style="text-align:center; margin-top:50px; font-family:sans-serif; direction:rtl;">الموقع تحت الصيانة مؤقتاً. جاري التحديث...</h2>');
}
ob_start();
session_start();

define('ROOT_DIR', __DIR__);
define('APP_DIR', __DIR__ . '/app');
define('CORE_DIR', __DIR__ . '/core');

spl_autoload_register(function ($class) {
    $directories = [
        __DIR__ . '/core',
        __DIR__ . '/app/Models',
        __DIR__ . '/app/Controllers'
    ];

    foreach ($directories as $dir) {
        $file = $dir . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once __DIR__ . '/config.php';

Session::trackOnline();

$router = new Router();

$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/products', 'ProductController@index');
$router->add('GET', '/about', 'PageController@about');
$router->add('GET', '/contact', 'PageController@contact');
$router->add('GET', '/checkout', 'CheckoutController@index');
$router->add('POST', '/checkout/process', 'CheckoutController@process');
$router->add('GET', '/profile', 'UserProfileController@index');
$router->add('POST', '/profile/update', 'UserProfileController@update');
$router->add('GET', '/my-orders', 'UserProfileController@orders');
$router->add('GET', '/my-messages', 'UserProfileController@messages');
$router->add('POST', '/my-messages/send', 'UserProfileController@sendMessage');
$router->add('GET', '/login', 'AuthController@showLogin');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/signup', 'AuthController@showSignup');
$router->add('POST', '/signup', 'AuthController@register');
$router->add('GET', '/logout', 'AuthController@logout');
$router->add('GET', '/admin', 'AdminController@index');
$router->add('GET', '/admin/products', 'AdminProductController@index');
$router->add('POST', '/admin/products/store', 'AdminProductController@store');
$router->add('GET', '/admin/products/edit', 'AdminProductController@edit');
$router->add('POST', '/admin/products/update', 'AdminProductController@update');
$router->add('GET', '/admin/products/delete', 'AdminProductController@delete');
$router->add('GET', '/admin/orders', 'AdminOrderController@index');
$router->add('POST', '/admin/orders/update', 'AdminOrderController@update');
$router->add('GET', '/admin/orders/delete', 'AdminOrderController@delete');
$router->add('GET', '/admin/users', 'AdminUserController@index');
$router->add('POST', '/admin/users/add', 'AdminUserController@add');
$router->add('POST', '/admin/users/update-role', 'AdminUserController@updateRole');
$router->add('POST', '/admin/users/ban', 'AdminUserController@ban');
$router->add('GET', '/admin/users/delete', 'AdminUserController@delete');
$router->add('GET', '/admin/settings', 'AdminSettingController@index');
$router->add('POST', '/admin/settings/update', 'AdminSettingController@update');
$router->add('GET', '/admin/messages', 'AdminMessageController@index');
$router->add('POST', '/admin/messages/reply', 'AdminMessageController@reply');
$router->add('GET', '/admin/messages/delete', 'AdminMessageController@delete'); 
$router->add('GET', '/admin/comments', 'AdminCommentController@index');
$router->add('POST', '/admin/comments/reply', 'AdminCommentController@reply');
$router->add('GET', '/admin/comments/delete', 'AdminCommentController@delete');
$router->add('GET', '/admin/online-visitors', 'AdminController@onlineVisitors');
$router->add('GET', '/product', 'ProductController@show');
$router->add('POST', '/product', 'ProductController@show');
$router->add('POST', '/contact', 'PageController@sendMessage');
$router->add('GET', '/services', 'ServiceController@index');
$router->add('POST', '/ai/comment', 'AiController@handleComment');
$router->add('POST', '/ai/chatbot', 'AiController@handleChatbot');
$router->add('POST', '/ai/generate-product', 'AiController@generateProduct');
$router->add('POST', '/ai/message-reply', 'AiController@generateMessageReply');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);