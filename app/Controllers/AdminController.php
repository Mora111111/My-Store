<?php
class AdminController {
    public function __construct() {
        if (Session::get('user_role') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index(): void {
        $productModel = new Product();
        $products = $productModel->getAll();
        $productsCount = $productModel->countAll();
        $ordersCount = (new Order())->countAll();
        $commentsCount = (new Comment())->countAll();
        $messagesCount = (new Message())->countAllContactMessages();
        $onlineUsersCount = Session::getOnlineCount();
        $showSearch = false;
        $pageIcon = 'fa-house';
        $pageTitle = 'لوحة التحكم';
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/dashboard.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function onlineVisitors() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM online_users ORDER BY last_activity DESC");
        $onlineUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pageTitle = 'الزوار المتصلين';
        $pageIcon = 'fa-globe';

        require_once APP_DIR . '/Views/admin/online_visitors.php';
    }
}