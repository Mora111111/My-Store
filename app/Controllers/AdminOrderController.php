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
            $newStatus = trim($_POST['new_status'] ?? '');
            $adminMessage = trim($_POST['admin_message'] ?? '');
            
            // جلب بيانات الطلب القديمة قبل التحديث لمعرفة حالته السابقة
            $oldOrder = $orderModel->findById($id);
            $oldStatus = $oldOrder ? strtolower(trim($oldOrder['status'])) : '';
            
            if ($oldOrder && $orderModel->updateStatus($id, $newStatus, $adminMessage)) {
                
                // الكلمات الدلالية لحالات الإلغاء (يمكنك تعديلها حسب المسميات في متجرك)
                $cancelledStatuses = ['cancelled', 'rejected', 'ملغي', 'مرفوض', 'مسترجع'];
                $currentStatusLow = strtolower($newStatus);
                
                // إذا تم تحويل الطلب من حالة "ناجحة" إلى "ملغية" -> إرجاع الكمية للمخزن
                if (in_array($currentStatusLow, $cancelledStatuses) && !in_array($oldStatus, $cancelledStatuses)) {
                    $this->adjustStock($oldOrder['products'], 'restock');
                }
                // إذا تم تحويل الطلب من حالة "ملغية" إلى "ناجحة" (عن طريق الخطأ مثلاً) -> خصم الكمية مجدداً
                else if (!in_array($currentStatusLow, $cancelledStatuses) && in_array($oldStatus, $cancelledStatuses)) {
                    $this->adjustStock($oldOrder['products'], 'deduct');
                }

                $_SESSION['toast_msg'] = 'تم تحديث حالة الطلب والمخزون بنجاح.';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_msg'] = 'حدث خطأ أثناء تحديث حالة الطلب.';
                $_SESSION['toast_type'] = 'error';
            }
        }
        header('Location: /admin/orders');
        exit;
    }

    // دالة مساعدة لضبط المخزون ديناميكياً
    private function adjustStock($productsJson, $operation): void {
        $products = json_decode($productsJson, true);
        if (is_array($products)) {
            $productModel = new Product();
            foreach ($products as $item) {
                $qty = (int)($item['quantity'] ?? 1);
                $id = (int)($item['id'] ?? 0);
                
                if ($id > 0) {
                    if ($operation === 'restock') {
                        $productModel->restock($id, $qty);
                    } else {
                        $productModel->deductStock($id, $qty);
                    }
                }
            }
        }
    }

    public function delete(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
                $_SESSION['toast_msg'] = 'فشل التحقق من أمان الطلب.';
                $_SESSION['toast_type'] = 'error';
                header('Location: /admin/orders');
                exit;
            }
            
            $orderModel = new Order();
            $id = intval($_POST['id'] ?? 0);
            
            if ($id > 0) {
                // خطوة اختيارية: يمكنك استدعاء adjustStock('restock') هنا إذا كنت تريد إرجاع الكمية عند الحذف النهائي للطلب
                $orderModel->delete($id);
                $_SESSION['toast_msg'] = 'تم حذف الطلب بنجاح.';
                $_SESSION['toast_type'] = 'success';
            }
        }
        header('Location: /admin/orders');
        exit;
    }
}