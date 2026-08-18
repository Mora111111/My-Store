<?php
class AdminOrderController {
    public function __construct() {
        if (Session::get('user_role') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    public function index(): void {
        $orderModel = new Order();
        $orders = $orderModel->getAll();
        $showSearch = true;
        $toast_msg = $_SESSION['toast_msg'] ?? '';
        $toast_type = $_SESSION['toast_type'] ?? '';
        unset($_SESSION['toast_msg'], $_SESSION['toast_type']);
        $pageIcon = 'fa-cart-shopping';
        $pageTitle = 'طلبات الشراء';
        require_once APP_DIR . '/Views/admin/layout_start.php';
        require_once APP_DIR . '/Views/admin/orders/index.php';
        require_once APP_DIR . '/Views/admin/layout_end.php';
    }

    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderModel = new Order();
            $id = intval($_POST['order_id'] ?? 0);
            $status = $_POST['new_status'] ?? '';
            if ($orderModel->updateStatus($id, $status)) {
                $_SESSION['toast_msg'] = 'تم تحديث حالة الطلب بنجاح.';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_msg'] = 'حدث خطأ أثناء تحديث حالة الطلب.';
                $_SESSION['toast_type'] = 'error';
            }
        }
        header('Location: /admin/orders');
        exit;
    }

    public function delete(): void {
        $orderModel = new Order();
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $orderModel->delete($id);
            $_SESSION['toast_msg'] = 'تم حذف الطلب بنجاح.';
            $_SESSION['toast_type'] = 'success';
        }
        header('Location: /admin/orders');
        exit;
    }
}
