<?php
class AdminCouponController {
    public function __construct() {
        if (Session::get('user_role') !== 'admin') { header('Location: /'); exit; }
    }
    public function index(): void {
        $couponModel = new Coupon();
        $productModel = new Product();$coupons = $couponModel->getAll();$products = $productModel->getAll();$showSearch = false;
        $toast_msg =$_SESSION['toast_msg'] ?? '';
        $toast_type =$_SESSION['toast_type'] ?? '';
        unset($_SESSION['toast_msg'],$_SESSION['toast_type']);
        $pageIcon = 'fa-tags';$pageTitle = 'العروض وكوبونات الخصم';
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/coupons/index.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
                $_SESSION['toast_msg'] = 'فشل التحقق الأمني.'; $_SESSION['toast_type'] = 'error';
                header('Location: /admin/coupons'); exit;
            }
            $couponModel = new Coupon();$target_type = $_POST['target_type'] ?? 'all';$data = [
                'code' => strtoupper(trim($_POST['code'] ?? '')),
                'discount_type' => $_POST['discount_type'] ?? 'percentage',
                'discount_value' => floatval($_POST['discount_value'] ?? 0),
                'target_type' => $target_type,
                'target_product_id' => $target_type === 'specific_product' ? intval($_POST['target_product_id'] ?? 0) : 0,                 'show_strikethrough' => isset($_POST['show_strikethrough']) ? 1 : 0
            ];
            try {
                $couponModel->create($data);$_SESSION['toast_msg'] = 'تم إضافة كود الخصم بنجاح!';
                $_SESSION['toast_type'] = 'success';
            } catch (PDOException $e) {$_SESSION['toast_msg'] = 'حدث خطأ. قد يكون كود الخصم مكرراً.';
                $_SESSION['toast_type'] = 'error';
            }
        }
        header('Location: /admin/coupons'); exit;
    }
    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['csrf_token']) && CSRF::validate($_POST['csrf_token'])) {
                (new Coupon())->delete(intval($_POST['id'] ?? 0));
                $_SESSION['toast_msg'] = 'تم حذف الكود بنجاح.'; $_SESSION['toast_type'] = 'success';
            }
        }
        header('Location: /admin/coupons'); exit;
    }
    public function toggle(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['csrf_token']) && CSRF::validate($_POST['csrf_token'])) {
                (new Coupon())->toggleStatus(intval($_POST['id'] ?? 0), intval($_POST['current_status'] ?? 0));
                $_SESSION['toast_msg'] = 'تم تحديث حالة الكود.'; $_SESSION['toast_type'] = 'success';
            }
        }
        header('Location: /admin/coupons'); exit;
    }
}
?>
