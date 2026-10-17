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
        $productsCount = $productModel->countAll();
        // جلب عدد الأقسام بناءً على الأقسام الفعلية المربوطة بالمنتجات
        $categoriesCount = count($productModel->getCategories());
        
        $ordersCount = (new Order())->countAll();
        $commentsCount = (new Comment())->countAll();
        $messagesCount = (new Message())->countAllContactMessages();
        $onlineUsersCount = Session::getOnlineCount();
        $revenueSummary = (new Order())->getRevenueSummary('2000-01-01 00:00:00', date('Y-m-d H:i:s'));
        $totalRevenue = (float)($revenueSummary['net'] ?? 0);
        $usersCount = (new User())->countAll();
        $activeCouponsCount = 0;
        foreach ((new Coupon())->getAll() as $couponRow) {
            if ((int)($couponRow['status'] ?? 0) === 1) {
                $activeCouponsCount++;
            }
        }
        $pendingOrdersCount = (new Order())->countPending();
        
        // نظام الاسترجاع التلقائي (The Reaper)
        $orderModel = new \App\Models\Order(); 
        $orderModel->reclaimAbandonedOrders(30);
        
        $showSearch = false;
        $pageIcon = 'fa-house';
        $pageTitle = 'لوحة التحكم';
        
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/dashboard.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function onlineVisitors() {
        $db = Database::getInstance()->getConnection();
        $now = time();

        $db->prepare("DELETE FROM online_users WHERE last_activity < ?")->execute([$now - 300]);

        $stmt = $db->query("SELECT * FROM online_users WHERE last_activity >= ? ORDER BY last_activity DESC");
        $onlineUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $unknownLabel = lang('undefined_location');
        $totalOnline = count($onlineUsers);
        $egyptCount = 0;
        $governorateBreakdown = [];
        $countryBreakdown = [];

        foreach ($onlineUsers as $visitor) {
            $code = strtoupper(trim((string)($visitor['country_code'] ?? '')));
            $country = trim((string)($visitor['country'] ?? ''));
            if ($country === '' || $country === 'غير محدد') {
                $country = $unknownLabel;
            }
            $isEgypt = ($code === 'EG') || ($country === 'مصر');
            if ($isEgypt) {
                $egyptCount++;
                $governorate = trim((string)($visitor['region'] ?? ''));
                if ($governorate === '' || $governorate === 'غير محدد') {
                    $governorate = $unknownLabel;
                }
                if (!isset($governorateBreakdown[$governorate])) {
                    $governorateBreakdown[$governorate] = 0;
                }
                $governorateBreakdown[$governorate]++;
            } else {
                if (!isset($countryBreakdown[$country])) {
                    $countryBreakdown[$country] = 0;
                }
                $countryBreakdown[$country]++;
            }
        }

        $foreignCount = $totalOnline - $egyptCount;
        arsort($governorateBreakdown);
        arsort($countryBreakdown);

        $showSearch = false;
        $pageTitle = 'الزوار المتصلين';
        $pageIcon = 'fa-globe';

        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/online_visitors.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function outOfStock(): void {
        $productModel = new Product();
        $outOfStockCount = $productModel->countOutOfStock();
        $outOfStockProducts = $productModel->getOutOfStockProducts(100);

        $showSearch = false;
        $pageIcon = 'fa-triangle-exclamation';
        $pageTitle = lang('out_of_stock_title');

        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/out_of_stock.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }
}